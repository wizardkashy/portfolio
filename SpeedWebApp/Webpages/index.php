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
    <title>Speed!</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <?php
      include_once "Partials/header.php";
    ?>
    <div id="main">
      <h1>Speed!</h1>
      <h2>What is Speed?</h2>
      <p>Speed is a playing card game designed for two players. On our website, You can play a game by logging in and visiting Play in the header.
        To play against another player, have both you and the other player log in. It will redirect you to a playfield where you can play the match out and see who is the best!</p>
      <h2>How to play!</h2>
      <p>To place a card on a pile, the card must have a value of one higher or lower than the top card of the pile. Also, kings can be placed on aces and aces on kings.</p>
      <p>To place a card, the player on the LEFT (host) must type:</p>
      <ul>
        <li>1: card 1 (leftmost)</li>
        <li>2: card 2 </li>
        <li>3: card 3 </li>
        <li>4: card 4 </li>
        <li>5: card 5 (rightmost)</li>
      </ul>
      <p>To place a card, the player on the RIGHT (guest) must type:</p>
      <ul>
        <li>o: card 1 (leftmost)</li>
        <li>p: card 2 </li>
        <li>[: card 3 </li>
        <li>]: card 4 </li>
        <li>\: card 5 (rightmost)</li>
      </ul>
      <p>When none of the player's cards can be placed on the piles, a tiebreak card is played from each of the piles on the left and right of the middle.</p>
      <p>A game ends when one player has used up all 20 of their cards, deeming them the winner, or when neither player has used up their cards, but none can be placed and there are no tiebreak cards available (a draw).</p>
      <h3>How can I view my matches?</h3>
      <p>Visit your <a href='profile.php'>profile</a>. You can see your match history and access to friend controls. To view the specifics of a match, you can click on it in your match history, which lets you see mistakes made and view the match replay.</p>
      <h3>How do we rank players?</h3>
      <p>We use the <a href='http://www.glicko.net/glicko/glicko2.pdf'>Glicko2 system</a> for ranking players. Visit our <a href='sources.php'>references page</a> to learn about the PHP implementation we used.</p>
    </div>
  </body>

</html>
