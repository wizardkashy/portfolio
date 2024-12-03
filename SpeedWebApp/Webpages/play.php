<?php
  session_start();
  if (!isset($_SESSION["host_id"])) {
    header("Location: login.php");
  }
  if (!isset($_SESSION["guest_id"])) {
    header("Location: guestlogin.php");
  }
  require_once "../DBManagement/dbconfig.php";
  try {
      $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
      $sth = $dbh->prepare("SELECT * FROM user where id=:hostid");
      $sth->bindValue(":hostid", $_SESSION["host_id"]);
      $sth->execute();
      $hostUser = $sth->fetch();

      $sth = $dbh->prepare("SELECT * FROM user where id=:guestid");
      $sth->bindValue(":guestid", $_SESSION["guest_id"]);
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
    <title>Play a game</title>
    <link href="styles.css" rel="stylesheet">
    <script
      src="https://code.jquery.com/jquery-3.3.1.js"
      integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
      crossorigin="anonymous"></script>
    <script src="Scripts/playGame.js"></script>
  </head>
  <body>
    <div id="playField">
      <div id="placeField"> <!-- this field is where the users place their cards.-->
        <div id="leftTiebreakerPile"></div>
        <div id="leftPlayPile"></div>
        <div id="rightPlayPile"></div>
        <div id="rightTiebreakerPile"></div>
      </div>
      <div id="dealField"><!-- this field is where the users see and receive cards to place.-->
        <div id="hostCards">
          <div class="card1"></div> <!-- these cards are styled as cards and their contents are set in the javascript. -->
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
      <div id="information"> <!-- this div contains information on the users, cards left and stuns. -->
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
