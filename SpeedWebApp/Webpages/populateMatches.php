<?php
  session_start();
  if ($_SESSION["admin"] == 0) {
    header("Location: profile.php");
    exit(401);
  }
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
    <title>Populate the match DB</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";
    ?>
    <div id="main">
      <form action="admin.php?referrer=populate" method="POST">
        <?php
              // this is for admins to spam a bunch of random matches to populate the database instead of playing a ton of games!
              $sth = $dbh->prepare("SELECT * FROM user ORDER BY RAND() LIMIT 2");
              $sth->execute();
              $players=$sth->fetchAll();

              echo "<input type='hidden' name='addmatch' value='on'>";
              echo "<input type='hidden' name='hostUsername' value='" . $players[0]["username"] . "'>";
              echo "<input type='hidden' name='guestUsername' value='" . $players[1]["username"] . "'>";
              $probability = rand();
              $totalrating = $players[0]["rating"] + $players[1]["rating"];
              $matchprob = $players[0]["rating"] / $totalrating;
              // for simplicity, we're taking the probability for a win as the straight division of rating over the total.
              if ($probability > $matchprob) {
                echo "<input type='hidden' name='hostCardsLeft' value='0'>";
                echo "<input type='hidden' name='guestCardsLeft' value='" . rand(1, 20) . "'>";
              }
              elseif ($probability > $matchprob - 0.05) {
                echo "<input type='hidden' name='hostCardsLeft' value='" . rand(1, 20) . "'>";
                echo "<input type='hidden' name='guestCardsLeft' value='" . rand(1, 20) . "'>";
              }
              else {
                echo "<input type='hidden' name='hostCardsLeft' value='" . rand(1, 20) . "'>";
                echo "<input type='hidden' name='guestCardsLeft' value='0'>";
              }




        ?>
        <button type="submit">submit</button>
      </form>
    </div>
  </body>

</html>
