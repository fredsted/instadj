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

function ytv3duration($duration){
	preg_match_all('/[0-9]+[HMS]/',$duration,$matches);
	$duration=0;
	foreach($matches as $match){
		//echo '<br> ========= <br>';
		//print_r($match);
		foreach($match as $portion){
			$unite=substr($portion,strlen($portion)-1);
			switch($unite){
				case 'H':{
					$duration +=    substr($portion,0,strlen($portion)-1)*60*60;
				}break;
				case 'M':{
					$duration +=substr($portion,0,strlen($portion)-1)*60;
				}break;
				case 'S':{
					$duration +=    substr($portion,0,strlen($portion)-1);
				}break;
			}
		}
		//  echo '<br> duratrion : '.$duration;
		//echo '<br> ========= <br>';
	}
	return $duration;

}