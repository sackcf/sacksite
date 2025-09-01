// script2.js

document.addEventListener("DOMContentLoaded", function () {
    fetch("/news")
        .then(response => response.json())
        .then(data => {
            const newsArea = document.getElementById("news-area");
            newsArea.innerHTML = ""; // Clear existing content

            data.forEach(item => {
                // Create a container for each news item
                const newsItem = document.createElement("div");
                newsItem.classList.add("news-item");

                // Build inner HTML (with image support)
                newsItem.innerHTML = `
                    <h2>${item.title}</h2>
                    <p>${item.content}</p>
                    ${item.image ? `<img src="${item.image}" alt="News image" class="news-image">` : ""}
                    <small>${item.date}</small>
                `;

                newsArea.appendChild(newsItem);
            });
        })
        .catch(error => console.error("Error fetching news:", error));
});
