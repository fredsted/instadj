<?php
require('base60.php');
require('config.php');
require('lib.php');

if (!isset($_GET['action'])) {
    json(['status' => 'error', 'Missing action']);
}

switch ($_GET['action']) {
    case 'store':
        if (!isset($_POST['playlistdata'])) {
            json(['status' => 'error', 'message' => 'Missing playlist data']);
        }

        $currentPlaylist = getCacheValue('current');
        $thisPlaylist = base60encode(base60decode($currentPlaylist) + 1);
        $data = base64_encode(htmlspecialchars($_POST['playlistdata'], ENT_NOQUOTES));
        setCacheValue($thisPlaylist, $data);
        setCacheValue('current', $thisPlaylist);

        plain($thisPlaylist);
        break;
    case 'get':
        $playlist = preg_replace('/[^a-zA-Z0-9\s]/', '', $_GET['id']);

        $data = getCacheValue($playlist);

        if (!$data) {
            json(['status' => 'error', 'message' => 'Playlist not found']);
        }

        $content = base64_decode($data);
        $parsed = json_decode($content, true);
        $videos = array_keys($parsed);
        $videoInfo = ytGet('videos?part=contentDetails,statistics,id,snippet&id=' . implode(',', $videos));
        $response = [];
        foreach ($videoInfo->items as $video) {
            $response[$video->id] = $video->snippet->title;
        }

        json($response);
        break;
}