#!/usr/bin/env php
<?php

$contentsString = file_get_contents("pgn-list.json");
$target="./pgn/br.pgn";
$jsondata = json_decode($contentsString, true);
//print_r($json);

// delete target file
//if (file_exists($target)) {
//    unlink($target);
//}
$file = fopen($target, "w");
foreach ($jsondata['lists'] as $group) {
    //printf("checking %s\n", $group['name']);
    if (str_contains($group['name'], 'All') ||
        str_contains($group['name'], 'Debug')) {
        printf("skipping %s\n", $group['name']);
    } else {
        printf("processing %s\n", $group['name']);
        foreach ($group['pgn'] as $pgn) {
            $source = $pgn['file'];
            $contents = file_get_contents($source);
            fwrite($file, $contents);
            fwrite($file, "\n");
            //printf("file = %s\n", $source);
        }
    }
}
fclose($file);

?>
