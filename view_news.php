<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Latest News</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    .container{
      display:grid;
       grid-template-columns: auto auto;
       padding: 10px;
       
    }
    .news-card {
      background: #fff; padding: 16px; margin-bottom: 16px;
      border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 90%;display: flex;      /* Allows internal alignment */
      flex-direction: column;
      height:100vh;
      overflow-wrap:anywhere; 
  word-break: break-all;
    }
    .news-card h3 { margin: 0 0 8px 0; }
    .news-card img { max-width: 100%; overflow-wrap: anywhere; height:auto;
      word-break: break-word;
      overflow: hidden; display:block; margin: 10px 0; border-radius: 6px; }
    .news-card small { color: #777; word-wrap: break-word;      /* Forces long words to break */
      overflow-wrap: break-word;
      word-break: break-all;
      flex-grow: 1; /* Pushes the 'Posted on' date to the bottom */
  
  /* Extra protection for the text */
  white-space: normal;
  overflow: hidden;      /* Specifically for long strings of random letters */
  ;}
  </style>
</head>
<body>
  <h1><center>Latest News</center></h1>

  <div class='container'>
  <?php
  $sql = "SELECT title, content, image_path, created_at FROM news ORDER BY created_at DESC";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "<div class='news-card'>";
          echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
          if (!empty($row['image_path'])) {
              // show image if available
              echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='news image'>";
          }
          echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
          echo "<small>Posted on: " . htmlspecialchars($row['created_at']) . "</small>";
          echo "</div>";
      }
  } else {
      echo "<p>No news found.</p>";
  }
  ?>
  </div>
</body>
</html>
