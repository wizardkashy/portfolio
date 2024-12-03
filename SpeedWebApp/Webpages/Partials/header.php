<?php
  session_start();
?>

<header>
  <a href='index.php'><img src="Images/longlogowhite.png" alt="logo"></a>
  <ul>
    <li><a href="play.php">Play</a></li>
    <li><a href="leaderboard.php">Leaderboard</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="sources.php">References</a></li>
  </ul>
  <form action="search.php" method="get">
    <input type="text" placeholder="Search for a player..." name="query">
    <button type="submit">Search</button>
  </form>
  <?php
    if (isset($_SESSION["host_id"])) {
      require_once "../DBManagement/dbconfig.php";
      try {
          $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
          $sth = $dbh->prepare("SELECT user.username FROM user WHERE id=:id");
          $sth->bindValue(":id", $_SESSION["host_id"]);
          $sth->execute();
          $loggedInUser = $sth->fetch();
          echo "<div>";
          echo "<p><a href='profile.php'>" . htmlspecialchars($loggedInUser["username"]) . "</a></p>";
          echo "<p><a href='logout.php'>Logout</a></p>";
          echo "</div>";
      }
      catch (PDOException $e) {
          echo "<p>Error: {$e->getMessage()}</p>";
      }
    }
    else {
      echo "<p><a href='login.php'>Login</a><br>";
      echo "<a href='signup.php'>Sign Up</a></p>";
    }
  ?>
</header>
<!-- Include logic to change if the user is logged in or not. have a login/logout button. -->
