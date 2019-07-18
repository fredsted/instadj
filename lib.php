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
    if (!is_dir('./cache')) {
        mkdir('./cache');
    }
    $fp = fopen('./' . $path, 'w+');
    fwrite($fp, $data);
    fclose($fp);
}

function readcache($URL)
{
    $filepath = 'cache/' . md5($URL) . '-v3.xml';

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

function getversion()
{
    $versionFile = __DIR__ . '/version.txt';
    if (file_exists($versionFile)) {
        return substr(trim(file_get_contents($versionFile)), 0, 10);
    }
    return time();
}