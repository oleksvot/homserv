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

$title="HomServ | PHP modules";
include_once("../scripts/config.php");
$extdir=ini_get('extension_dir');
$ini=file($phpini);
$loadedext=array();
foreach ($ini as $line) {
	$line=trim($line);
	if (substr($line, 0, 1)==';') {continue;}
	if (preg_match('/^extension\s*\=\s*(\S+)\s*$/', $line, $m)) {
		$loadedext[]=$m[1];
	}
}

if (!empty($_POST['submit'])) {
	$fline=false;
	foreach ($ini as $n => $line) {
		$line=trim($line);
		if (!preg_match('/^\;?extension\s*\=\s*(\S+)\s*$/', $line, $m)) {continue;}
		if (!$fline) {
			$fline=$n;
			$ini[$n]="";
		} else {
			unset($ini[$n]);
		}
	}
	$l="";
	$dh=opendir($extdir);
	while (($file=readdir($dh))!==false) {
		if (!preg_match('/\.dll$/i', $file)) {continue;}
		if (isset($_POST[preg_replace('/\.dll$/i', '', $file)])) {
			$l.="extension=$file\r\n";
		} else {
			$l.=";extension=$file\r\n";
		}
	}
	closedir($dh);
	$ini[$fline]=$l;
	file_put_contents($phpini, implode("", $ini));
	restartapache();
}

?>
<div class="zag">PHP modules</div>
<form action="phpmodules.php" method="post">
<? $dh=opendir($extdir);
while (($file=readdir($dh))!==false) {
if (!preg_match('/\.dll$/i', $file)) {continue;}?>
<input type="checkbox" name="<?=preg_replace('/\.dll$/i', '', $file)?>" <?=in_array($file, $loadedext)?'checked':''?>/><?=$file?><br/>
<?}
closedir($dh);?>
<input type="submit" name="submit" value="Save"/>
</form>
<? include_once("../scripts/foother.php");?>