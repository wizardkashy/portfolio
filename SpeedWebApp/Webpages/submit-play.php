<?php
    require_once "../Glicko2/src/Glicko2.php";
    require_once "../Glicko2/src/Match.php";
    require_once "../Glicko2/src/MatchCollection.php";
    require_once "../Glicko2/src/Player.php";
    use Zelenin\Glicko2\Glicko2;
    use Zelenin\Glicko2\Match;
    use Zelenin\Glicko2\MatchCollection;
    use Zelenin\Glicko2\Player;
    session_start();
      if (!isset($_SESSION["host_id"]) || !isset($_SESSION["guest_id"])) {
        exit(406);
      }

    require_once '../DBManagement/dbconfig.php';

    try {
        $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {}

    $jsonData = file_get_contents('php://input'); // accesses the JSON data from the javascript POST request.

    $data = json_decode($jsonData); // data posted as a JSON is stored in $data as a php object
    // var_dump($data);
    // A LOOT OF VALIDATION :(
    if ($data->winner == "host") {
      if (count($data->hostCardsLeft) != 0) {
        exit(406);
      }
      $winnerid = $_SESSION["host_id"];
    }
    elseif ($data->winner == "guest") {
      if (count($data->guestCardsLeft) != 0) {
        exit(406);
      }
      $winnerid = $_SESSION["guest_id"];
    }
    elseif ($data->winner == "draw") {
      $winnerid = 0;
    }
    else {
      exit(406); // something went wrong / bad data.
    }
    // var_dump($data->hostCardsLeft);
    // var_dump(count($data->hostCardsLeft));

    if (count($data->hostCardsLeft) < 0 || count($data->hostCardsLeft) > 20 || count($data->guestCardsLeft) < 0 || count($data->guestCardsLeft) > 20) {
      // echo "not within range";
      exit(406); // information isn't within the expected range of cards.
    }

    // fetch the host and guest from the database.
    try {
        $sth = $dbh->prepare('SELECT * FROM user WHERE id=:id');
        $sth->bindValue('id', $_SESSION["host_id"]);
        $sth->execute();
        $host = $sth->fetch();

        $sth = $dbh->prepare('SELECT * FROM user WHERE id=:id');
        $sth->bindValue('id', $_SESSION["guest_id"]);
        $sth->execute();
        $guest = $sth->fetch();

        // calculate elo changes for both players.
        $glicko = new Glicko2();
        // var_dump($data);
        // var_dump($data->guestCardsLeft);
        $hostScore = 20 - count($data->hostCardsLeft); // the scores are based on how many cards they placed (20 - cards left)
        $guestScore = 20 - count($data->guestCardsLeft);

        $hostPlayer = new Player($host["rating"], $host["rd"]);
        $guestPlayer = new Player($guest["rating"], $guest["rd"]);
        $match = new Match($hostPlayer, $guestPlayer, $hostScore, $guestScore);
        $glicko->calculateMatch($match);

        // echo $host["username"] . " - " . $host["rating"] . " -> " . $hostPlayer->getR() . ", " . $host["rd"] . " -> " . $hostPlayer->getRd();
        // echo $guest["username"] . " - " . $guest["rating"] . " -> " . $guestPlayer->getR() . ", " . $guest["rd"] . " -> " . $guestPlayer->getRd();
        // add a new match record
        $sth = $dbh->prepare("INSERT INTO `match` (host_id, guest_id, deck_order, match_events, winner_id, host_elochange, guest_elochange)
        VALUES (:host_id, :guest_id, :deck_order, :match_events, :winner_id, :host_elochange, :guest_elochange)");
        $sth->bindValue(":host_id", $_SESSION["host_id"]);
        $sth->bindValue(":guest_id", $_SESSION["guest_id"]);
        $sth->bindValue(":deck_order", json_encode($data->initDeckOrder)); // this and match events are used for replays!
        $sth->bindValue(":match_events", json_encode($data->matchEvents));
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

    } catch (PDOException $e) {echo $e->getMessage();}

    // try {
    //     $sth = $dbh->prepare('INSERT INTO test (id, data) VALUES (:id, :data);');
    //     $sth->bindValue('id', $data->id);
    //     $sth->bindValue('data', $data);
    //
    //     $sth->execute();
    // } catch (PDOException $e) {}
    ?>
