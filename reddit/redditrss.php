<?php
//header("Content-Type: application/xml; charset=ISO-8859-1"); 
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
    case "classicalmusic":
    case "trance":
    case "dnb":
    case "mashups":
    
    
        define('MAGPIE_DIR', 'magpie/');
        require_once(MAGPIE_DIR.'rss_fetch.inc');
        $url = "http://www.reddit.com/r/". $subreddit ."/search.rss?q=%28and+site%3A%27youtube%27%29&sort=new&restrict_sr=on/";
       
//	echo $url."  <br><br>";
	
	
        if ( $url ) {
               $rss = @fetch_rss( $url );
        }
        

}
?><rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
<channel>
	<title><?=$rss->channel['title'] ?></title>
	<link><?=$rss->channel['link'] ?></link>

<?php
    foreach ($rss->items as $item) {
      
        if (strpos($item['description'], 'youtube') === false) {
          //dont do anything
        } 
        else {
            $href = $item['link'];
            $title = htmlspecialchars($item['title']);
            $descrip = $item['description'];
            $link = substr($descrip, strpos($descrip,'http://www.youtub')+31, 11); 
            echo "		<item>\n";
            echo "			<title>$title</title>\n";
            echo "			<a>$link</a>\n";
            echo "		</item>\n\n";
        }
        
    }
?>
</channel>
</rss>