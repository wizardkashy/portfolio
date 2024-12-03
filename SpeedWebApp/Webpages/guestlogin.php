<?php
  session_start();
  if (!isset($_SESSION["host_id"])) {
    header("Location: login.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Guest Login</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";

    ?>
    <div id="main">
      <h1>Guest log in to play the game!</h1>
      <form action="guestlogin.php" method="POST">
        <label for="username">
          Username
          <input name="username" type="text" maxlength="32" required id="username">
        </label>
        <label for="password">
          Password
          <input name="password" type="password" required id="password">
        </label>
        <button type="submit">Log in</button>
      </form>
      <?php
        // same logic as login.php but with the guest. also redirects to a play once logged in.
        if (isset($_SESSION["guest_id"])) {
          echo "<p>You are already logged in! go <a href='play.php'>here to play</a></p>";
        }
        elseif (isset($_POST["username"]) && isset($_POST["password"])) {
          require_once "../DBManagement/dbconfig.php";
          try {
              $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);

              $sth = $dbh->prepare("SELECT id, password_hash FROM user WHERE username=:username");
              $sth->bindValue("username", $_POST["username"]);
              $sth->execute();
              $user = $sth->fetch();
              if ($user["id"] == $_SESSION["host_id"]) {
                echo "<h1>You cannot log in as the same person as host!</h1>";
              }
              // var_dump($user);
              if (password_verify($_POST["password"], $user["password_hash"])) {
                $_SESSION["guest_id"] = $user["id"];
                header("Location: play.php");
              }
              else {
                echo "<p class='danger'> Incorrect Password Entered! Try again.</p>";
              }
          }
          catch (PDOException $e) {
              echo "<p>Error: {$e->getMessage()}</p>";
          }
        }

      ?>
      <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
  </body>
</html>
