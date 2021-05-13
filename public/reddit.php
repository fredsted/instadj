<?php
require_once '../config.php';
require_once 'lib.php';

//header("Content-Type: application/json");

$subreddit = urlencode($_GET['reddit']);
$url = "https://www.reddit.com/r/$subreddit.json?sort=new&restrict_sr=on&limit=100";
$data = json_decode(readcache($url), true);

$youtubeIds = [];

foreach ($data['data']['children'] as $item) {
	$post = $item['data'];

	$youtubeId = null;

	if (isset($post['url'])) {
		$youtubeId = $youtubeId = extract_youtube_id($post['url']);
	}

	if (isset($post['selftext'])) {
		$youtubeId = $youtubeId = extract_youtube_id($post['selftext']);
	}

	if (isset($post['media']['oembed']['html']) && !$youtubeId) {
		$youtubeId = $youtubeId = extract_youtube_id($post['media']['oembed']['html']);
	}

	if (isset($post['media_embed']['content']) && !$youtubeId) {
		$youtubeId = $youtubeId = extract_youtube_id($post['media_embed']['content']);
	}

	if ($youtubeId) {
		$youtubeIds[] = $youtubeId;
	}
}

resp_json([
	'youtube_ids' => array_chunk($youtubeIds, 50)[0],
]);