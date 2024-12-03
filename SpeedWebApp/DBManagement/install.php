<html>
<head>
    <title>install databases</title>
</head>
<body>
<?php
  require_once "dbconfig.php";
  try {
      $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
      $query = file_get_contents('drop.sql');
      $dbh->exec($query);
      $query = file_get_contents('create.sql');
      $dbh->exec($query);
      echo "<p>Successfully uninstalled and installed databases</p>";
  }
  catch (PDOException $e) {
      echo "<p>Error: {$e->getMessage()}</p>";
  }
?>
</body>
</html>
