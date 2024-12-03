<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";

    ?>
    <div id="main">
      <h1>Log in to your account!</h1>
      <form action="login.php" method="POST">
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
        if (isset($_SESSION["host_id"])) {
          // a user must log out before logging in again.
          echo "<p>You are already logged in!</p>";
        }
        elseif (isset($_POST["username"]) && isset($_POST["password"])) {

          require_once "../DBManagement/dbconfig.php";
          try {
              $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
              // check the password hash against the provided password.
              $sth = $dbh->prepare("SELECT id, password_hash, is_admin FROM user WHERE username=:username");
              $sth->bindValue("username", $_POST["username"]);
              $sth->execute();
              $user = $sth->fetch();
              // var_dump($user);
              if (password_verify($_POST["password"], $user["password_hash"])) {
                $_SESSION["host_id"] = $user["id"];
                $_SESSION["admin"] = $user["is_admin"]; // give admin permissions if the user is an admin.
                header("Location: profile.php");
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
