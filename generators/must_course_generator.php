<?php
if(php_sapi_name() !== "cli") die();
$ch = null;
function curl_url_with_vars($url, $vars, $persistent = false) {
	global $ch;
	if(!$persistent)
		$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
	curl_setopt( $ch, CURLOPT_COOKIEJAR, "/tmp/cok" );
	curl_setopt( $ch, CURLOPT_COOKIEFILE, "/tmp/pom" );
	return curl_exec( $ch );
}

$url = 'https://oibs.metu.edu.tr/cgi-bin/View_Program_Details_58/View_Program_Details_58.cgi';
$vars = 'SubmitName=Submit&SaFormName=action_index__Findex_html';

$oibs58_raw = curl_url_with_vars($url, $vars);

$oibs58 = new DOMDocument();
$oibs58->loadHTML($oibs58_raw);

$oibs58_tables = $oibs58->getElementsByTagName("table");

$oibs58_data = $oibs58_tables->item(2);

$oibs58_rows = $oibs58_data->childNodes;

$codes_deptnames = array();

for($i=1; $i < $oibs58_rows->length; $i++) {
	$dept = $oibs58_rows->item($i);
	$cols = $dept->childNodes;
	$dept_string = $cols->item(8)->nodeValue;
	$dept_code = $cols->item(14)->nodeValue;
	if(!isset($codes_deptnames[$dept_code]))
		$codes_deptnames[$dept_code] = $dept_string;	
}
$depts_musts = array();
foreach($codes_deptnames as $code => $deptname) {
	if(!isset($depts_musts[$deptname])) {
		$depts_musts[$deptname] = array();
		$url = "https://oibs.metu.edu.tr/cgi-bin/View_Program_Details_58/View_Program_Details_58.cgi";
		$vars = "radio_program_code=$code&SubmitName=Must Courses&SaFormName=action_programs__FPrograms_html";
		$course_raw = curl_url_with_vars($url, $vars, true);
		if(strpos($course_raw, "There is no must course defined for this program.") === false) {
			$course_DD = new DOMDocument();
			$course_DD->loadHTML($course_raw);
			$data_table = $course_DD->getElementsByTagName("table")->item(5);
			$rows = $data_table->getElementsByTagName("tr");
			for($i=1; $i<$rows->length; $i++) {
				$current_row = $rows->item($i);
				$tds = $current_row->getElementsByTagName("td");
				//var_dump($tds->item(1));die();
				$set_no = (int)trim($tds->item(0)->nodeValue);
				$year_no = (int)trim($tds->item(5)->nodeValue);
				$course_name = trim($tds->item(2)->nodeValue);
				if($set_no != 0 || $year_no > 4 || stripos($course_name, "SUMMER PRACTICE") !== false)
					continue;
				$course_code = (int)trim($tds->item(1)->nodeValue);
				$sem_no = (int)trim($tds->item(4)->nodeValue);
				if(!isset($depts_musts[$deptname][$sem_no]))
					$depts_musts[$deptname][$sem_no] = array();
				$depts_musts[$deptname][$sem_no][] = $course_code;
			}
		}
	}
}

echo json_encode($depts_musts);
?>
