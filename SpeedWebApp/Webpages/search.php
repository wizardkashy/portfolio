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
    <title>Search for a player</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <?php
    include_once "Partials/header.php";
    ?>
    <div id="main">
        <h1>search for a player</h1>
        <form id="searchform" action="search.php" method="get">
            <input type="text" placeholder="Search for a player..." name="query">
            <button type="submit">search</button>
        </form>
        <?php

            if (isset($_GET["query"])) {
                try {
                    $sth = $dbh->prepare("SELECT id, username FROM user WHERE username LIKE :querypattern OR id = :id");
                    // query with %q% to select all records where the query is contained anywhere in the username
                    // searching for "town" yields seventown, eighttown, etc..
                    $sth->bindValue(":querypattern", "%" . $_GET["query"] . "%");
                    $sth->bindValue(":id", $_GET["query"]);
                }
                catch (PDOException $e) {
                    echo "<p>Error: {$e->getMessage()}</p>";
                }
            }
            else {
              // list all players if no query was provided.
                try {
                    $sth = $dbh->prepare("SELECT id, username FROM user");
                }
                catch (PDOException $e) {
                    echo "<p>Error: {$e->getMessage()}</p>";
                }
            }
            try {
                $sth->execute();
                $foundUsers = $sth->fetchAll();
                if (count($foundUsers) == 0) {
                  // fallback if no one was found.
                    echo "<h2>Couldn't find any players with that query :(</h2>";
                }
                else {
                    echo "<ul id='searchlist'>";
                    foreach ($foundUsers as $u) {
                        echo "<li><a href='profile.php?id=" . $u["id"] . "'>" . $u["username"] . "</a></li>";
                    }
                    echo "</ul>";
                }
            }
            catch (PDOException $e) {
                echo "<p>Error: {$e->getMessage()}</p>";
            }

        ?>
    </div>
</body>

</html>
