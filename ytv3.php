<?php

require('config.php');
require('lib.php');

function printvideo($thumb, $url, $title, $hd, $views, $id, $duration)
{
    echo '<div class="video" style="background-image:url(' . $thumb . ');">
			<a href="' . $url . '" class="title"> ' . $title . '</a>';

    if ($hd === '1') {
        echo '<span class="hd">HD</span>';
    }

    echo '<span class="duration">' . $duration . '</span>
			<span class="videoinfo">' . $views . ' views</span>
			<div class="playoverlay">&nbsp;</div>
			<span class="related" style="display:none;">
				<a href="#" data-href="ytv3.php?action=related&id=' . $id . '">
				<i class="glyphicon glyphicon-search"></i> Related</a>
			</span>
		</div>';
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

            return 'search?part=snippet&q=' . qs('q') . '&type=video';

        case 'videoids':
            return 'videos?part=snippet&id=' . qs('ids') . '&maxResults=50';

        case 'related':
            return 'search?part=snippet&relatedToVideoId=' . qs('id') . '&type=video';

        case 'userfavorites':
            $query = 'channels?part=contentDetails&forUsername=' . qs('user');
            $favoritesPlaylistId = ytGet($query)->items[0]->contentDetails->relatedPlaylists->favorites;
            return 'playlistItems?part=snippet&playlistId=' . $favoritesPlaylistId;

        case 'useruploads':
            $query = 'channels?part=contentDetails&forUsername=' . qs('user');
            $favoritesPlaylistId = ytGet($query)->items[0]->contentDetails->relatedPlaylists->uploads;
            return 'playlistItems?part=snippet&playlistId=' . $favoritesPlaylistId;

        default:
            return 'videoCategories?part=snippet';
    }
}

function getvideoinfo($videoids)
{
    return ytGet('videos?part=contentDetails,statistics&id=' . implode(',', $videoids));
}

function printvideos($result, $videos)
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

    if (isset($result->nextPageToken))
        printloadmore($result->nextPageToken);
}


$videos = [];

// Get basic info about each video and collect in $videos array
foreach (ytGet(getquery())->items as $video) {
    $id = isset($video->id->videoId) ? $video->id->videoId : $video->id;

    $videos[$id] = [
        'title' => $video->snippet->title,
        'thumb' => $video->snippet->thumbnails->high->url,
        'url' => "https://www.youtube.com/watch?v=$id",
    ];
}

$result = getvideoinfo(array_keys($videos));

// Embellish videos with views, hd status and duration info
foreach ($result->items as $video) {
    $videos[$video->id]['views'] = $video->statistics->viewCount;
    $videos[$video->id]['hd'] = ($video->contentDetails->definition === 'hd' ? '1' : '0');
    $videos[$video->id]['duration'] = gmdate('i:s', ytDuration($video->contentDetails->duration));
}

if (empty($videos)) {
    printnoresults();
} else {
    printvideos($result, $videos);
}
