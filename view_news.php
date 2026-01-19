<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="assets/css/style.css">
<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />   
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width:auto;      /* Allows internal alignment */
      height:auto;
      overflow-wrap:anywhere; 
  word-break: break-all;
    }
    .news-card h3 { margin: 0 0 8px 0; }
    .news-card img { max-width: 100%; overflow-wrap: anywhere; height:auto;
      word-break: break-word;
      overflow: hidden;  margin: 10px 0; border-radius: 6px; }
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

  <div class="py-5 bg-body-tertiary">
  <div class='container'>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
  <?php
  $sql = "SELECT title, content, image_path, created_at FROM news ORDER BY created_at DESC";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<div class='col d-flex'>";
          echo "<div class='news-card'>";
          echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
          if (!empty($row['image_path'])) {
              // show image if available
              echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='news image'>";
          }
          echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
          echo "<small>Posted on: " . htmlspecialchars($row['created_at']) . "</small>";
          echo "</div>";
          echo "</div>";
      }
  } else {
      echo "<p>No news found.</p>";
  }
  ?>
  </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
