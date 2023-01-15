<?php

$contentsString = file_get_contents("pgn-list.json");
$jsondata = json_decode($contentsString, true);
//print_r($json);

foreach ($jsondata['lists'] as $group) {
  printf("<div class='listtitle'>%s</div>\n", $group['name']);
  printf("<ul>\n");
  foreach ($group['pgn'] as $pgn) {
    printf("  <li><a href=\"%s\">\n", $pgn['file']);
    printf("   %s\n", $pgn['text']);
    printf("</a></li>\n");
  }
  printf("</ul>\n");

}

?>
