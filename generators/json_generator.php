<?php
if(php_sapi_name() !== "cli") die();
//ini_set('display_startup_errors',1);
//ini_set('display_errors',1);
//error_reporting(-1);
error_reporting(0);
@ini_set('display_errors', 0);
$ch = null;
function remove_cookies() {
	file_put_contents("/tmp/cok", "");
	file_put_contents("/tmp/pom", "");
}
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
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
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

$codes_deptnames[642]="TURK";$codes_deptnames[643]="THEA";$codes_deptnames[644]="SLTP";$codes_deptnames[651]="MUS";$codes_deptnames[682]="INST";$codes_deptnames[120]="ARCH";$codes_deptnames[121]="CRP";$codes_deptnames[125]="ID";$codes_deptnames[801]="AH";$codes_deptnames[852]="RP";$codes_deptnames[853]="CP";$codes_deptnames[854]="BS";$codes_deptnames[855]="UD";$codes_deptnames[856]="CONS";$codes_deptnames[857]="IDDI";$codes_deptnames[858]="ARCD";$codes_deptnames[219]="GENE";$codes_deptnames[230]="PHYS";$codes_deptnames[232]="SOC";$codes_deptnames[233]="PSY";$codes_deptnames[234]="CHEM";$codes_deptnames[236]="MATH";$codes_deptnames[238]="BIOL";$codes_deptnames[240]="HIST";$codes_deptnames[241]="PHIL";$codes_deptnames[246]="STAT";$codes_deptnames[817]="FPSY";$codes_deptnames[830]="IPSY";$codes_deptnames[836]="AET";$codes_deptnames[840]="SAN";$codes_deptnames[864]="ASTR";$codes_deptnames[310]="ADM";$codes_deptnames[311]="ECON";$codes_deptnames[312]="BA";$codes_deptnames[314]="IR";$codes_deptnames[315]="GIA";$codes_deptnames[316]="BAS";$codes_deptnames[837]="EMBA";$codes_deptnames[410]="ELE";$codes_deptnames[411]="ECE";$codes_deptnames[412]="ESE";$codes_deptnames[413]="EME";$codes_deptnames[420]="SSME";$codes_deptnames[421]="PHED";$codes_deptnames[422]="CHED";$codes_deptnames[430]="CEIT";$codes_deptnames[450]="FLE";$codes_deptnames[451]="TEFL";$codes_deptnames[453]="PES";$codes_deptnames[454]="EDS";$codes_deptnames[459]="BED";$codes_deptnames[820]="ELT";$codes_deptnames[821]="ELIT";$codes_deptnames[822]="ESME";$codes_deptnames[823]="COUN";$codes_deptnames[824]="EAP";$codes_deptnames[825]="CI";$codes_deptnames[834]="HRDE";$codes_deptnames[560]="ENVE";$codes_deptnames[561]="ES";$codes_deptnames[562]="CE";$codes_deptnames[563]="CHE";$codes_deptnames[564]="GEOE";$codes_deptnames[565]="MINE";$codes_deptnames[566]="PETE";$codes_deptnames[567]="EE";$codes_deptnames[568]="IE";$codes_deptnames[569]="ME";$codes_deptnames[570]="METE";$codes_deptnames[571]="CENG";$codes_deptnames[572]="AEE";$codes_deptnames[573]="FDE";$codes_deptnames[866]="EM";$codes_deptnames[867]="SE";$codes_deptnames[868]="ST";$codes_deptnames[869]="HE";$codes_deptnames[876]="MDM";$codes_deptnames[884]="ENVM";$codes_deptnames[601]="BASE";$codes_deptnames[602]="ARAB";$codes_deptnames[603]="FREN";$codes_deptnames[604]="GERM";$codes_deptnames[605]="JA";$codes_deptnames[606]="ITAL";$codes_deptnames[607]="RUS";$codes_deptnames[608]="SPAN";$codes_deptnames[609]="HEB";$codes_deptnames[610]="GRE";$codes_deptnames[611]="CHN";$codes_deptnames[629]="TFL";$codes_deptnames[639]="ENG";$codes_deptnames[790]="TPR";$codes_deptnames[791]="AUTO";$codes_deptnames[792]="WELD";$codes_deptnames[795]="TKPR";$codes_deptnames[796]="FDTE";$codes_deptnames[797]="ELEK";$codes_deptnames[798]="ENEL";$codes_deptnames[799]="ENOT";$codes_deptnames[970]="IAM";$codes_deptnames[971]="CRYP";$codes_deptnames[972]="SC";$codes_deptnames[973]="FM";$codes_deptnames[901]="IS";$codes_deptnames[902]="COGS";$codes_deptnames[903]="MS";$codes_deptnames[904]="ION";$codes_deptnames[905]="SM";$codes_deptnames[906]="MI";$codes_deptnames[907]="WBLS";$codes_deptnames[908]="BIN";$codes_deptnames[909]="GATE";$codes_deptnames[910]="CSEC";$codes_deptnames[950]="MASC";$codes_deptnames[951]="PHOC";$codes_deptnames[952]="CHOC";$codes_deptnames[953]="MGEO";$codes_deptnames[954]="MBIO";$codes_deptnames[956]="OCEA";$codes_deptnames[860]="BCH";$codes_deptnames[861]="BTEC";$codes_deptnames[862]="PST";$codes_deptnames[863]="ARME";$codes_deptnames[865]="GGIT";$codes_deptnames[870]="CEME";$codes_deptnames[871]="MNT";$codes_deptnames[872]="BME";$codes_deptnames[873]="EQS";$codes_deptnames[874]="ESS";$codes_deptnames[877]="OHS";$codes_deptnames[878]="NSNT";$codes_deptnames[880]="OR";$codes_deptnames[800]="ISS";$codes_deptnames[810]="GWS";$codes_deptnames[811]="UPL";$codes_deptnames[814]="SA";$codes_deptnames[815]="ARS";$codes_deptnames[816]="MCS";$codes_deptnames[831]="STPS";$codes_deptnames[832]="MES";$codes_deptnames[833]="EUS";$codes_deptnames[835]="EAS";$codes_deptnames[838]="EI";$codes_deptnames[839]="SPL";$codes_deptnames[841]="GTSS";$codes_deptnames[842]="ASN";$codes_deptnames[843]="LNA";$codes_deptnames[355]="CNG";$codes_deptnames[356]="EEE";$codes_deptnames[357]="MAT";$codes_deptnames[358]="PHY";$codes_deptnames[360]="CHM";$codes_deptnames[363]="STAS";$codes_deptnames[364]="CVE";$codes_deptnames[365]="MECH";$codes_deptnames[367]="CHME";$codes_deptnames[373]="BIO";$codes_deptnames[374]="PNGE";$codes_deptnames[379]="ARC";$codes_deptnames[382]="ENV";$codes_deptnames[383]="ESC";$codes_deptnames[384]="ASE";$codes_deptnames[386]="IDS";$codes_deptnames[359]="ENGL";$codes_deptnames[369]="GRM";$codes_deptnames[370]="FRN";$codes_deptnames[380]="CHIN";$codes_deptnames[385]="SPN";$codes_deptnames[399]="ENGP";$codes_deptnames[351]="BUSD";$codes_deptnames[352]="ECO";$codes_deptnames[353]="BUS";$codes_deptnames[354]="PSIR";$codes_deptnames[361]="TUR";$codes_deptnames[362]="HST";$codes_deptnames[366]="EFL";$codes_deptnames[368]="EDUS";$codes_deptnames[371]="PSYC";$codes_deptnames[372]="SOCL";$codes_deptnames[375]="ART";$codes_deptnames[376]="CTE";$codes_deptnames[377]="PHL";$codes_deptnames[378]="GPC";$codes_deptnames[381]="TEA";$codes_deptnames[390]="SEES";$codes_deptnames[391]="ENLT";

$oibs64_raw = file_get_contents("https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php");

$oibs64 = new DOMDocument();
$oibs64->loadHTML($oibs64_raw);
$selects = $oibs64->getElementsByTagName("select");
//var_dump($selects);

$select_semester_option = $selects->item(1)->childNodes->item(0);
$select_semester = NULL; /// to post
foreach($select_semester_option->attributes as $attr) {
	if($attr->nodeName === "value") {
		$select_semester = $attr->nodeValue;
		break;
	}
}

echo $select_semester . "\n";

$dept_select = $selects->item(0);
$dept_options = $dept_select->childNodes;
$all_courses = array();
for($i=0; $i<$dept_options->length; $i++) {
	echo $i+1 . "/"; //. $dept_options->length;
	//echo "\n";
	flush();
	$current_dept_option_node = $dept_options->item($i);
	$patch_string = "";
	if(strpos($current_dept_option_node->nodeValue,"Kamp") !== false) $patch_string = " - Northern Cyprus Campus";
	$current_dept_option_val = NULL;
	foreach($current_dept_option_node->attributes as $attr) {
		if($attr->nodeName === "value") {
			$current_dept_option_val = $attr->nodeValue;
			echo $current_dept_option_node->nodeValue . "\n";
			flush();
			break;
		}
	}
	$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
	$vars = "select_dept=$current_dept_option_val&select_semester=$select_semester&submit_CourseList=Submit&textWithoutThesis=1&hidden_redir=Login";
	remove_cookies();
	$raw_data = curl_url_with_vars($url, $vars);
	if(strpos($raw_data, "Information about the department could not be found.") !== false)
		continue;
	$domdoc = new DOMDocument();
	$domdoc->loadHTML($raw_data);
	$course_table = $domdoc->getElementsByTagName("table")->item(3);
	$courses = $course_table->childNodes;
	for($j=1; $j<$courses->length; $j++) {
		//echo "\t" . $j . "/" . $courses->length . "\n";
		$c_course_array = array();
		$current_course = $courses->item($j);
		$columns = $current_course->childNodes;
		$course_code = (int)trim($columns->item(2)->nodeValue);
		$c_course_array["c"] = $course_code;
		$course_name = preg_replace("!\s+!", " ", $columns->item(4)->nodeValue);
		$course_name = trim(preg_replace("!\(\s*\)!", "", $course_name), ' ');
		echo "\t" . $course_name . "\n";
		flush();
		$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
		$vars = "SubmitCourseInfo=Course Info&text_course_code=$course_code&hidden_redir=Course_List";
		$course_info = curl_url_with_vars($url, $vars, true);
		while(strpos($course_info, "Your session has expired") !== false) {
			echo "here\n";flush();
			file_get_contents("https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php");
			$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
			$vars = "select_dept=$current_dept_option_val&select_semester=$select_semester&submit_CourseList=Submit&textWithoutThesis=1&hidden_redir=Login";
  			remove_cookies();
        		curl_url_with_vars($url, $vars);
			$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
	                $vars = "SubmitCourseInfo=Course Info&text_course_code=$course_code&hidden_redir=Course_List";
	                $course_info = curl_url_with_vars($url, $vars, true);
		}
		$course_dept_code = substr($course_code, 0, 3);
		$course_indept_code = null;
		//echo "\t\t" . ((string)$course_code)[3] . "\n";flush();
		$tostrrr = (string)$course_code;
		if($tostrrr[3] === '0') {
			$course_indept_code = substr($course_code, 4);
		} else {
			$course_indept_code = substr($course_code, 3);
		}
		//echo "\t\t" . $course_indept_code . "\n";flush();
		$c_course_array["n"] = $codes_deptnames[$course_dept_code].$course_indept_code ." - ". $course_name . $patch_string;
		$lololo = $course_info;
		$course_info_domdoc = new DOMDocument();
		$course_info_domdoc->loadHTML($course_info);
		$course_info = $course_info_domdoc;
		$c_course_table = $course_info->getElementsByTagName("table")->item(2);
		$c_rows = $c_course_table->childNodes;
		$instructors = array();
		$times = array();
		$section_numb = null;
		$c_course_array["s"] = array();
		for($k = 2; $k < $c_rows->length; $k++) {
			$current_row = $c_rows->item($k);
			if($k % 2 == 0) {
				//instructor info
				$children = $current_row->childNodes;
				$section_inp = $children->item(0);
				$section_numb = $section_inp->getElementsByTagName("input")->item(0)->getAttribute("value");
				$instructors[] = trim($children->item(2)->nodeValue, ' ');
				$instructors[] = trim($children->item(4)->nodeValue, ' ');
				
			} else {
				// time info
				$children = $current_row->getElementsByTagName("tr");
				for($p=0; $p<$children->length; $p++) {
					$td_children = $children->item($p)->childNodes;
					if(strlen($td_children->item(0)->nodeValue) === 0) {
						continue;
					}
					$day = $td_children->item(0)->nodeValue;
					$start = $td_children->item(2)->nodeValue;
					$end = $td_children->item(4)->nodeValue;
					$place = $td_children -> item(6)->nodeValue;
					$time_array = array();
					$str_val = null;
					if(stripos($day, 'mon') !== false) {
						$str_val = 'mon';
					} else if (stripos($day, 'tue') !== false) {
						$str_val = 'tue';
					} else if (stripos($day, 'wed') !== false) {
						$str_val = 'wed';
					} else if (stripos($day, 'thu') !== false) {
						$str_val = 'thu';
					} else if (stripos($day, 'fri') !== false) {
						$str_val = 'fri';
					} else {
						continue;
					}
					$start = explode(':', $start)[0];
					$end = explode(':', $end)[0];
					for($lo = $start; $lo < $end; $lo++) {
						$times[] = array("b" => $str_val . '-' . ($lo-7), "p" => $place); 
					}
				}


				$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
				$vars = "submit_section=$section_numb&hidden_redir=Course_Info";
				$inf = curl_url_with_vars($url, $vars, true);
				while(strpos($inf, "Your session has expired") !== false) {
					echo "here\n";flush();
					file_get_contents("https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php");
					 $url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
		                        $vars = "select_dept=$current_dept_option_val&select_semester=$select_semester&submit_CourseList=Submit&textWithoutThesis=1&hidden_redir=Login";
                		        remove_cookies();
                     		   curl_url_with_vars($url, $vars);
     		                   $url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
             		           $vars = "SubmitCourseInfo=Course Info&text_course_code=$course_code&hidden_redir=Course_List";
                     			  	curl_url_with_vars($url, $vars, true);
					$url = "https://oibs2.metu.edu.tr/View_Program_Course_Details_64/main.php";
                             		   $vars = "submit_section=$section_numb&hidden_redir=Course_Info";
                            		    $inf = curl_url_with_vars($url, $vars, true);
				}

				$given_dept = "ALL";
				$start_surname = "AA";
				$end_surname = "ZZ";
				$constraint_arr = array();
				if(stripos($inf, "There is no section criteria to take the selected courses for this section.") === false) {
					$constraints = new DOMDocument();
					$constraints->loadHTML($inf);
					$const_table = $constraints->getElementsByTagName("table")->item(2);
					$data_rows = $const_table->childNodes;
					for($li = 1; $li<$data_rows->length; $li++) {
						$data_row = $data_rows->item($li);
						$given_dept = trim($data_row->childNodes->item(0)->nodeValue, ' ');
						$start_surname = trim($data_row->childNodes->item(2)->nodeValue, ' ');
						$end_surname = trim($data_row->childNodes->item(4)->nodeValue, ' ');
						$constraint_arr[] = array("ss" => $start_surname, "es" => $end_surname, "gd" => $given_dept);
					}
				} else {
					$constraint_arr[] = array("ss" => $start_surname, "es" => $end_surname, "gd" => $given_dept);
				}
/*				if(!is_array($c_course_array["s"])) {
					$c_course_array["s"] = array();
				}
*/				$c_course_array["s"][] = array("sn" => $section_numb, "i" => $instructors, "t" => $times, "cs" => $constraint_arr);
				$section_numb = null;
				$instructors = array();
				$times = array();
			}
		}
		if(count($c_course_array["s"]) === 0) {
			echo $lololo . "\n";
			flush();
		}
		$all_courses[] = $c_course_array;
	}
}

function compare_courses($a, $b) {
	return strcmp($a['n'], $b['n']);
}

usort($all_courses, "compare_courses");

$f = fopen("data.json", "w") or die("Unable to open file");
fwrite($f, json_encode(array_values($all_courses)));
fclose($f);

?>
