<?php
//error_reporting(E_ALL); 
ini_set( 'display_errors','0');

    $maxResults = 50;
    $ytAPI = "http://gdata.youtube.com/feeds/api/";
    
    function qs($which, $default = '') {
    	if (isset($_GET[$which])) {
      		if ($_GET[$which]!== '') {
    			return urlencode($_GET[$which]);
    		} else {
    			return $default;
    		}
    	}
    }
    
    function writecache($path, $URL) {
    	$data = file_get_contents($URL); 
    	//secho "skriver cache fra $URL til $path";
    	if (file_exists('./'.$path)) {
    		unlink('./'.$path);
		}
		$fp = fopen('./'.$path, 'w+'); 
		fwrite($fp, $data); 
		fclose($fp);
    }

    function readcache($URL) {
    	$filepath = 'cache/'.md5($URL).'.xml';
    	
  		if (file_exists($filepath)) {
			if (filemtime($filepath) < time() - 3600*24) {  // 24hrs
				writecache($filepath, $URL);
			}
   		} else {
   			writecache($filepath, $URL);
   		}

   		return $filepath;
    }
    
    $query = '';
	
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
	    switch ($action){
	    	case "search":
				$query = "videos?q=" . qs('q');
				try {
					$db = new PDO('sqlite:searches.sqlite');
					
					$sql = "INSERT INTO searches (search, date) VALUES (:search, date('now'));";
					
					$sth = $db->prepare($sql);
					
					$savequery = trim(strtolower(qs('q')));
					
					$sth->execute(array(':search'=>substr($savequery, 0, 60)));
									
					$db = NULL;

				}
					catch(PDOException $e)
				{
					print 'Exception : '.$e->getMessage();
				}
				
	    		break;
	    	case "userfavorites":
	    		$query = "users/" . qs('user','fredsted') . "/favorites?";
	    		break;
	    	case "useruploads":
	    		$query = "users/" . qs('user','fredsted') . "/uploads?";
	    		break;
	    	case "related":
	    		$query = "videos/". qs('id') ."/related?v=2";
	    		break;
	    	case "standardfeed":
	    		$query = "standardfeeds/" . qs('feed','toprated');
	    		break;
	    	default:
	    		$query = "users/fredsted/favorites?";
	    } 
	} else {
	    $query = "users/fredsted/favorites?";
	}
	
    $queryURL = $ytAPI . $query . "&max-results=" . $maxResults;

    if (isset($_GET['from'])) {
    	$queryURL = $queryURL . "&start-index=" . qs('from','0');
    }
	
	$class = ' ' . qs('class');
	
	$sxml = simplexml_load_file(readcache($queryURL));
	
	if (!empty($sxml->entry)) {
	    $i = 0;
	  	foreach ($sxml->entry as $entry) {
            $media = $entry->children('media', true);
            $thumb = (string)$media->group->thumbnail[1]->attributes()->url;
            $url = (string)$media->group->player->attributes()->url;
			parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
			$id = $my_array_of_vars['v']; 
            $title =  (string)$media->group->title;
			$yt = $entry->children('yt', true);
			$views = number_format((string)$yt->statistics->attributes()->viewCount, 0, '.', ',');
			$hd = "";
			if (isset($entry->children('yt')->hd)) { $hd = "<span class='hd'>HD</span>"; }
            echo <<<HTML
<div class="video$class" style="background-image:url($thumb);">
	<a href="$url" class="title"> $title</a>
	$hd
	<span class="videoinfo">$views views</span>
	<span class="playoverlay">&nbsp;</span>
	<span class="related" style="display:none;"><a href="#" data-href="yt.php?action=related&id=$id">Related</a></span>
</div>
HTML;
	    
}	 // <a href="#" class="btn btn-mini addtoplaylist"><i class="icon icon-plus"></i></a>
	    
    echo "<div class=\"loadmore\">
			<a href=\"yt.php?action=".qs('action')
	         .'&q='.qs('q').'&user='.qs('user').'&id='.qs('id')
	         .'&feed='.qs('feed')."\">
                <img class=\"loadmoreimg\" src=\"more.png\" width=\"62\" height=\"62\" />
		    </a>
	    </div>";

 

} else {
echo "No results!";
}




?>