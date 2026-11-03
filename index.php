<?php
session_start();
$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>To-Do List</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Frontends will build the page structure here -->
  <script src="js/script.js"></script>
</body>
</html>