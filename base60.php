<?php

function base60encode($iInteger)
{
    $aMap = getMap();
    $sString = '';
    while ($iInteger > 0) {
        $sString .= (string)$aMap[$iInteger % count($aMap)];
        $iInteger = floor($iInteger / count($aMap));
    }
    return $sString;
}

function base60decode($sString)
{
    $aFlippedMap = array_flip(
        getMap()
    );
    $iInteger = 0;
    for ($iCurrentPosition = 0; $iCurrentPosition < strlen($sString); $iCurrentPosition++) {
        $iInteger += $aFlippedMap[$sString[$iCurrentPosition]] * pow(count($aFlippedMap), $iCurrentPosition);
    }
    return $iInteger;
}

function getMap()
{
    return array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'm', 'n', 'o', 'p',
        'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '2', '3',
        '4', '5', '6', '7', '8', '9'
    );
}

?>