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

    if (file_exists('./' . $path)) {
        unlink('./' . $path);
    }
    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR);
    }
    $fp = fopen('./' . $path, 'w+');
    fwrite($fp, $data);
    fclose($fp);
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
    preg_match_all('/[0-9]+[HMS]/', $duration, $matches);

    foreach ($matches as $match) {
        foreach ($match as $portion) {
            $unite = substr($portion, strlen($portion) - 1);

            switch ($unite) {
                case 'H':
                    return gmdate('i:s', substr($portion, 0, strlen($portion) - 1) * 60 * 60);

                case 'M':
                    return gmdate('i:s', substr($portion, 0, strlen($portion) - 1) * 60);

                default:
                    return gmdate('i:s', substr($portion, 0, strlen($portion) - 1));
            }
        }
    }
}

function ytget($query)
{
    $url = YT_API . $query . '&key=' . YT_KEY . '&maxResults=50';
    $result = json_decode(readcache($url));
    return $result;
}

function getversion()
{
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
