<?php

function dump(...$items)
{
    foreach ($items as $item) {
        $lines = explode(PHP_EOL, print_r($item, true));
        echo '<details style="background-color: #CCC; padding: 5px">
                <summary>' . $lines[0] . '</summary>
                <pre>';
        print_r($item);
        echo '</pre></details>';
    }
}

function dd(...$items)
{
    dump($items);
    die();
}

function qs($which, $default = '')
{
    if (isset($_GET[$which]) && $_GET[$which] !== '') {
        return urlencode($_GET[$which]);
    }

    return $default;
}

function writecache($path, $URL)
{
    $data = file_get_contents($URL);

    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR);
    }

    file_put_contents($path, $data);

    return $data;
}

function readcache($URL)
{
    $filepath = CACHE_DIR . '/' . md5($URL) . '-v3.xml';

    if (file_exists($filepath)) {
        if (filemtime($filepath) < time() - 3600 * 24) {  // 24hrs
            writecache($filepath, $URL);
        }
    } else {
        writecache($filepath, $URL);
    }

    return file_get_contents($filepath);
}

function ytv3duration($duration)
{
    $start = new DateTime('@0'); // Unix epoch
    $start->add(new DateInterval($duration));

    if ($start->diff(new DateTime('@0'))->h > 0) {
        return $start->format('H:i:s');
    }
    return $start->format('i:s');
}

function ytget($query)
{
    $url = YT_API . $query . '&key=' . YT_KEY . '&maxResults=50';
    $result = json_decode(readcache($url));
    return $result;
}

function getversion(string $file = null)
{
	if (file_exists($file)) {
		return md5_file($file);
	}

    $versionFile = __DIR__ . '/version.txt';
    if (file_exists($versionFile)) {
        return substr(trim(file_get_contents($versionFile)), 0, 10);
    }
    return time();
}

function getPlaylistId()
{
    if (isset($_GET['id'])) {
        return strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $_GET['id']));
    }

    if (preg_match('/^(\/)([a-z0-9]+)$/', $_SERVER['REQUEST_URI'], $matches) === 1) {
        if (isset($matches[2])) {
            return $matches[2];
        }
    }
    return '';
}

function base33encode($iInteger)
{
    $aMap = base33map();
    $sString = '';
    while ($iInteger > 0) {
        $sString .= (string)$aMap[$iInteger % count($aMap)];
        $iInteger = floor($iInteger / count($aMap));
    }
    return $sString;
}

function base33decode($sString)
{
    $aFlippedMap = array_flip(
        base33map()
    );
    $iInteger = 0;
    for ($iCurrentPosition = 0; $iCurrentPosition < strlen($sString); $iCurrentPosition++) {
        $iInteger += $aFlippedMap[$sString[$iCurrentPosition]] * pow(count($aFlippedMap), $iCurrentPosition);
    }
    return $iInteger;
}

function base33map()
{
    return array(
        'a', 'b', 'c',
        'd', 'e', 'f',
        'g', 'h', 'i',
        'j', 'k', 'm',
        'n', 'o', 'p',
        'q', 'r', 's',
        't', 'u', 'v',
        'w', 'x', 'y',
        'z', '2', '3',
        '4', '5', '6',
        '7', '8', '9',
    );
}

// @link https://gist.github.com/RadGH/84edff0cc81e6326029c
function number_format_short( $n, $precision = 1 ) {
	if ($n < 900) {
		// 0 - 900
		$n_format = number_format($n, $precision);
		$suffix = '';
	} else if ($n < 900000) {
		// 0.9k-850k
		$n_format = number_format($n / 1000, $precision);
		$suffix = 'K';
	} else if ($n < 900000000) {
		// 0.9m-850m
		$n_format = number_format($n / 1000000, $precision);
		$suffix = 'M';
	} else if ($n < 900000000000) {
		// 0.9b-850b
		$n_format = number_format($n / 1000000000, $precision);
		$suffix = 'B';
	} else {
		// 0.9t+
		$n_format = number_format($n / 1000000000000, $precision);
		$suffix = 'T';
	}

	// Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	// Intentionally does not affect partials, eg "1.50" -> "1.50"
	if ( $precision > 0 ) {
		$dotzero = '.' . str_repeat( '0', $precision );
		$n_format = str_replace( $dotzero, '', $n_format );
	}

	return $n_format . $suffix;
}