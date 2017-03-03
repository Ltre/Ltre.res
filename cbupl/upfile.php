<?php
$f = $_FILES['f'];
if (empty($f)) die('no file');
if (preg_match('/\.(php|html|phtml|php3|jsp|asp|htm|js|java|sh)/', $f['name'])) die('fuck you');
if (! is_uploaded_file($f['tmp_name'])) die('found upload attack..');
if (preg_match('/\\0|\:|\/|\\|\?|\^|\*|\<|\>|\$/', $f['name'])) die('illegal file name');
if ($f['size'] > 5242880*2) die('file size is greater than 10MB');
if (move_uploaded_file($f['tmp_name'], '/home/wwwroot/res.miku.us/res/other/'.$f['name'])) {
    echo 'http://res.miku.us/res/other/'.$f['name'];
} else {
    echo 'fail';
}
