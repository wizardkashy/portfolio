<?php
  session_start();
  require_once "../DBManagement/dbconfig.php";
  try {
      $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
  }
  catch (PDOException $e) {
      echo "<p>Error: {$e->getMessage()}</p>";
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>References</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";
    ?>
    <div id="main">
      <h1>Sources</h1>
      <ul>
        <li><a href="https://www.picfair.com/pics/03231967-black-and-white-playing-cards-as-background">background image</a></li>
        <li><a href="https://github.com/zelenin/glicko2">Glicko2 PHP implementation</a></li>
      </ul>
    </div>
  </body>

</html>
