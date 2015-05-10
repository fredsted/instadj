<?php
	
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

	if (file_exists('./'.$path)) {
		unlink('./'.$path);
	}
	if (!is_dir('./cache')) {
		mkdir('./cache');
	}
	$fp = fopen('./'.$path, 'w+'); 
	fwrite($fp, $data); 
	fclose($fp);
}

function readcache($URL) {
	$filepath = 'cache/'.md5($URL).'-v3.xml';
	
	if (file_exists($filepath)) {
		if (filemtime($filepath) < time() - 3600*24) {  // 24hrs
			writecache($filepath, $URL);
		}
	} else {
		writecache($filepath, $URL);
	}
	
	return file_get_contents($filepath);
}