<?
# HomServ control panel
# Copyright (C) 2007-2024 Oleksandr Titarenko <admin@homserv.net>
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <https://www.gnu.org/licenses/>.

set_time_limit(0);
$title="HomServ | FTP Sync";
include_once("../scripts/config.php");
include_once('Net/FTP.php');
if (empty($_REQUEST['name'])) {die("Error. No task name.");} 
$name=$_REQUEST['name'];
$updateconf=unserialize(file_get_contents($updatefile));
$ftp=new Net_FTP();
$ftp->connect($updateconf[$name]['ftphost']);
if ($ftp->login($updateconf[$name]['ftplogin'], $updateconf[$name]['ftppass'])!==true) {
	echo "FTP error.";include_once("../scripts/foother.php");exit;
}
$ftp->setPassive();

if (!empty($_REQUEST['first'])) {
	$new=$_REQUEST['new'];
	$up=readftp($updateconf[$name]['ignore'], $updateconf[$name]['ftplist'], $ftp, $updateconf[$name]['remotedir']);
	updateftp(($new=='ftp')?$ftp:false, $updateconf[$name]['remotedir'], $updateconf[$name]['localdir'], $up, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	
	$up=readlocal($updateconf[$name]['ignore'], $updateconf[$name]['locallist'], $updateconf[$name]['localdir']);
	updatelocal(($new=='local')?$ftp:false, $updateconf[$name]['localdir'], $updateconf[$name]['remotedir'], $up, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	file_put_contents($updatefile, serialize($updateconf));
	header("Location: http://localhost/cp/updateconf.php");
	ob_end_clean();
	exit;
}

if ($_REQUEST['m']=='ftp') {
	$up=readftp($updateconf[$name]['ignore'], $updateconf[$name]['ftplist'], $ftp, $updateconf[$name]['remotedir']);
	if (empty($up)) {
		echo 'No changed files.<br/><div align="center"><a href="javascript:history.back()">[Back]</a></div>';
	} else {
		file_put_contents("$serverdir\\temp\\ftp-$name.txt", serialize($up));
		$filescount=0;
		$filessize=0;
		foreach ($up as $u) {
			if ($u['type']=='load') {
				$filescount++;
				$filessize+=$u['size'];
			}
		}
?>
Need to download <?=$filescount?> files total <?=$filessize?> bytes.<br/>
<a href="runupdate.php?m=okftp&amp;name=<?=$name?>">Dismiss all</a><br/>
<form action="runupdate.php" method="post">
<input type="hidden" name="m" value="okftp"/>
<input type="hidden" name="name" value="<?=$name?>"/> 
<? foreach ($up as $n=>$u) {
	if ($u['type']=='load') {$d=date("j.m.Y H:i", $u['date']);} else {$d="";$u['size']="";}
	echo "<input type=\"checkbox\" name=\"f$n\" checked/> $u[type] $u[path] $u[size] $d<br/>";
}?>
<input type="submit" value="ÎÊ"/>
</form>
<?
	}
}

if ($_REQUEST['m']=='okftp') {
	$up=unserialize(file_get_contents("$serverdir\\temp\\ftp-$name.txt"));
	$noup=array();
	foreach ($up as $n=>$u) {
		if (!isset($_REQUEST["f$n"])) {
			$noup[]=$u;
			unset($up[$n]);
		}
	}
	$up=array_values($up);
	updateftp($ftp, $updateconf[$name]['remotedir'], $updateconf[$name]['localdir'], $up, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	updateftp(false, $updateconf[$name]['remotedir'], $updateconf[$name]['localdir'], $noup, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	echo 'Download completed.<br/><div align="center"><a href="updateconf.php">[Back]</a></div>';
}







if ($_REQUEST['m']=='local') {
	$up=readlocal($updateconf[$name]['ignore'], $updateconf[$name]['locallist'], $updateconf[$name]['localdir']);
	if (empty($up)) {
		echo 'No changed files.<br/><div align="center"><a href="javascript:history.back()">[Back]</a></div>';
	} else {
		file_put_contents("$serverdir\\temp\\local-$name.txt", serialize($up));
		$filescount=0;
		$filessize=0;
		foreach ($up as $u) {
			if ($u['type']=='load') {
				$filescount++;
				$filessize+=$u['size'];
			}
		}
?>
Need to upload <?=$filescount?> files total <?=$filessize?> bytes.<br/>
<a href="runupdate.php?m=oklocal&amp;name=<?=$name?>">Dismiss all</a><br/>
<form action="runupdate.php" method="post">
<input type="hidden" name="m" value="oklocal"/>
<input type="hidden" name="name" value="<?=$name?>"/> 
<? foreach ($up as $n=>$u) {
	if ($u['type']=='load') {$d=date("j.m.Y H:i", $u['date']);} else {$d="";$u['size']="";}
	echo "<input type=\"checkbox\" name=\"f$n\" checked/> $u[type] $u[path] $u[size] $d<br/>";
}?>
<input type="submit" value="ÎÊ"/>
</form>
<?
	}
}

if ($_REQUEST['m']=='oklocal') {
	$up=unserialize(file_get_contents("$serverdir\\temp\\local-$name.txt"));
	$noup=array();
	foreach ($up as $n=>$u) {
		if (!isset($_REQUEST["f$n"])) {
			$noup[]=$u;
			unset($up[$n]);
		}
	}
	$up=array_values($up);
	updatelocal($ftp, $updateconf[$name]['localdir'], $updateconf[$name]['remotedir'], $up, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	updatelocal(false, $updateconf[$name]['localdir'], $updateconf[$name]['remotedir'], $noup, $updateconf[$name]['locallist'], $updateconf[$name]['ftplist']);
	echo 'Upload completed.<br/><div align="center"><a href="updateconf.php">[Back]</a></div>';
}


file_put_contents($updatefile, serialize($updateconf));


?>

<? include_once("../scripts/foother.php");?>