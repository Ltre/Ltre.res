<?php
foreach (glob('*') as $f){
	if ($f=='index.php') continue;
	echo '<a href="http://'.rtrim($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '/').'/'.$f.'" target="_blank">'.$f.'</a><br>';
}
