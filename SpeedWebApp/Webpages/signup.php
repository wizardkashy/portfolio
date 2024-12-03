<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Signup</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";

    ?>
    <div id="main">
      <h1>Sign up for an account!</h1>
      <form action="signup.php" method="POST">
        <label for="username">
          <p>Username (max 32 characters, can only contain letters a-z, numbers 0-9 and underscores)</p>
          <input name="username" type="text" maxlength="32" required>
        </label>
        <label for="password">
          <p>Password</p>
          <input name="password" type="password" required>
        </label>
        <label for="passwordConfirm">
          <p>Confirm Password</p>
          <input name="passwordConfirm" type="password" required>
        </label>
        <button type="submit">Sign up</button>
      </form>
      <?php
        if (isset($_SESSION["host_id"])) {
          echo "<p>You are already logged in!</p>";
        }

        if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["passwordConfirm"])) {

          require_once "../DBManagement/dbconfig.php";
          try {
              $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);

              $sth = $dbh->prepare("SELECT id, password_hash FROM user WHERE username=:username");
              $sth->bindValue("username", $_POST["username"]);
              $sth->execute();
              $sameUsernames = $sth->fetchAll();

              if (count($sameUsernames) != 0) {
                echo "<h1>Username is already taken! Try again</h1>";
              }
              elseif (preg_match('/[^a-z0-9_]/', $_POST["username"]) != 0 || strlen($_POST["username"]) > 32) { // make sure the username has only the permitted characters.
                echo "<h1>Username must be at most 32 characters and contain only numbers, lowercase letters, and underscores! Try again</h1>";
              }
              elseif ($_POST["password"] != $_POST["passwordConfirm"]) {
                echo "<h1>Passwords don't match!</h1>";
              }
              else {
                // if the password and username are permitted, insert a new record with the username and hashed password
                // the database assigns default values for rating and rd automagically.
                $sth = $dbh->prepare("INSERT INTO user (username, is_admin, password_hash) VALUES (:username, :is_admin, :password_hash)");
                $sth->bindValue(":username", $_POST["username"]);
                $sth->bindValue(":is_admin", 0);
                $sth->bindValue(":password_hash", password_hash($_POST["password"], PASSWORD_DEFAULT));
                $worked = $sth->execute();
                if ($worked == true) {
                  echo "<h1>Signup complete!</h1>";
                }
                else {
                  // this message gets printed if something weird happens and the record couldn't get inserted.
                  echo "<h1>Signup failed for some reason. Try again</h1>";
                }
                }
              }
            catch (PDOException $e) {
                echo "<p>Error: {$e->getMessage()}</p>";
            }
        }
      ?>
      <p>Already have an account? <a href="login.php">Log in here</a></p>
    </div>
  </body>
</html>
