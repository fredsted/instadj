<?php

require('config.php');
require('lib.php');

$query = '';

if (isset($_GET['action'])) {
	$action = $_GET['action'];
    switch ($action){
    	case "search":
			$query = "search?part=snippet&q=" . qs('q').'&type=video';
    		break;
    	case "related":
    		$query = "search?part=snippet&relatedToVideoId=". qs('id') ."&type=video";
    		break;
    	default:
    		$query = "videoCategories?part=snippet";
    } 
} else {
	$query = "videoCategories";
}

$url = YT_API . $query . '&key=' . YT_KEY . '&maxResults=50';
$result = json_decode(readcache($url));

//echo'<pre>';print_r($result);die();

foreach ($result->items as $video) {
	$videos[$video->id->videoId] = [
		'title' => $video->snippet->title,
		'thumb' => $video->snippet->thumbnails->high->url,
		'url' => "https://www.youtube.com/watch?v=" . $video->id->videoId,
	];
}

$vqUrl = YT_API . 'videos?part=contentDetails,statistics&id=' . implode(',', array_keys($videos)) . '&key=' . YT_KEY . '&maxResults=50';
$vqRes = json_decode(readcache($vqUrl));

foreach ($vqRes->items as $video) {
	$videos[$video->id]['views'] = $video->statistics->viewCount;
	$videos[$video->id]['hd'] = ($video->contentDetails->definition === 'hd' ? '1' : '0');
	$videos[$video->id]['duration'] = gmdate('i:s', ytv3duration($video->contentDetails->duration));
}

if (count($videos) > 0) {
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
	
	printloadmore();
} else {
	printnoresults();
}



function printvideo($thumb, $url, $title, $hd, $views, $id, $duration) {
	echo '
		<div class="video" style="background-image:url('.$thumb.');">
			<a href="'.$url.'" class="title"> '.$title.'</a>';

	if ($hd === '1') {
		echo '<span class="hd">HD</span>';
	}

	echo '
			<span class="duration">'. $duration .'</span>
			<span class="videoinfo">'.$views.' views</span>
			<div class="playoverlay">&nbsp;</div>
			<span class="related" style="display:none;">
				<a href="#" data-href="yt.php?action=related&id='.$id.'">
				<i class="glyphicon glyphicon-search"></i> Related</a>
			</span>
		</div>
	';
}

function printloadmore() {
    echo "<div class=\"loadmore\">
		<a href=\"ytv3.php?action=".qs('action')
         .'&q='.qs('q').'&user='.qs('user').'&id='.qs('id')
         .'&feed='.qs('feed')."\">
            <img class=\"loadmoreimg\" src=\"/assets/images/more.png\" width=\"62\" height=\"62\" /><br />Load more
	    </a>
    </div>";
}

function printnoresults()
{
	echo '<div class="video" style="background-image:url(/assets/images/noresults.png);"></div>';
}