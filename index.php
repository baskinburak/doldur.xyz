
<?php
echo "all good things must end.<br> reach me if you want to buy the domain: basbursen@gmail.com <br><img src='https://i.pinimg.com/originals/09/ac/14/09ac14ffeb1878670c64cfd21a7371f7.jpg'/><br> Maybe when other good things end";
die();
 @session_start();
date_default_timezone_set('Europe/Istanbul');
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);
	if(!isset($_GET['action'])) {
		$_GET['action'] = 'new';
	}
	$actions = array("new", "show");
	if(!in_array($_GET['action'], $actions)) die();
	$action = $_GET['action'];
	$user_logged_in = false;
	$uname = null;
	if($action == 'show') {
		$user_logged_in = true;
		if(!isset($_GET['name']) || empty($_GET['name'])) die("badboy");
		$uname = $_GET['name'];
	}

	$editname = null;
	if($_GET['action'] === 'new' && isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['name']) && !empty($_POST['name'])) {
		$editname = (string)$_POST['name'];
	}
	$eee = true;
	function print_show_page() {
	?>
		<div class="row">
			<div class="large-12 small-12 columns">
		<?php
					global $uname;
					require_once("db.php");
					$prepared = $pdo->prepare("SELECT data,owndata FROM users WHERE username = :un");
					$prepared->bindValue(':un', $uname);
					$prepared->execute();
					$data = $prepared->fetchAll(PDO::FETCH_ASSOC);
					if(count($data)==0) {
						echo "Username does not exist.</div></div>";
						$eee = false;
						return;
					}
					$owndata = unserialize($data[0]['owndata']);
					$data = unserialize($data[0]['data']);
					$course_data = json_decode(file_get_contents('./inc/data.json'), true);
					$block_array = array();
					$colors = array("#CC0000", "#7A00CC", "#29A329", "#CCCC00",  "#00CCCC", "#00008A", "#002900", "#E62EB8", "#005C5C", "#CC3300", "#808080", "#00FF00", "#666633", "#002E2E");
					$color_idx = 0;
					for($i=0; $i<count($data); $i++) {
						$cur = $data[$i];
						$course_code = (int)$cur['cc'];
						$section_numb = (int)$cur['sn'];
						$ccd = null;
						$csd = null;
						for($j = 0; $j < count($course_data); $j++) {
							if($course_code == $course_data[$j]['c']) {
								$ccd = $course_data[$j];
								for($p=0; $p<count($ccd["s"]); $p++) {
									$i_val = (int)$ccd["s"][$p]["sn"];
									if($i_val == $section_numb) {
										$csd = $ccd["s"][$p];
										break;
									}
								}
								break;
							}
						}
						if($ccd === null || $csd === null) {
							continue;
						}
						$color = $colors[$color_idx % count($colors)];
						$color_idx++;
						$inner = trim(explode('-', $ccd['n'])[0]) . " - " . $section_numb . " (";
						foreach($csd["t"] as $time) {
							$block_id = $time["b"];
							if(!isset($block_array[$block_id])) {
								$block_array[$block_id] = array();
							}
							$block_array[$block_id][] = "<div class=\"lecture-block\" style=\"background-color:$color\">$inner".$time["p"].")</div>";
						}
					}
					foreach($owndata as $key=>$value) {
						foreach($value as $str) {
							$block_array[$key][] = "<div class=\"own-block\">".htmlspecialchars($str)."</div>";
						}
					}
					$days = array("mon", "tue", "wed", "thu", "fri");
					$row_idx = 1;
					?>
						<div style="text-align:center; margin: 10px 0"><a href="http://<?php echo $_SERVER['HTTP_HOST']; echo $_SERVER['REQUEST_URI'];?>">http://<?php echo $_SERVER['HTTP_HOST']; echo $_SERVER['REQUEST_URI'];?></a></div>
						<form id="calendar" method="post" action="/calendar_download.php"><table id="schedule-table"><thead><tr><th>Hours</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr></thead><tbody>
					<?php
						for(; $row_idx<=9; $row_idx++) {
							echo "<tr>";
							echo "<td>";
							echo $row_idx+7;
							echo ":40</td>";
							for($d=0; $d<5; $d++) {
								$id = $days[$d]."-".$row_idx;
								echo "<td id=\"$id\">";
								if(!isset($block_array[$id])) {
									$block_array[$id] = array();
								}
								for($p=0; $p<count($block_array[$id]); $p++) {
									echo $block_array[$id][$p];
								}
								echo "</td>";
							}


							echo "</tr>";
						}
					?>
						</tbody></table></form>
					<?php
		?>
			</div>
		</div>
	<?php
	}

	function print_new_page() {
		?>
			<div class="row">
				<div class="large-12 columns" style="text-align:center">
					<div id = "musts-loading-l">
						Loading must courses...
					</div>
					<div id="course-data-loading-l">
						Loading course data...
					</div>
				</div>
			</div>
			<div style="max-width:80rem" class="row">
				<div class="large-8 small-12 columns">
					<div style="text-align:center">
						<ul class="button-group round sc-possibility-nav">
							<li style="float:left"><a href="#" id="sc-possibility-prev" class="button secondary tiny">Previous</a></li>
							<li id="possibility-status">0/0</li>
							<li style="float:right"><a href="#" id="sc-possibility-next" class="button secondary tiny">Next</a></li>
						</ul>
					</div>
					<table id="schedule-table"><thead><tr><th>Hours</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr></thead><tbody><tr><td>8:40</td><td id="mon-1"></td><td id="tue-1"></td><td id="wed-1"></td><td id="thu-1"></td><td id="fri-1"></td></tr><tr><td>9:40</td><td id="mon-2"></td><td id="tue-2"></td><td id="wed-2"></td><td id="thu-2"></td><td id="fri-2"></td></tr><tr><td>10:40</td><td id="mon-3"></td><td id="tue-3"></td><td id="wed-3"></td><td id="thu-3"></td><td id="fri-3"></td></tr><tr><td>11:40</td><td id="mon-4"></td><td id="tue-4"></td><td id="wed-4"></td><td id="thu-4"></td><td id="fri-4"></td></tr><tr><td>12:40</td><td id="mon-5"></td><td id="tue-5"></td><td id="wed-5"></td><td id="thu-5"></td><td id="fri-5"></td></tr><tr><td>13:40</td><td id="mon-6"></td><td id="tue-6"></td><td id="wed-6"></td><td id="thu-6"></td><td id="fri-6"></td></tr><tr><td>14:40</td><td id="mon-7"></td><td id="tue-7"></td><td id="wed-7"></td><td id="thu-7"></td><td id="fri-7"></td></tr><tr><td>15:40</td><td id="mon-8"></td><td id="tue-8"></td><td id="wed-8"></td><td id="thu-8"></td><td id="fri-8"></td></tr><tr><td>16:40</td><td id="mon-9"></td><td id="tue-9"></td><td id="wed-9"></td><td id="thu-9"></td><td id="fri-9"></td></tr></tbody></table>
					<a href="#" id="add-your-own" class="button tiny expand">Add your own thing (Currently no collision check for these things)</a>
					<div id="your-own-thing">
						<form>
							<div class="row">
								<div class="small-12 large-4 columns">
									<label>Text
										<input type="text" id="own-text" placeholder="" />
									</label>
								</div>
								<div class="small-12 large-3 columns">
									<label>Day
										<select id="own-day">
											<option value="mon">Monday</option>
											<option value="tue">Tuesday</option>
											<option value="wed">Wednesday</option>
											<option value="thu">Thursday</option>
											<option value="fri">Friday</option>
										</select>
									</label>
								</div>
								<div class="small-12 large-2 columns">
									<label>Time
										<select id="own-time">
											<option value="1">08:40</option>
											<option value="2">09:40</option>
											<option value="3">10:40</option>
											<option value="4">11:40</option>
											<option value="5">12:40</option>
											<option value="6">13:40</option>
											<option value="7">14:40</option>
											<option value="8">15:40</option>
											<option value="9">16:40</option>
										</select>
									</label>
								</div>
								<div class="small-12 large-3 columns">
									<label><br/>
										<a type="submit" href="#" id="add-confirm" class="button tiny">ADD</a>
									</label>
								</div>
							</div>
						</form>
					</div>
					<input type="text" id="course-name" placeholder="Enter course name"/>
				</div>
				<div class="large-4 columns">
					<form>
						<div class="row">
							<div class="small-12 large-4 columns">
								<label>Surname
									<input type="text" id="surname" placeholder="2 letters" />
								</label>
							</div>
							<div class="small-12 large-8 columns">
								<label>Dept Name
									<input type="text" id="dept-code" placeholder="ceng,aee,adm etc." />
								</label>
							</div>
						</div>
						<div class="row">
							<div class="small-12 large-8 columns">
								<label>Semester No.
									<input type="text" id="semester" placeholder="3rd year 1st term = 5 for example" />
								</label>
							</div>
							<div class="small-12 large-4 columns">
									<label>
									<a href="#" id="add-must-courses" class="button tiny">Add MUST courses</a>
									</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
									<input type="checkbox" id="surname-constraint" placeholder="2 letters" /><label for="surname-constraint">Disable surname constraints.</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
									<input type="checkbox" id="dept-constraint" placeholder="2 letters" /><label for="dept-constraint">Disable department constraints.</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
									<input type="checkbox" id="collision-constraint" placeholder="2 letters" /><label for="collision-constraint">Disable collision check.</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
									<input type="checkbox" id="lunch-time" placeholder="2 letters" /><label for="lunch-time">Try to reserve lunch time.</label>
							</div>
						</div>
						<div class="row" id="lunch-time-importance">
							<div class="large-12 columns end">
								<div id="lunch-imp" class="range-slider" data-slider data-options="step: 1;start: 1; end: 100; initial: 50;">
									<span class="range-slider-handle" role="slider" tabindex="0"></span>
									<span class="range-slider-active-segment"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
									<input type="checkbox" id="gather-courses" placeholder="2 letters" /><label for="gather-courses">Try to gather courses. (Blocks)</label>
							</div>
						</div>
						<div class="row" id="gather-block-importance">
							<div class="large-12 columns end">
								<div id="gather-imp" class="range-slider" data-slider data-options="step: 1;start: 1; end: 100; initial: 50;">
									<span class="range-slider-handle" role="slider" tabindex="1"></span>
									<span class="range-slider-active-segment"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<a href="" id="initiate-schedule" class="button expand">Schedule</a>
							</div>
						</div>
							<div class="row">
							<div class="large-12 columns">
								<input form="calendar " type="submit" class="button expand">Export as Calendar</a>
							</div>
						</div>
					</form>
					<form id = "form-course">
						<div class="row" style="margin-bottom:10px">
							<div class="large-12 columns">
								<span id="form-course-name"></span>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<input type="submit" id="toggle-course-info" class="button info expand" value="Course Info" />
							</div>
						</div>
						<div class="row" id="course-info-to-reveal">
							<div class="large-12 columns">
								<span id="course-info-content">
								</span>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								Choose sections
								<div id="toggle-sections">
									Toggle
								</div>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<span id="section-selector">
								</span>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<input type="checkbox" id="surname-constraint-current-course" placeholder="2 letters" /><label for="surname-constraint-current-course">Disable surname constraint for this course.</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<input type="checkbox" id="dept-constraint-current-course" placeholder="2 letters" /><label for="dept-constraint-current-course">Disable dept. constraint for this course.</label>
							</div>
						</div>
						<div class="row">
							<div class="large-12 columns">
								<input style="margin-bottom:0; display:none" type="submit" id="apply-options" class="button success expand" value="Apply options" />
							</div>
						</div>
					</form>
				</div>
			</div>
                        <div class="row" style="margin-bottom:15px; font-size:11px; text-decoration:italic;max-width:80rem">
                                <div class="large-12 columns">
                                        Course database last updated: <?php echo date ("d F Y H:i:s.", filemtime(getcwd()."/inc/data.json")); ?> Summer School 2018-2019
                                </div>
                        </div>
		<?php
	}

	function print_page() {
		global $action;
		switch($action) {
			case 'new':
				print_new_page();
				break;
			case 'show':
				print_show_page();
				break;
		}
	}
	
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Scheduler for METU courses.">
	<meta name="keywords" content="METU,ODTÜ,schedule,scheduler">
	<title>doldur.xyz</title>
	<link rel="stylesheet" href="/inc/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/inc/foundation/css/normalize.css">
	<link rel="stylesheet" href="/inc/foundation/css/foundation.min.css">
	<link rel="stylesheet" href="/inc/style.css">
	<link rel="icon" type="image/png" href="/inc/img/fav.png">
	<script src="/inc/foundation/js/vendor/modernizr.js"></script>
</head>
<body>
<nav class="top-bar sc-nav" data-topbar role="navigation">
  <ul class="title-area">
    <li class="name">
      <h1><a href="./">doldur.xyz</a></h1>
    </li>
    <li class="toggle-topbar"><a href="#"><span>Menu</span></a></li>
  </ul>

  <section class="top-bar-section">
    <ul class="left">
			<?php
				if(!$user_logged_in) {
			?>
      		<li class="active"><a href="#" data-reveal-id="save-modal">Save the schedule</a></li>
		<li class="active" style="border-left:1px solid #000000"><a href="#" data-reveal-id="forgot-modal">Forgot password</a></li>
					<li class=""><a href="#" data-reveal-id="about-modal">About</a></li>
			<?php
				} else {
			?>
				<li class="active"><a href="#" id="edit-schedule">Edit the schedule</a></li>
			<?php
				}
			?>
    </ul>
		<ul class="right">
			<div style="text-align:center; display:inline-block; color: white; background-color:#E44424;margin-right:10px;margin-top:5px;padding:3px 10px;">
			Try clicking table cells!
			</div>
		</ul>
    <!-- Left Nav Section -->
  </section>
</nav>
<div>
<?php print_page(); ?>
</div>
	<?php
		if(!$user_logged_in) {
	?>
		<div id="save-modal" class="reveal-modal tiny" data-reveal aria-labelledby="Login" aria-hidden="true" role="dialog">
			<form>
				<div class="row">
					<div class="small-12 large-12 columns">
						<div id="status-message">
						</div>
						If you already have an account, current schedule will replace the old one.<br/><br/>
						If you don't have account, a new account will be created.<br/><br/>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						<label>Email
						  <input id="em" type="email" name="email" placeholder="Your email address" />
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						<label>Username
						  <input id="un" type="text" name="username" placeholder="/username" />
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						<label>Password
						  <input id="pw" type="password" name="password" placeholder="No spaces." />
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						  <input id="save-sc-button" type="submit" class="button small" value="save" />
					</div>
				</div>
			</form>
		</div>
		<div id="forgot-modal" class="reveal-modal tiny" data-reveal aria-labelledby="Login" aria-hidden="true" role="dialog">
			<form>
				<div class="row">
					<div class="small-12 large-12 columns">
						<div id="forgot-status-message-happy">Verification code is sent.</div>
						<div id="forgot-status-message">
						</div>
						Enter your email and NEW PASSWORD.<br/><br/>
						A confirmation mail will be sent to your email address.<br/><br/>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						<label>Email
						  <input id="forgot_em" type="email" name="email" placeholder="Your email address" />
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						<label>New Password
						  <input id="forgot_pw" type="password" name="password" placeholder="No spaces." />
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-12 large-12 columns">
						  <input id="forgot-sc-button" type="submit" class="button small" value="change" />
						  <div id="loading-div" style="display:none"><img src="inc/img/progress.gif" width="60"/></div>
					</div>
				</div>
			</form>
		</div>
		<div id="about-modal" class="reveal-modal tiny" data-reveal aria-labelledby="About" aria-hidden="true" role="dialog">
			Higly inspired from sekizkirk.com.<br/>
			Added some features, removed some features.<br/><br/>
			If you encounter any problems: basbursen@gmail.com<br/><br/>
			Sevgi &lt;3
		</div>
		<?php
			if(!isset($_SESSION['batuhan2'])){
		?>
                <div id="batuhan-modal" class="reveal-modal tiny" data-reveal aria-labelledby="Batuhan" aria-hidden="true" role="dialog">
			<div style="text-align:center;">Brace yourselves!</div>
			<div style="text-align:center;">doldur.xyzV2 is coming with more features you will like. All system will be on github!</div>
                </div>
		<?php
			$_SESSION['batuhan2'] = true;
			}
		?>

	<?php
		}
	?>
	<script src="/inc/foundation/js/vendor/jquery.js"></script>
	<script src="/inc/jquery-ui/jquery-ui.min.js"></script>
	<script src="/inc/foundation/js/vendor/fastclick.js"></script>
	<script src="/inc/foundation/js/foundation.min.js"></script>
	<script>
		$(document).foundation('reflow');
	</script>
	<?php
		if(!$user_logged_in) {
			if($editname != null && strlen($editname)>0) {
				require_once("db.php");
				$prepared = $pdo->prepare("SELECT data FROM users WHERE username=:un");
				$prepared->bindValue(":un", $editname);
				$prepared->execute();
				$row = $prepared->fetchAll(PDO::FETCH_ASSOC);
				if(count($row) > 0) {
					$data = unserialize($row[0]['data']);
	?>
					<script>
						var edit_data = <?php echo json_encode($data); ?>;
					</script>
		<?php
				}
			}
		?>
		<script src="/inc/main.js?ver=2"></script>
	<?php
		} else if($eee) {
	?>
		<script>
		jQuery(document).ready(function(){
			jQuery("#edit-schedule").click(function(e){
				e.preventDefault();
				var form = jQuery("<form></form>");
				form.attr("method", "post");
				form.attr("action", "/");
				var action = jQuery("<input>");
				action.attr("name", "action");
				action.attr("type", "hidden");
				action.val("edit");
				var name = jQuery("<input>");
				name.attr("name", "name");
				name.val("<?php echo $uname; ?>");
				name.attr("type", "hidden");
				form.append(action);
				form.append(name);
				jQuery("body").append(form);
				form.submit();
			});
		});
		</script>
	<?php
		}
	?>
<script>
//$('#batuhan-modal').foundation('reveal', 'open');
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-57184117-3', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>