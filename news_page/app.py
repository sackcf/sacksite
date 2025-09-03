from flask import Flask, jsonify, render_template, request, redirect, url_for, abort, session
import json
import os
from datetime import datetime

# Minimal Flask app for a news page with a tiny admin panel.
# Auth is simple: username/password from auth.json (fallback to admin/admin).

app = Flask(__name__)
app.secret_key = os.environ.get("FLASK_SECRET_KEY", "dev-secret-change-me")

@app.route("/")
def home():
    # Page loads and fetches news via JS
    return render_template("news.html")

def _news_json_path() -> str:
    return os.path.join(app.root_path, "news_data", "news.json")


def _auth_json_path() -> str:
    return os.path.join(app.root_path, "news_data", "auth.json")


def _load_auth() -> dict:
    """Return {'username', 'password'} from auth.json or defaults."""
    path = _auth_json_path()
    try:
        if os.path.exists(path):
            with open(path, "r", encoding="utf-8") as f:
                data = json.load(f)
                if isinstance(data, dict) and data.get("username") and data.get("password"):
                    return {"username": str(data["username"]), "password": str(data["password"])}
    except Exception:
        # Fall through to defaults
        pass
    return {"username": "admin", "password": "admin"}


def _login_required():
    return bool(session.get("user"))


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
    # Return news sorted by date (YYYY-MM-DD newest first)
    items = _load_news()
    def parse_date(item):
        d = item.get("date", "")
        try:
            return datetime.strptime(d, "%Y-%m-%d")
        except Exception:
            return datetime.min
    items_sorted = sorted(items, key=parse_date, reverse=True)
    return jsonify(items_sorted)


@app.route("/admin", methods=["GET"]) 
def admin_page():
    # Simple form to add a news item
    if not _login_required():
        return redirect(url_for("login"))
    return render_template("admin.html")


@app.route("/news", methods=["POST"]) 
def add_news():
    # Accept form submission to append a single news item
    if not _login_required():
        abort(401, "Unauthorized")
    title = (request.form.get("title") or "").strip()
    content = (request.form.get("content") or "").strip()
    date_str = (request.form.get("date") or "").strip()
    image = (request.form.get("image") or "").strip()

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

    # Redirect back to home after adding
    return redirect(url_for("home"))


@app.route("/login", methods=["GET", "POST"]) 
def login():
    if request.method == "GET":
        return render_template("login.html")

    # POST: attempt login from simple form
    username = (request.form.get("username") or "").strip()
    password = (request.form.get("password") or "").strip()

    creds = _load_auth()
    if username == creds.get("username") and password == creds.get("password"):
        session["user"] = username
        return redirect(url_for("admin_page"))

    return render_template("login.html", error="Invalid username or password")


@app.route("/logout", methods=["GET"]) 
def logout():
    session.clear()
    return redirect(url_for("home"))

if __name__ == "__main__":
    app.run(debug=True)
