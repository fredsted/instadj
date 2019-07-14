<?php
header("Content-Type: text/plain");
//header("Content-Type: application/xml; charset=ISO-8859-1"); 
error_reporting(E_ALL);

ini_set('display_errors', '1');
$subreddit = $_GET['reddit'];

switch ($subreddit) {
    case "futuregarage":
    case "futurebeats":
    case "futurefunkairlines":
    case "dubstep":
    case "realdubstep":
    case "electronicmusic":
    case "idm":
    case "music":
    case "purplemusic":
    case "housemusic":
    case "listentothis":
    case "metal":
    case "indierock":
    case "jazz":
    case "hiphopheads":
    case "chillmusic":
    case "classicalmusic":
    case "trance":
    case "dnb":
    case "mashups":
        define('MAGPIE_DIR', 'magpie/');
        require_once(MAGPIE_DIR.'rss_fetch.inc');
        $url = "http://www.reddit.com/r/". $subreddit ."/search.rss?q=%28and+site%3A%27youtube%27%29&sort=new&restrict_sr=on/";	

        if ( $url ) {
               $rss = @fetch_rss( $url );
        }
}

function extract_youtube_id($string) {
	$matches = null;
    $did_match = preg_match("/www.youtube.com\/watch\?v=(.{11})/", $string, $matches);
		
	if ($did_match)
		return $matches[1];
	return '';
}

?><rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
<channel>
	<title><?=$rss->channel['title'] ?></title>
	<link><?=$rss->channel['link'] ?></link>

<?php
    foreach ($rss->items as $item) {
//      var_dump($item);
        if (strpos($item['atom_content'], 'youtube.com/watch') === false)
        	continue;
        	
//        $href = $item['link'];
        $title = htmlspecialchars($item['title']);
        $descrip = $item['atom_content'];
        $link = extract_youtube_id($item['atom_content']); //substr($descrip, strpos($descrip,'http://www.youtub')+31, 11); 
        echo "		<item>\n";
        echo "			<title>$title</title>\n";
        echo "			<a>$link</a>\n";
        echo "		</item>\n\n";
    
    }
?>
</channel>
</rss>
