<?php
require('base60.php');

if (isset($_GET['action'])) {
	$action = $_GET['action'];
	
    switch ($action){
    	case "store":
			$last_playlist_id = file_get_contents('playlists/.current');
			$next_playlist_id = base60encode(base60decode($last_playlist_id) + 1);
						
			if (isset($_POST["playlistdata"])) {
				if (!file_exists('playlists/'.$next_playlist_id)) {
					$data = base64_encode(htmlspecialchars($_POST["playlistdata"], ENT_NOQUOTES));
					
					$fp = fopen('playlists/'.$next_playlist_id, 'w+'); 
					fwrite($fp, $data); 
					fclose($fp);
					
					unlink('playlists/.current');
					$fp2 = fopen('playlists/.current', 'w+'); 
					fwrite($fp2, (string) $next_playlist_id); 
					fclose($fp2);
					
					echo $next_playlist_id;
		   		} else {
		   			echo '_instadjerr_2'.$last_playlist_id;
		   		}
			} else {
				echo '_instadjerr_4';
			}
			break;

    	case "get":
    		if (isset($_GET['id'])) {
	    		$id = preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']);
		    	$filepath = 'playlists/'.$id;
		    	
		  		if (file_exists($filepath)) {
					echo base64_decode(file_get_contents($filepath));
		   		} else {
		   			echo '_instadjerr_6';
		   		}
			} else {
				echo '_instadjerr_5';
			}
    		break;
    	default:
    		echo '_instadjerr_3';
    } 
} else {
	echo '_instadjerr_1';
}
	
	

?>