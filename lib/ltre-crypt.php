<?php
function ltreCrypt($str){
    $table = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\'');//@为%的替代符
    $v1 = str_replace('%', '@', urlencode($str));
    $v2 = array();
    foreach (str_split($v1) as $k => $v) {
        $rawPos = array_search($v, $table);
        $offset = floor(mt_rand(0, count($table)-$rawPos-1));
        $plusPos = $rawPos + $offset;
        $v2[] = $table[$offset];
        $v2[] = $table[$plusPos];
    }
    $v2 = implode('', array_reverse($v2));
    return $v2;
}


function ltreDeCrypt($str){
    $table = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\'');
    $rawList = array();
    $offsetList = array();
    foreach (array_reverse(str_split($str)) as $k => $v) {
        $pos = array_search($v, $table);
        if (intval(($k + 1) % 2) == 1) {
            $offsetList[] = $pos;
        } else {
            $rawPos = intval($pos - $offsetList[($k + 1) / 2 - 1]);
            @$rawList[] = $table[$rawPos];
        }
    }
    $raw = str_replace('@', '%', implode('', $rawList));
    return urldecode($raw);
}

