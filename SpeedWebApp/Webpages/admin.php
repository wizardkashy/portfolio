<?php require_once '../DBManagement/dbconfig.php'; $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD); session_start();
    if ($_SESSION['admin'] != 1) {
        header("Location: profile.php");
    }
    require_once "../Glicko2/src/Glicko2.php";
    require_once "../Glicko2/src/Match.php";
    require_once "../Glicko2/src/MatchCollection.php";
    require_once "../Glicko2/src/Player.php";
    use Zelenin\Glicko2\Glicko2;
    use Zelenin\Glicko2\Match;
    use Zelenin\Glicko2\MatchCollection;
    use Zelenin\Glicko2\Player;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Form Submission</title>
</head>
<body>
    <?php
        // var_dump($_POST);
        if ($_SESSION['admin'] == 1) {
            if ($_POST["remove"] == "on") {
                // remove the player from the database.
                try {
                    $sth = $dbh->prepare('DELETE FROM user WHERE id = :userId;');
                    $sth->bindValue('userId', $_GET['user']);
                    if ($sth->execute()) {
                        echo '<p>Successfully removed user</p>';
                    }
                } catch (PDOException $e) {}
            }
            elseif ($_POST["editname"] == "on") {
                // edit the player's username
                try {
                    $sth = $dbh->prepare('SELECT * FROM user WHERE username = :username;');
                    $sth->bindValue('username', $_POST['new_username']);
                    $sth->execute();

                    if (count($sth->fetchAll()) != 0) {
                        echo "<p>Username already taken</p>";
                    }
                    elseif (preg_match('/[^a-z0-9_]/', $_POST["new_username"]) != 0 || strlen($_POST["new_username"]) > 32) { // make sure the username has only the permitted characters.
                        echo "<p>Username must be at most 32 characters and contain only numbers, lowercase letters, and underscores! Try again</p>";
                    } else {
                        $sth = $dbh->prepare('UPDATE user SET username = :username WHERE id = :userId;');
                        $sth->bindValue('userId', $_GET['user']);
                        $sth->bindValue('username', $_POST['new_username']);
                        if ($sth->execute()) {
                            echo '<p>Successfully updated username</p>';
                        }
                    }
                } catch (PDOException $e) {}
            }
            if ($_POST["addmatch"] == "on" && !empty($_POST["hostUsername"]) && !empty($_POST["guestUsername"])) {
                // add a match to the database.
                if ((!filter_var($_POST['hostCardsLeft'], FILTER_VALIDATE_INT)) && (!filter_var($_POST['guestCardsLeft'], FILTER_VALIDATE_INT)) && $_POST["hostCardsLeft"] >= 0 && $_POST["hostCardsLeft"] <= 20 && $_POST["guestCardsLeft"] >= 0 && $_POST["guestCardsLeft"] <= 20) {

                    $sth = $dbh->prepare('SELECT * FROM user WHERE username=:name');
                    $sth->bindValue(':name', $_POST["hostUsername"]);
                    $sth->execute();
                    $host = $sth->fetch();

                    $sth = $dbh->prepare('SELECT * FROM user WHERE username=:name');
                    $sth->bindValue(':name', $_POST["guestUsername"]);
                    $sth->execute();
                    $guest = $sth->fetch();
                    if ($host == false || $guest == false) {
                      echo "<h1>Players not found!</h1>";
                      exit(406);
                    }
                    if ($_POST["hostCardsLeft"] == 0) {
                        $winner = "host";
                        $winnerid = $host['id'];
                    }
                    elseif ($_POST["guestCardsLeft"]== 0) {
                        $winner = "guest";
                        $winnerid = $guest['id'];
                    }
                    else {
                        $winner = "draw";
                        $winnerid = 0;
                    }

                    try {
                        // calculate elo changes for both players.
                        $glicko = new Glicko2();
                        // var_dump($data);
                        // var_dump($data->guestCardsLeft);
                        $hostScore = 20 - (int)$_POST['hostCardsLeft'];
                        $guestScore = 20 - (int)$_POST['guestCardsLeft'];
                        // dn't know how to construct the players. Which variables do i need to store per player and which ones are constant for calculations?
                        $hostPlayer = new Player($host["rating"], $host["rd"]); // need to add a new column to the table for ratings deviation.
                        $guestPlayer = new Player($guest["rating"], $guest["rd"]);
                        $match = new Match($hostPlayer, $guestPlayer, $hostScore, $guestScore);
                        $glicko->calculateMatch($match);

                        // echo $host["username"] . " - " . $host["rating"] . " -> " . $hostPlayer->getR() . ", " . $host["rd"] . " -> " . $hostPlayer->getRd();
                        // echo $guest["username"] . " - " . $guest["rating"] . " -> " . $guestPlayer->getR() . ", " . $guest["rd"] . " -> " . $guestPlayer->getRd();
                        // add a new match record
                        $sth = $dbh->prepare("INSERT INTO `match` (host_id, guest_id, winner_id, host_elochange, guest_elochange)
                        VALUES (:host_id, :guest_id, :winner_id, :host_elochange, :guest_elochange)");
                        $sth->bindValue(":host_id", $host["id"]);
                        $sth->bindValue(":guest_id", $guest["id"]);
                        $sth->bindValue(":winner_id", $winnerid);
                        $sth->bindValue(":host_elochange", $hostPlayer->getR() - $host["rating"]);
                        $sth->bindValue(":guest_elochange", $guestPlayer->getR() - $guest["rating"]);
                        $sth->execute();
                        // $matchID = $dbh->lastInsertId();
                        // echo $matchID;
                        // edit the elo values of both players.
                        $sth = $dbh->prepare("UPDATE user SET rating = :newrating, rd = :newrd WHERE id=:id");
                        $sth->bindValue(":newrating", $hostPlayer->getR());
                        $sth->bindValue(":newrd", $hostPlayer->getRd());
                        $sth->bindValue(":id", $host["id"]);
                        $sth->execute();

                        $sth = $dbh->prepare("UPDATE user SET rating = :newrating, rd = :newrd WHERE id=:id");
                        $sth->bindValue(":newrating", $guestPlayer->getR());
                        $sth->bindValue(":newrd", $guestPlayer->getRd());
                        $sth->bindValue(":id", $guest["id"]);
                        $sth->execute();

                        if ($_GET["referrer"] == "populate") {
                          header("Location: populateMatches.php");
                        }

                    } catch (PDOException $e) {echo $e->getMessage();}

                }
                else {
                    echo "<h1>Incorrect card information</h1>";
                }
            }
            else {
                echo "<h1>Did not submit usernames for a match / did not request to add a match</h1>";
            }
        }
    ?>
</body>
</html>
