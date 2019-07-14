<?php

function dump($item)
{
    if (PHP_SAPI != 'cli') echo '<pre style="background:#CCC;padding:5px;display:block;">';
    print_r($item);
    echo PHP_EOL;
    if (PHP_SAPI != 'cli') echo '</pre>';
}

function qs($which, $default = '')
{
    if (isset($_GET[$which])) {
        if ($_GET[$which] !== '') {
            return urlencode($_GET[$which]);
        } else {
            return $default;
        }
    }
}

function writeUrlCached($path, $URL)
{
    $data = file_get_contents($URL);

    setCacheValue($path, $data);

    return $data;
}

function getUrlCached($URL)
{
    $filepath = 'cache/' . md5($URL) . '-v3.xml';

    $value = getCacheValue($filepath);

    if ($value === false) {
        $value = writeUrlCached($filepath, $URL);
    }

    return $value;
}

function ytGet($query)
{
    $url = YT_API . $query . '&key=' . YT_KEY . '&maxResults=50';
    if (isset($_GET['next_page'])) {
        $url .= '&pageToken=' . ($_GET['next_page']);
    }

    $result = json_decode(getUrlCached($url));
    return $result;
}

function ytDuration($duration)
{
    preg_match_all('/[0-9]+[HMS]/', $duration, $matches);

    foreach ($matches as $match) {
        foreach ($match as $portion) {
            $unite = substr($portion, strlen($portion) - 1);

            switch ($unite) {
                case 'H':
                    return substr($portion, 0, strlen($portion) - 1) * 60 * 60;

                case 'M':
                    return substr($portion, 0, strlen($portion) - 1) * 60;

                default:
                    return substr($portion, 0, strlen($portion) - 1);
            }
        }
    }
}

/** @return Redis */
function redis() {
    static $redis;

    if (!$redis) {
        $redis = new Redis;
        $redis->connect(REDIS_HOST, REDIS_PORT);
    }

    return $redis;
}

function getCacheValue($key) {
    return redis()->get(REDIS_PREFIX.$key);
}

function setCacheValue($key, $value) {
    return redis()->setex(REDIS_PREFIX.$key, 3600*24, $value);
}

function json($array, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($array);
    exit;
}

function plain($string, $status = 200) {
    header('Content-Type: text/plain');
    http_response_code($status);
    echo $string;
    exit;
}