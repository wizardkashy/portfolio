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
    <title>Match</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";
    ?>
    <div id="main">
      <h1>Match Stats</h1>
      <?php

        try {
          // find the match that the user is looking for, or fetch the latest match.
          if (isset($_GET["id"])) {
            $sth = $dbh->prepare("SELECT id, host_id, guest_id, host_elochange, guest_elochange, match_events, winner_id, timestamp, timestamp('timestamp') AS 'match_timestamp' from `match` WHERE id=:bindID");
            $sth->bindValue(":bindID", $_GET["id"]);
          }
          elseif (isset($_SESSION["host_id"])) {
            $sth = $dbh->prepare("SELECT id, host_id, guest_id, host_elochange, guest_elochange, match_events, winner_id, timestamp, timestamp('timestamp') AS 'match_timestamp' FROM `match` WHERE host_id = :hostid ORDER BY 1 DESC");
            $sth->bindValue(":hostid", $_SESSION["host_id"]);
          }
          else {
            $sth = $dbh->prepare("SELECT id, host_id, guest_id, host_elochange, guest_elochange, match_events, winner_id, timestamp, timestamp('timestamp') AS 'match_timestamp' FROM `match` ORDER BY 1 DESC");
          }
          $sth->execute();
          $match = $sth->fetch();
          if ($match == false) {
            echo "<h1>Match Not Found!</h1>";
            exit(406);
          }

          // fetch each player that was part of the match.
          $sth = $dbh->prepare("SELECT * from user WHERE id=:hostid");
          $sth->bindValue(":hostid", $match["host_id"]);
          $sth->execute();
          $host = $sth->fetch();

          $sth = $dbh->prepare("SELECT * from user WHERE id=:guestid");
          $sth->bindValue(":guestid", $match["guest_id"]);
          $sth->execute();
          $guest = $sth->fetch();
          // var_dump($match);
          // var_dump($guest);
        } catch (PDOException $e) {echo "<p>Error: {$e->getMessage()}</p>";}
       ?>
       <div id="matchContainer">
         <?php
            // echo match stat data.

            echo "<p><a href='profile.php?id=" . $host["id"] . "'>" . htmlspecialchars($host["username"]) . "</a> (";
            echo ($match["host_elochange"] > 0) ? "+" . $match["host_elochange"] : $match["host_elochange"];
            echo ")</p>";
            $matchEvents = json_decode($match["match_events"]);
            // var_dump($matchEvents);
            if (isset($matchEvents->hostCardsLeft)) {
              echo "<p>" . htmlspecialchars(count($matchEvents->hostCardsLeft)) . " cards (" . $matchEvents->hostStuns . " stuns) - " . htmlspecialchars(count($matchEvents->guestCardsLeft)) . " cards (" . $matchEvents->guestStuns . " stuns)</p>";
            }
            else {
              echo "<p>(admin-submitted match)</p>";
            }


            echo "<p><a href='profile.php?id=" . $guest["id"] . "'>" . htmlspecialchars($guest["username"]) . "</a> (";
            echo ($match["guest_elochange"] > 0) ? "+" . $match["guest_elochange"] : $match["guest_elochange"];
            echo ")</p>";

         ?>
       </div>
       <div id="matchLinksContainer">
         <?php
            // echo replay link and time played.

            if (isset($matchEvents->hostCardsLeft)) {
              $dateTime = new DateTime($match["timestamp"]);
              echo "<p>duration of " . $matchEvents->keystrokes[count($matchEvents->keystrokes)-1]->timestamp / 1000 . " seconds. </p>";
              echo "<p><a href='replay.php?id=" . $match["id"] . "'>View Match Replay</a></p>";
            }
            echo "<p> played on " . $match["timestamp"] . "</p>";
         ?>
       </div>
    </div>
  </body>

</html>
