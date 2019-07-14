<?php

include('config.php');
include('lib.php');

$count = 0;
echo 'Cache...' . PHP_EOL;

foreach (glob(__DIR__. '/cache/*.xml') as $filename) {
    $basename = basename($filename);
    $data = file_get_contents($filename);
    setCacheValue('cache/'.$basename, $data);
    $count++;
}

echo 'Done. Processed: '. $count . PHP_EOL;
echo 'Playlists...' . PHP_EOL;

foreach (glob(__DIR__. '/playlists/*') as $filename) {
    $basename = basename($filename);
    $data = file_get_contents($filename);
    setCacheValue($basename, $data);
    $count++;
}

echo 'Done. Processed: '. $count . PHP_EOL;
echo 'Current...' . PHP_EOL;

$current = file_get_contents(__DIR__ . '/playlists/.current');
setCacheValue('current', $current);

$count++;
echo 'Done. Processed: '. $count . PHP_EOL;