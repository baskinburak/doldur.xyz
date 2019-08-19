
<?php
//die("err");
if(!isset($_GET['code'])) die("pls no");

$id = $_GET['code'];

$arr = array();
$file = fopen("/tmp/data", "r");
while($line = fgets($file)) {
	$arr[] = $line;
}

$rarr = array();
foreach($arr as $elem) {
	$rarr[] = unserialize($elem);
}

$resarr = array();
foreach($rarr as $elem) {
	foreach($elem as $item) {
		if(array_key_exists($item['cc'], $resarr)) {
			$resarr[$item['cc']]++;
		} else {
			 $resarr[$item['cc']] = 1;
		}
	}
}

ksort($resarr);

//print_r($resarr);


echo $id . " : " .  $resarr[$id] . "\n";

//echo 'logic: ' . $resarr[5710424] . "\n";

//echo 'image: ' . $resarr[5710466] . "\n";

//echo 'script: ' . $resarr[5710445] . "\n";
