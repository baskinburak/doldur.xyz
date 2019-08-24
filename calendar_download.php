<?php

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=calendardata.ics');

$startdate = "2019-09-23"; # 23 sept 2019 semester start
$enddate = "2019-12-27"; # 27 dec 2019  semester end


$totalweeks = ceil(abs(strtotime($startdate) - strtotime($enddate)) / 60 / 60 / 24 / 7);
function create_event($start,$end,$name,$description,$location) {
	global $totalweeks;
	$event = "BEGIN:VEVENT\r\nDTSTART;TZID=Europe/Istanbul:".$start."\r\nDTEND;TZID=Europe/Istanbul:".$end."\r\nRRULE:FREQ=WEEKLY;COUNT=".(string)$totalweeks."\r\nLOCATION:".$location."\r\nUID:".uniqid()."\r\nDTSTAMP;TZID=Europe/Istanbul:".date("Ymd\THis")."\r\nSUMMARY:".$name."\r\nDESCRIPTION:".$description."\r\nEND:VEVENT\r\n";
	return $event;
}



$data = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nPRODID:-//doldur.xyz//METU Courses calendar//EN\r\nBEGIN:VTIMEZONE\r\nTZID:Europe/Istanbul\r\nTZURL:http://tzurl.org/zoneinfo-outlook/Europe/Istanbul\r\nX-LIC-LOCATION:Europe/Istanbul\r\nBEGIN:STANDARD\r\nTZOFFSETFROM:+0300\r\nTZOFFSETTO:+0300\r\nTZNAME:+03\r\nDTSTART:19700101T000000\r\nEND:STANDARD\r\nEND:VTIMEZONE\r\n";

foreach($_POST as $key => $value)
{
	
	$dtstart = explode(":", $_POST[$key]["dtstart"]);
	$stringst = '+'.$_POST[$key]["day"].' day '.$dtstart[0].' hour '.$dtstart[1].' minutes'; # 8.40
	$stringend = '+'.$_POST[$key]["day"].' day '.(string)(intval($dtstart[0]) + 1) .' hour '. (string)(intval($dtstart[1]) - 10) .' minutes'; # 9.30
	
	$data .= create_event(date("Ymd\THis", strtotime($startdate.$stringst)),date("Ymd\THis", strtotime($startdate.$stringend)),$_POST[$key]["name"],"This is an event made by doldur.xyz",$_POST[$key]["loc"]);
}


$data .= "END:VCALENDAR";

echo $data;


?>