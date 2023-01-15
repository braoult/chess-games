<?php
require_once "chessParser/autoload.php";

//$json=NULL;

function readJsonFile($file)
{
  $contentsString = file_get_contents($file);
  return json_decode($contentsString, true);
}

function displayPgnList($jsondata)
{
  $i=1;
  foreach ($jsondata['lists'] as $group) {
    printf("<div class='listtitle'>%s</div>\n", $group['name']);
    printf("<ul>\n");
    foreach ($group['pgn'] as $pgn) {
      //printf("  <li><a href=\"%s\">\n", $pgn['file']);
      printf("<li class='arrow'>");
      printf("  <label class='collapse' for='code%d'>%s</label>", $i, $pgn['text']);
      printf("  <input id='code%d' type='checkbox' />", $i);
      displayGamesList($pgn['file']);
      //printf("  <li><a href=\"%s\">\n", $pgn['file']);
      //printf("   %s\n", $pgn['text']);
      printf("</li>\n");
      $i++;
    }
    printf("</ul>\n");
  }
}

function displayGamesList($pgnfile)
{
  static $num=1;
  if (!file_exists($pgnfile)) {
    printf("<p>%s: not such file or directory.</p>", $pgnfile);
  }
  $parser = new PgnParser($pgnfile, false);
  $gameListUnparsed = $parser->getUnparsedGames(); // array
  $gameList=$parser->getGames();
  printf("<div>\n");
  printf("<p><a href='%s'>Download PGN</a></p>", $pgnfile);
  printf("<table id='x-table-%d' class='chess left hover'>\n", $num);
  $num++;
  printf(" <thead>\n");
  printf("  <tr>\n");
  printf("   <th>#</th>\n");
  printf("   <th class='hide'>url</th>\n");
  printf("   <th class='left2x'>Date</th>\n");
  printf("   <th class='left2x'>White</th>\n");
  printf("   <th>Elo</th>\n");
  printf("   <th class='left2x'>Black</th>\n");
  printf("   <th>Elo</th>\n");
  printf("   <th class='left2x'>Result</th>\n");
  printf("   <th>Moves</th>\n");
  printf("   <th class='left2x'>ECO</th>\n");
  printf("   <th>Opening</th>\n");
  printf("   <th class='left2x'>Event</th>\n");
  printf("   <th>Round</th>\n");
  printf("   <th>Place</th>\n");
  printf("  </tr>\n");
  printf(" </thead>\n");
  printf(" <tbody>\n");

  $i=1;
  foreach ($gameList as $game) {
    printf(" <tr>\n");
    printf("  <td class='num'>%d</td>\n", $i);
    printf("   <td class='url'>%s</td>\n", htmlspecialchars($pgnfile));
    printf("  <td class='date'>%s</td>\n", htmlspecialchars($game['date']));
    printf("   <td class='name'>%s</td>\n", htmlspecialchars($game['white']));
    if (isset($game['metadata']['whiteelo'])) {
      printf("   <td class='elo'>%s</td>\n", htmlspecialchars($game['metadata']['whiteelo']));
    } else {
      printf("   <td class='elo'>?</td>\n");
    }
    printf("   <td class='name'>%s</td>\n", htmlspecialchars($game['black']));
    if (isset($game['metadata']['blackelo'])) {
      printf("   <td class='elo'>%s</td>\n", htmlspecialchars($game['metadata']['blackelo']));
    } else {
      printf("   <td class='elo'>?</td>\n");
    }
    printf("   <td class='result'>%s</td>\n", htmlspecialchars($game['result']));
    printf("   <td class='plycount'>%d</td>\n", ($game['plycount'] + 1) / 2);
    //echo "<pre>";
    //print_r($game);
    //echo "</pre>";
    printf("   <td class='eco'>%s</td>\n",
           isset($game['metadata']['ecot']) ? $game['metadata']['ecot'] :
           (isset($game['eco']) ? $game['eco'] : "?"));

    printf("   <td class='opening'>");
    if (isset($game['metadata']['openingt'])) {
        printf("%s", $game['metadata']['openingt']);
        if (isset($game['metadata']['variationt']))
            printf(", %s", $game['metadata']['variationt']);
    } else if (isset($game['metadata']['opening'])) {
        printf("%s", $game['metadata']['opening']);
        if (isset($game['metadata']['variation']))
            printf(", %s", $game['metadata']['variation']);
    } else {
        printf("?");
    }
    printf("</td>\n");

    /* printf("  <td class='url'>%s?file=%s&game=%d</td>\n",
           "showgame.phtml",
           htmlspecialchars($pgnfile),
           $i);
    */
    printf("   <td class='event'>%s</td>\n", htmlspecialchars($game['event']));
    printf("  <td class='round'>%s</td>\n",
           isset($game['round']) && ctype_digit($game['round']) ?
           $game['round']:
           "-");
    if (isset($game['site'])) {
      printf("   <td class='site'>%s</td>\n", htmlspecialchars($game['site']));
    } else {
      printf("   <td class='center'>?</td>\n");
    }
    printf(" </tr>\n");
    $i++;
  }
  printf("</tbody>\n");
  printf("</table>\n");
  printf("<div class='spacer'></div>\n");
  printf("</div>\n");

}

function getGameJson($pgnfile, $game="1")
{
  // check arguments
  //printf("<pre>");
  if (! (preg_match("/^[0-9]+$/", $game))) {
    printf("Game number: [%s] is not an integer\n", htmlspecialchars($game));
    return "";
  }
  //printf("game=%d\n\n", $game);
  if (! (preg_match("/^[-\/a-zA-Z0-9]+\.pgn$/", $pgnfile))) {
    printf("Wrong filename\n");
    return "";
  }
  //printf("file=%s\n", $pgnfile);

  //$parser = new PgnParser( 'annotated.pgn', false );
  //$gamelist_unparsed = $parser->getUnparsedGames(); // array
  //var_dump( $gamelist_unparsed );
  //return;

  $parser = new PgnParser($pgnfile, false);
  //$gameListUnparsed = $parser->getUnparsedGames(); // array
  $gameList=$parser->getGames();
  $game--;
  //printf("game=%d size=%d\n", $game, sizeof($gameListUnparsed));
  if ($game < 0 || $game >= sizeof($gameList)) {
    printf("Wrong game number\n");
    return "";
  }
  $theGame=$gameList[$game];

  //print_r($parser);
  //print_r($gameListUnparsed);
  //printf("STR=+%s+\n", $thegamestring);
  //printf("JSONSTR=+%s+\n", json_encode($thegamestring));
  //printf("\n</pre>");
  return $theGame;
}

function getGameString($pgnfile, $game="1")
{
  // check arguments
  //printf("<pre>");
  if (! (preg_match("/^[0-9]+$/", $game))) {
    printf("Game number: not an integer\n");
    return "";
  }
  //printf("game=%d\n\n", $game);
  if (! (preg_match("/^[-\/a-zA-Z0-9]+\.pgn$/", $pgnfile))) {
    printf("Wrong filename\n");
    return "";
  }
  //printf("file=%s\n", $pgnfile);

  //$parser = new PgnParser( 'annotated.pgn', false );
  //$gamelist_unparsed = $parser->getUnparsedGames(); // array
  //var_dump( $gamelist_unparsed );
  //return;

  $parser = new PgnParser($pgnfile, false);
  // $gameListUnparsed = $parser->getUnparsedGames(); // array
  $gameList=$parser->getGames();
  $game--;
  //printf("game=%d size=%d\n", $game, sizeof($gameListUnparsed));
  if ($game < 0 || $game >= sizeof($gameList)) {
    printf("Wrong game number\n");
    return "";
  }
  //printf("<pre>");
  //print_r($gameList);
  $thegamestring=$gameList[$game]['movesStr'];
  //printf("</pre>");

  //print_r($parser);
  //print_r($gameListUnparsed);
  //printf("STR=+%s+\n", $thegamestring);
  //printf("JSONSTR=+%s+\n", json_encode($thegamestring));
  //printf("\n</pre>");
  return addslashes(json_encode($thegamestring));
}

?>
