<?php
  session_start();
  if (!isset($_GET["id"])) {
    header("Location: profile.php");
    exit(406);
  }

  require_once "../DBManagement/dbconfig.php";
  try {
      $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
      $sth = $dbh->prepare("SELECT * FROM `match` WHERE id=:id");
      $sth->bindValue(":id", $_GET["id"]);
      $sth->execute();
      $match = $sth->fetch();
      if ($match == false) {
        header("Location: profile.php");
        exit(406);
      }


      $sth = $dbh->prepare("SELECT * FROM user WHERE id=:id");
      $sth->bindValue(":id", $match["host_id"]);
      $sth->execute();
      $hostUser = $sth->fetch();

      $sth = $dbh->prepare("SELECT * FROM user WHERE id=:id");
      $sth->bindValue(":id", $match["guest_id"]);
      $sth->execute();
      $guestUser = $sth->fetch();

  }
  catch (PDOException $e) {
      echo "<p>Error: {$e->getMessage()}</p>";
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Replay a game</title>
    <link href="styles.css" rel="stylesheet">
    <script
      src="https://code.jquery.com/jquery-3.3.1.js"
      integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
      crossorigin="anonymous"></script>
    <script src="Scripts/replayGame.js"></script>
  </head>
  <body>
    <!-- Print out the information that the javascript needs to be able to replay the game.
    this includes match events, initial deck order, and the match id (to redirect back after the game).-->
    <?php
      if ($match["match_events"] == NULL || $match["deck_order"] == null) {
        echo "<h1>Match log not found! Likely an admin-submitted match.</h1>";
        exit(406);
      }
     ?>
    <p id="replayID" class="hidden">
      <?php
        echo $match["id"];
       ?>
    </p>
    <p id="initDeckOrder" class="hidden">
      <?php
        echo $match["deck_order"];
       ?>
    </p>
    <p id="matchEvents" class="hidden">
      <?php
        echo $match["match_events"];
      ?>
    </p>
    <div id="playField">
      <div id="placeField">
        <div id="leftTiebreakerPile"></div>
        <div id="leftPlayPile"></div>
        <div id="rightPlayPile"></div>
        <div id="rightTiebreakerPile"></div>
      </div>
      <div id="dealField">
        <div id="hostCards">
          <div class="card1"></div>
          <div class="card2"></div>
          <div class="card3"></div>
          <div class="card4"></div>
          <div class="card5"></div>
        </div>
        <div id="guestCards">
          <div class="card1"></div>
          <div class="card2"></div>
          <div class="card3"></div>
          <div class="card4"></div>
          <div class="card5"></div>
        </div>
      </div>
      <div id="information">
        <div id="hostinfo">
          <?php
            echo "<p>" . htmlspecialchars($hostUser["username"]) . "</p>";
          ?>
          <p id="hostCardsLeft"></p>
          <p id="hostStuns"></p>
        </div>
        <div id="guestinfo">
          <?php
            echo "<p>" . htmlspecialchars($guestUser["username"]) . "</p>";
          ?>
          <p id="guestCardsLeft"></p>
          <p id="guestStuns"></p>
        </div>
      </div>
    </div>
  </body>
</html>
