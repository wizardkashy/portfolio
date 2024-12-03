<?php
session_start();
if (!isset($_GET["id"]) && !isset($_SESSION["host_id"])) {
    header("Location: index.php");
    exit(401);
}
else {
    require_once "../DBManagement/dbconfig.php";
    try {
        $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $sth = $dbh->prepare("SELECT * FROM user WHERE id=:id");
        if (isset($_GET["id"])) {
            // use the GET id value to fetch all data of the certain user queried for and store the result in $user.
            $sth->bindValue(":id", $_GET["id"]);
        }
        else {
            // if the id value is not set, then use the session's host_id to get the profile of the logged in user.
            $sth->bindValue(":id", $_SESSION["host_id"]);
        }
        $sth->execute();
        $user = $sth->fetch();
        if ($user == false) {
          header("Location: profile.php");
          exit();
        }

        if (isset($_GET['accept'])) { // accepting frequests
            try {
                $sth = $dbh->prepare('SELECT * FROM friendship WHERE friendship.id = :requestId;');
                $sth->bindValue('requestId', $_GET['accept']);
                $sth->execute();

                if ($sth->fetch()['requestee_id'] == $_SESSION['host_id']) {
                    $sth = $dbh->prepare('UPDATE friendship SET `status` = "accepted" WHERE friendship.id = :requestId;');
                    $sth->bindValue('requestId', $_GET['accept']);
                    $sth->execute();
                }
            } catch (PDOException $e) { }
        }

        if (isset($_GET['deny'])) { // denying frequests
            try {
                $sth = $dbh->prepare('SELECT * FROM friendship WHERE friendship.id = :requestId;');
                $sth->bindValue('requestId', $_GET['deny']);
                $sth->execute();

                if ($sth->fetch()['requestee_id'] == $_SESSION['host_id']) {
                    $sth = $dbh->prepare('DELETE FROM friendship WHERE friendship.id = :requestId;');
                    $sth->bindValue('requestId', $_GET['deny']);
                    $sth->execute();
                }
            } catch (PDOException $e) { }
        }

        if (isset($_GET['cancel'])) { // canceling frequests
            try {
                $sth = $dbh->prepare('SELECT * FROM friendship WHERE friendship.id = :requestId;');
                $sth->bindValue('requestId', $_GET['cancel']);
                $sth->execute();

                if ($sth->fetch()['requester_id'] == $_SESSION['host_id']) {
                    $sth = $dbh->prepare('DELETE FROM friendship WHERE friendship.id = :requestId;');
                    $sth->bindValue('requestId', $_GET['cancel']);
                    $sth->execute();
                }
            } catch (PDOException $e) { }
        }

        if (isset($_GET['remove'])) { // removing friends
            try {
                $sth = $dbh->prepare('SELECT * FROM friendship WHERE friendship.id = :requestId;');
                $sth->bindValue('requestId', $_GET['remove']);
                $sth->execute();

                $relationship = $sth->fetch();

                if (($relationship['requester_id'] == $_SESSION['host_id']) || ($relationship['requestee_id'] == $_SESSION['host_id'])) {
                    $sth = $dbh->prepare('DELETE FROM friendship WHERE friendship.id = :requestId;');
                    $sth->bindValue('requestId', $_GET['remove']);
                    $sth->execute();
                }
            } catch (PDOException $e) { }
        }

        if (isset($_GET['request'])) { // sending frequests
            try {
                $sth = $dbh->prepare('SELECT * FROM friendship WHERE (requester_id = :requestee_id OR requestee_id = :requestee_id) AND (requester_id = :requester_id OR requestee_id = requester_id);');
                $sth->bindValue('requestee_id', $_GET['request']);
                $sth->bindValue('requester_id', $_SESSION['host_id']);
                $sth->execute();

                if (count($sth->fetchAll()) === 0) {
                    $sth = $dbh->prepare('INSERT INTO friendship (requester_id, requestee_id, status) VALUES (:requester_id, :requestee_id, "pending");');
                    $sth->bindValue('requester_id', $_SESSION['host_id']);
                    $sth->bindValue('requestee_id', $_GET['request']);
                    $sth->execute();
                }
            } catch (PDOException $e) { }
        }

    }
    catch (PDOException $e) {
        echo "<p>Error: {$e->getMessage()}</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Profile</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <?php
    include_once "Partials/header.php";
    ?>
    <div id="main">
        <?php
        // print basic user information
        echo "<h1>" . htmlspecialchars($user["username"]) . "</h1>";
        echo "<h2>Rating: " . htmlspecialchars($user["rating"]) . "&plusmn;" . $user['rd'] . "</h2>";
        if ($_SESSION['admin'] == 1) {
            echo    '
                      <h2>Admin controls.</h2>
                      <form action="admin.php?user=' . $user['id'] . '" method="POST" id="edituserinfo">
                        <div id="controlaction">
                            <input type="checkbox" name="remove">
                            <label for="remove">Remove user</label>
                            <input type="checkbox" name="editname">
                            <label for="editname">edit username</label>
                            <input type="checkbox" name="addmatch">
                            <label for="addmatch">add a new match</label>
                        </div>
                        <h3>edit user info</h3>
                        <div id="change_username">
                            <input type="text" name="new_username" placeholder="New username">
                            <label for="new_username">Edit username</label>
                        </div>
                        <h3>add match</h3>
                        <div id="hostuser">
                          <input type="text" name="hostUsername" placeholder="host username" value="' . $user["username"] . '">
                          <input type="number" name="hostCardsLeft" min="0" max="20">
                          <label for="hostCardsLeft">host cards</label>
                        </div>
                        <div id="guestuser">
                          <input type="text" name="guestUsername" placeholder="guest username">
                          <input type="number" name="guestCardsLeft" min="0" max="20">
                          <label for="guestCardsLeft">guest cards</label>
                        </div>
                        <button type="submit">submit</button>
                    </form>
                    ';
        }
        ?>
        <div id="content">
            <div id="matchHistoryContainer">
                <h2>Match History</h2>
                <?php
                // print the user's match history
                // get information on the user, opponent, winner, elochanges, and timestamp and keep it in one array to iterate through.
                $sth = $dbh->prepare("SELECT user.username AS 'opponent_username', user.id AS 'opponent_id', match.host_elochange AS 'user_elochange', match.guest_elochange AS 'opponent_elochange', match.winner_id, timestamp, match.id FROM `match` JOIN `user` ON match.guest_id = user.id WHERE match.host_id = :userId");
                $sth->bindValue(":userId", $user["id"]);
                $sth->execute();
                $matches = $sth->fetchAll();
                // 2 statements are required for if the $user is the host of the match or the guest of the match.
                $sth = $dbh->prepare("SELECT user.username AS 'opponent_username', user.id AS 'opponent_id', match.host_elochange AS 'opponent_elochange', match.guest_elochange AS 'user_elochange', match.winner_id, timestamp, match.id FROM `match` JOIN `user` ON match.host_id = user.id WHERE match.guest_id = :userId");
                $sth->bindValue(":userId", $user["id"]);
                $sth->execute();
                $matches = array_merge($matches, $sth->fetchAll());
                // var_dump($matches);
                // this doesn't work.
                function sortTimestamps($a, $b) {
                  return new DateTime($a["timestamp"]) <= new DateTime($b["timestamp"]);
                }
                usort($matches, 'sortTimestamps');
                // var_dump($matches);
                // need to sort array by timestamp desc.
                foreach ($matches as $match) {
                    // loop through the fetchAll() array and add divs to the flexbox.

                    // edit the div's class for if the match was won, lost, or drawn
                    if ($match["winner_id"] == $user["id"]) {
                        echo "<a class='match wonmatch' href='match.php?id=" . $match["id"] . "'>";
                    }
                    elseif ($match["winner_id"] == $match["opponent_id"]) {
                        echo "<a class='match lostmatch' href='match.php?id=" . $match["id"] . "'>";
                    }
                    else {
                        echo "<a class='match drawmatch' href='match.php?id=" . $match["id"] . "'>";
                    }

                    // print the user and opponent's names and elos.
                    echo "<p>";
                    echo ($match["user_elochange"] > 0) ? "+" . $match["user_elochange"] : $match["user_elochange"];
                    echo "</p>";
                    echo "<p>" . htmlspecialchars($user["username"]) . "</p>";
                    echo "<p>" . htmlspecialchars($match["opponent_username"]) . "</p>";
                    echo "<p>";
                    echo ($match["opponent_elochange"] > 0) ? "+" . $match["opponent_elochange"] : $match["opponent_elochange"];
                    echo "</p>";
                    $matchTime = new DateTime($match["timestamp"]);
                    echo "<p>" . htmlspecialchars($matchTime->format("m-d-y")) . "</p>";

                    echo "</a>";
                }
                ?>
            </div>
            <div id="friends">
                <div id="friendsHeaderContainer">
                    <?php
                        if (!($user['id'] == $_SESSION['host_id'])) { // logic to show the various friend buttons
                            try {
                                $sth = $dbh->prepare('SELECT * FROM friendship WHERE (requestee_id = :userId AND requester_id = :hostId) OR (requester_id = :userId AND requestee_id = :hostId);');
                                $sth->bindValue('hostId', $_SESSION['host_id']);
                                $sth->bindValue('userId', $user['id']);
                                $sth->execute();

                                $sth2 = $dbh->prepare('SELECT * FROM friendship WHERE status = "pending" AND requester_id = :userId AND requestee_id = :hostId;');
                                $sth2->bindValue('hostId', $_SESSION['host_id']);
                                $sth2->bindValue('userId', $user['id']);
                                $sth2->execute();

                                $sth3 = $dbh->prepare('SELECT * FROM friendship WHERE status = "pending" AND requestee_id = :userId AND requester_id = :hostId;');
                                $sth3->bindValue('hostId', $_SESSION['host_id']);
                                $sth3->bindValue('userId', $user['id']);
                                $sth3->execute();

                                if ($sth->fetch()['status'] === 'accepted') {
                                    // shows remove friend button if you are already friended with the user
                                    $sth = $dbh->prepare('SELECT * FROM friendship WHERE status = "accepted" AND ((requester_id = :hostId AND requestee_id = :userId) OR (requestee_id = :hostId AND requester_id = :userId));');
                                    $sth->bindValue('hostId', $_SESSION['host_id']);
                                    $sth->bindValue('userId', $user['id']);
                                    $sth->execute();

                                    echo '<a href="profile.php?remove=' . $sth->fetch()['id'] . '">Remove Friend</a>';
                                } elseif (count($sth2->fetchAll()) !== 0) { // shows accept button if you have an incoming request
                                    $sth = $dbh->prepare('SELECT * FROM friendship WHERE status = "pending" AND requester_id = :userId AND requestee_id = :hostId;');
                                    $sth->bindValue('userId', $user['id']);
                                    $sth->bindValue('hostId', $_SESSION['host_id']);
                                    $sth->execute();

                                    echo '<a href="profile.php?accept=' . $sth->fetch()['id'] . '">Accept Friend Request</a>';
                                } elseif (count($sth3->fetchAll()) !== 0) { // shows cancel button if you have an outgoing request
                                    $sth = $dbh->prepare('SELECT * FROM friendship WHERE status = "pending" AND requester_id = :hostId AND requestee_id = :userId;');
                                    $sth->bindValue('userId', $user['id']);
                                    $sth->bindValue('hostId', $_SESSION['host_id']);
                                    $sth->execute();

                                    echo '<a href="profile.php?cancel=' . $sth->fetch()['id'] . '">Cancel Outgoing Friend Request</a>';
                                } elseif (isset($_SESSION["host_id"])) {
                                    echo '<a href="profile.php?request=' . $user['id'] . '">Send Friend Request</a>';
                                }
                            } catch (PDOException $e) { }
                        }
                    ?>
                </div>

                <?php
                    if ($user['id'] == $_SESSION['host_id']) { // if the profile being viewed is the currently logged in user, don't display mutual friends, and display friend requests
                        try {
                            $sth = $dbh->prepare('SELECT user.id as id, user.username as username, friendship.id as request_id FROM friendship JOIN user ON friendship.requester_id = user.id WHERE friendship.requestee_id = :userId AND friendship.status = "pending";'); // pending incoming friend requests
                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $pending_incoming_frequests = $sth->fetchAll();

                            echo '<h3>Pending Friend Requests</h3>';

                            echo '<div id="pendingIncomingFRequestsContainer">';
                            echo '<h4>Incoming</h4><ul>';

                            foreach ($pending_incoming_frequests as $key => $value) {
                                echo '<li><a href="profile.php?id=' . htmlspecialchars($value['id']) . '">' . htmlspecialchars($value['username']) . '</a><a href="profile.php?accept=' . htmlspecialchars($value['request_id']) . '">Accept</a><a href="profile.php?deny=' . htmlspecialchars($value['request_id']) . '">Deny</a></li>';
                            }

                            echo '</ul></div>';

                            $sth = $dbh->prepare('SELECT user.id as id, user.username, friendship.id as request_id FROM friendship JOIN user ON friendship.requestee_id = user.id WHERE friendship.requester_id = :userId AND friendship.status = "pending";'); // pending outgoing friend requests
                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $pending_outgoing_frequests = $sth->fetchAll();

                            echo '<div id="pendingOutgoingFRequestsContainer">';
                            echo '<h4>Outgoing</h4><ul>';

                            foreach ($pending_outgoing_frequests as $key => $value) {
                                echo '<li><a href="profile.php?id=' . htmlspecialchars($value['id']) . '">' . htmlspecialchars($value['username']) . '</a><a href="profile.php?cancel=' . htmlspecialchars($value['request_id']) . '">Cancel</a></li>';
                            }

                            echo '</ul></div>';
                        } catch (PDOException $e) {

                        }

                        try { // don't display mutual friends on your own profile
                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requestee_id = user.id WHERE friendship.requester_id = :userId AND friendship.status = "accepted";'); // retrieves accepted friend requests where the friend was the requestee

                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $friends = $sth->fetchAll();

                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requester_id = user.id WHERE friendship.requestee_id = :userId AND friendship.status = "accepted";');

                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $friends = array_merge($friends, $sth->fetchAll()); // retrieves and merges friend requests where the friend was the requested

                            echo '<div id="currentFriendsContainer"><h3>Friends</h3><ul>';
                            foreach ($friends as $key => $value) {
                                echo '<li><a href="profile.php?id=' . htmlspecialchars($value['friend_id']) . '">' . htmlspecialchars($value['friend_username']) . '</a></li>';
                            }
                            echo '</ul></div>';

                        }
                        catch (PDOException $e) {
                            echo "<p>Error: {$e->getMessage()}</p>";
                        }

                    } else {
                        try { // display mutual friends on other profiles
                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requestee_id = user.id WHERE friendship.requester_id = :userId AND friendship.status = "accepted";'); // retrieves accepted friend requests where the friend was the requestee

                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $friends = $sth->fetchAll();

                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requester_id = user.id WHERE friendship.requestee_id = :userId AND friendship.status = "accepted";');

                            $sth->bindValue('userId', $user['id']);
                            $sth->execute();
                            $friends = array_merge($friends, $sth->fetchAll()); // retrieves and merges friend requests where the friend was the requested

                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requestee_id = user.id WHERE friendship.requester_id = :hostId AND friendship.status = "accepted";'); // retrieves accepted friend requests where the friend was the requestee

                            $sth->bindValue('hostId', $_SESSION['host_id']);
                            $sth->execute();
                            $hostFriends = $sth->fetchAll();

                            $sth = $dbh->prepare('SELECT user.id as friend_id, user.username as friend_username FROM friendship JOIN user ON friendship.requester_id = user.id WHERE friendship.requestee_id = :hostId AND friendship.status = "accepted";');

                            $sth->bindValue('hostId', $_SESSION['host_id']);
                            $sth->execute();
                            $hostFriends = array_merge($hostFriends, $sth->fetchAll()); // retrieves and merges friend requests where the friend was the requested

                            $mutualFriends = array();
                            $nonMutualFriends = array();

                            if (!isset($_SESSION['host_id'])) {
                                echo '<div id="currentFriendsContainer"><h3>Friends</h3><ul>';
                                foreach ($friends as $key => $value) {
                                    echo '<li><a href="profile.php?id=' . htmlspecialchars($value['friend_id']) . '">' . htmlspecialchars($value['friend_username']) . '</a></li>';
                                }
                                echo '</ul></div>';
                            } elseif (count($hostFriends) == 0) {
                                echo '<div id="currentFriendsContainer"><h3>Friends</h3><h4>Mutual Friends</h4>';
                                echo '<h4>Non-mutual Friends</h4><ul>';
                                foreach ($friends as $key => $value) {
                                    echo '<li><a href="profile.php?id=' . htmlspecialchars($value['friend_id']) . '">' . htmlspecialchars($value['friend_username']) . '</a></li>';
                                }
                                echo '</ul></div>';
                            } else {
                                foreach ($friends as $friend) {
                                    $mutual = false;
                                    foreach($hostFriends as $hostFriend) {
                                        if ($friend['friend_id'] === $hostFriend['friend_id']) {
                                            array_push($mutualFriends, $friend);
                                            $mutual = true;
                                            break;
                                        }
                                    }
                                    if ($mutual) { continue; }
                                    array_push($nonMutualFriends, $friend);
                                }

                                echo '<div id="currentFriendsContainer"><h3>Friends</h3><h4>Mutual Friends</h4><ul>';
                                foreach ($mutualFriends as $key => $value) {
                                    echo '<li><a href="profile.php?id=' . htmlspecialchars($value['friend_id']) . '">' . htmlspecialchars($value['friend_username']) . '</a></li>';
                                }
                                echo '</ul><h4>Non-mutual Friends</h4><ul>';
                                foreach ($nonMutualFriends as $key => $value) {
                                    echo '<li><a href="profile.php?id=' . htmlspecialchars($value['friend_id']) . '">' . htmlspecialchars($value['friend_username']) . '</a></li>';
                                }
                                echo '</ul></div>';
                            }


                        }
                        catch (PDOException $e) {
                            echo "<p>Error: {$e->getMessage()}</p>";
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
