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

$class = ' ' . qs('class');

if (count($result->items > 0)) {
	
	foreach ($result->items as $video) {
		$thumb		= $video->snippet->thumbnails->default->url;
		$title		= $video->snippet->title;
		$hd			= '';
		$views		= '0';
		$id			= $video->id->videoId;
		$url		= "https://www.youtube.com/watch?v=" . $id;

		
		printvideo($class, $thumb, $url, $title, $hd, $views, $id);
	}
	
	printloadmore();

} else {
	printnoresults($class);
}



function printvideo($class, $thumb, $url, $title, $hd, $views, $id) {
	echo '
		<div class="video'.$class.'" style="background-image:url('.$thumb.');">
			<a href="'.$url.'" class="title"> '.$title.'</a>
			'.$hd.'
			<span class="videoinfo">'.$views.' views</span>
			<div class="playoverlay">&nbsp;</div>
			<span class="related" style="display:none;"><a href="#" data-href="yt.php?action=related&id='.$id.'"><i class="glyphicon glyphicon-search"></i> Related</a></span>
		</div>
	';
}

function printloadmore() {
    echo "<div class=\"loadmore\">
		<a href=\"yt.php?action=".qs('action')
         .'&q='.qs('q').'&user='.qs('user').'&id='.qs('id')
         .'&feed='.qs('feed')."\">
            <img class=\"loadmoreimg\" src=\"/assets/images/more.png\" width=\"62\" height=\"62\" /><br />Load more
	    </a>
    </div>";
}

function printnoresults($class)
{
	echo '<div class="video'.$class.'" style="background-image:url(/assets/images/noresults.png);"></div>';
}