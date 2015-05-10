<?php
require('base60.php');
require('config.php');

if (!is_dir(PL_DIR)) {
	mkdir(PL_DIR);
}
if (!file_exists(CUR_FILE)) {
	file_put_contents(CUR_FILE, 'a');
}

if (isset($_GET['action'])) {
	$action = $_GET['action'];
	
    switch ($action){
    	case "store":
			$last_playlist_id = file_get_contents(CUR_FILE);
			$next_playlist_id = base60encode(base60decode($last_playlist_id) + 1);
						
			if (isset($_POST["playlistdata"])) {
				if (!file_exists(PL_DIR.'/'.$next_playlist_id)) {
					$data = base64_encode(htmlspecialchars($_POST["playlistdata"], ENT_NOQUOTES));



					$fp = fopen(PL_DIR.'/'.$next_playlist_id, 'w+');
					fwrite($fp, $data); 
					fclose($fp);
					
					unlink(CUR_FILE);
					$fp2 = fopen(CUR_FILE, 'w+');
					fwrite($fp2, (string) $next_playlist_id); 
					fclose($fp2);
					
					echo $next_playlist_id;
		   		} else {
		   			echo '{ "error": true, "code": 2 }';
		   		}
			} else {
				echo '{ "error": true, "code": 4 }';
			}
			break;

    	case "get":
    		if (isset($_GET['id'])) {
	    		$id = preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']);
		    	$filepath = PL_DIR.'/'.$id;
		    	
		  		if (file_exists($filepath)) {
					echo base64_decode(file_get_contents($filepath));
		   		} else {
		   			echo '{ "error": true, "code": 6 }';
		   		}
			} else {
				echo '{ "error": true, "code": 5 }';
			}
    		break;
    	default:
    		echo '{ "error": true, "code": 3 }';
    } 
} else {
	echo '{ "error": true, "code": 1 }';
}