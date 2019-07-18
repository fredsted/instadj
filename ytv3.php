<?php

require('config.php');
require('lib.php');

function ytget($query)
{
    $url = YT_API . $query . '&key=' . YT_KEY . '&maxResults=50';
    $result = json_decode(readcache($url));
    return $result;
}

function printvideo($thumb, $url, $title, $hd, $views, $id, $duration)
{
    echo '
		<div class="video" style="background-image:url(' . $thumb . ');">
			<a href="' . $url . '" class="title"> ' . $title . '</a>';

    if ($hd === '1') {
        echo '<span class="hd">HD</span>';
    }

    echo '
			<span class="duration">' . $duration . '</span>
			<span class="videoinfo">' . $views . ' views</span>
			<div class="playoverlay">&nbsp;</div>
			<span class="related" style="display:none;">
				<a href="#" data-href="ytv3.php?action=related&id=' . $id . '">
				<i class="glyphicon glyphicon-search"></i> Related</a>
			</span>
		</div>
	';
}

function printloadmore($nextPageToken)
{
    echo "<div class=\"loadmore\">
		<a href=\"ytv3.php?action=" . qs('action')
        . '&q=' . qs('q') . '&user=' . qs('user') . '&id=' . qs('id') . '&next_page=' . $nextPageToken
        . '&feed=' . qs('feed') . "\">
            <img class=\"loadmoreimg\" src=\"/assets/images/more.png\" width=\"138\" height=\"138\" />
	    </a>
    </div>";
}

function printnoresults()
{
    echo '<div class="video" style="background-image:url(/assets/images/noresults.png);"></div>';
}

function getquery()
{
    switch ($_GET['action']) {
        case 'search':
            if (strpos(qs('q'), '%2Fplaylist%3Flist%3D') !== false) {
                preg_match('/list\%3D([a-zA-Z0-9_-]+)/s', qs('q'), $matches);
                if (!empty($matches)) return 'playlistItems?part=snippet&playlistId=' . $matches[1];
            }

            $url = 'search?part=snippet&q=' . qs('q') . '&type=video';
            if (isset($_GET['next_page'])) {
                $url .= '&pageToken=' . ($_GET['next_page']);
            }
            return $url;

        case 'videoids':
            return 'videos?part=snippet&id=' . qs('ids') . '&maxResults=50';

        case 'related':
            return 'search?part=snippet&relatedToVideoId=' . qs('id') . '&type=video';

        case 'userfavorites':
            $query = 'channels?part=contentDetails&forUsername=' . qs('user');
            $favoritesPlaylistId = ytget($query)->items[0]->contentDetails->relatedPlaylists->favorites;
            return 'playlistItems?part=snippet&playlistId=' . $favoritesPlaylistId;

        case 'useruploads':
            $query = 'channels?part=contentDetails&forUsername=' . qs('user');
            $favoritesPlaylistId = ytget($query)->items[0]->contentDetails->relatedPlaylists->uploads;
            return 'playlistItems?part=snippet&playlistId=' . $favoritesPlaylistId;

        default:
            return 'videoCategories?part=snippet';
    }
}

function getvideoinfo($videoids)
{
    $url = 'videos?part=contentDetails,statistics&id=' . implode(',', $videoids);
    $result = ytget($url);
    return $result;
}

function printvideos($videos, $results)
{
    foreach ($videos as $videoId => $video) {
        printvideo(
            $video['thumb'],
            $video['url'],
            $video['title'],
            $video['hd'],
            $video['views'],
            $videoId,
            $video['duration']
        );
    }

    if (isset($results->nextPageToken)) printloadmore($results->nextPageToken);
}


$videos = [];
$results = ytget(getquery());

// Get basic info about each video and collect in $videos array
foreach ($results->items as $video) {
    $id = isset($video->id->videoId) ? $video->id->videoId : $video->id;

    $videos[$id] = [
        'title' => $video->snippet->title,
        'thumb' => $video->snippet->thumbnails->high->url,
        'url' => "https://www.youtube.com/watch?v=$id",
    ];
}
$videoInfo = getvideoinfo(array_keys($videos))->items;

// Embellish videos with views, hd status and duration info
foreach ($videoInfo as $video) {
    $videos[$video->id]['views'] = $video->statistics->viewCount ?? 0;
    $videos[$video->id]['hd'] = ($video->contentDetails->definition === 'hd' ? '1' : '0');
    $videos[$video->id]['duration'] = ytv3duration($video->contentDetails->duration);
}

if (empty($videos)) {
    return printnoresults();
}

return printvideos($videos, $results);