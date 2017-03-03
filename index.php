<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_STRICT);
require 'lib/ltre-crypt.php';
if ($_SERVER['HTTP_HOST'] == 'hx.res.miku.us') {

$prePath = '/home/wwwroot/res.miku.us/';
$file = $prePath.ltreDeCrypt($_SERVER['REQUEST_URI']);
//die($file);
readfile($file);
} else {
require '404.html';
}
