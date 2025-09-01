from flask import Flask, jsonify, render_template, request, redirect, url_for, abort
import json
import os
from datetime import datetime

app = Flask(__name__)

@app.route("/")
def home():
    # Page loads and fetches news via JS
    return render_template("news.html")

def _news_json_path() -> str:
    return os.path.join(app.root_path, "news_data", "news.json")


def _load_news() -> list:
    path = _news_json_path()
    if not os.path.exists(path):
        return []
    with open(path, "r", encoding="utf-8") as f:
        try:
            data = json.load(f)
            if not isinstance(data, list):
                return []
            return data
        except json.JSONDecodeError:
            return []


def _save_news(items: list) -> None:
    path = _news_json_path()
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, "w", encoding="utf-8") as f:
        json.dump(items, f, ensure_ascii=False, indent=2)


@app.route("/news", methods=["GET"]) 
def get_news():
    # Return news sorted by date (newest first) when possible
    items = _load_news()
    def parse_date(item):
        d = item.get("date", "")
        for fmt in ("%Y-%m-%d", "%Y/%m/%d", "%d-%m-%Y"):
            try:
                return datetime.strptime(d, fmt)
            except Exception:
                continue
        return datetime.min
    items_sorted = sorted(items, key=parse_date, reverse=True)
    return jsonify(items_sorted)


@app.route("/admin", methods=["GET"]) 
def admin_page():
    # Simple form to add a news item
    return render_template("admin.html")


@app.route("/news", methods=["POST"]) 
def add_news():
    # Accept form or JSON body to append a single news item
    if request.is_json:
        payload = request.get_json(silent=True) or {}
        title = payload.get("title", "").strip()
        content = payload.get("content", "").strip()
        date_str = (payload.get("date") or "").strip()
        image = (payload.get("image") or "").strip()
    else:
        title = request.form.get("title", "").strip()
        content = request.form.get("content", "").strip()
        date_str = request.form.get("date", "").strip()
        image = request.form.get("image", "").strip()

    if not title or not content:
        abort(400, "Missing required fields: title and content")

    # Default date to today if not provided
    if not date_str:
        date_str = datetime.now().strftime("%Y-%m-%d")

    items = _load_news()
    items.append({
        "title": title,
        "content": content,
        "date": date_str,
        "image": image
    })
    _save_news(items)

    # Redirect back to home for form posts; return JSON when posted as JSON
    if request.is_json:
        return jsonify({"status": "ok"})
    return redirect(url_for("home"))

if __name__ == "__main__":
    app.run(debug=True)
