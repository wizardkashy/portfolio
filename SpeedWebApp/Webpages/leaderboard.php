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
    <title>Leaderboard</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";
    ?>
    <div id="main">
        <h1>Leaderboard</h1>
        <ol id="leaderboard">
            <?php
                // simple list ordered by rating.
                try {
                    $sth = $dbh->prepare("SELECT id, username, rating FROM user ORDER BY rating DESC, rd ASC");
                    $sth->execute();
                    $leaders = $sth->fetchAll();
                    foreach ($leaders as $l) {
                        echo "<li class='leader'><a href='profile.php?id=" . $l["id"] . "'>";
                        echo htmlspecialchars($l["username"]) . " - ";
                        echo htmlspecialchars($l["rating"]);
                        echo "</a></li>";
                    }
                }
                catch (PDOException $e) {
                    echo "<p>Error: {$e->getMessage()}</p>";
                }
            ?>
        </ol>
    </div>
  </body>
</html>
