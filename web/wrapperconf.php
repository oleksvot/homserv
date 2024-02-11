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

$title="HomServ | exe wrapper";
include_once("../scripts/config.php");
if (file_exists("$serverdir\\tools\\wrapperconf.txt")) {
	$wrapperconf=unserialize(file_get_contents("$serverdir\\tools\\wrapperconf.txt"));
} else {
	$wrapperconf=array();
}

if (@$_REQUEST['mode']=='install') {
	$cnf['exewrapper']=true;
	saveconfig($cnf);
	installwrapper($wrapperconf);
}

if (@$_REQUEST['mode']=='add') {
	if (empty($_POST['path'])) {
		echo "Error. No path.";
	} elseif (!file_exists(strtok($_POST['rpath'], " "))) {
		echo "Error. File $_POST[rpath] not exists.";
	} else {
		foreach ($wrapperconf as $w) {
			if ($w['path']==$_POST['path']) {$error=true;break;}
		}
		if (@$error) {
			echo "Error. Already exists.";
		} else {
			$wrapperconf[]=array('path'=>htmlspecialchars($_POST['path']), 'rpath'=>htmlspecialchars($_POST['rpath']));
			file_put_contents("$serverdir\\tools\\wrapperconf.txt", serialize($wrapperconf));
			installwrapper($wrapperconf);
			file_put_contents("$serverdir\\tools\\wrapperconf.txt", serialize($wrapperconf));
		}
	}
	
}

if (@$_REQUEST['mode']=='del') {
	$n=intval($_REQUEST['n']);
	unlink($wrapperconf[$n]['wpath']);
	unset($wrapperconf[$n]);
	$wrapperconf=array_values($wrapperconf);
	file_put_contents("$serverdir\\tools\\wrapperconf.txt", serialize($wrapperconf));
}

$wrapperallow=$cnf['exewrapper'];
if ((!empty($wrapperconf)) and (!file_exists($wrapperconf[0]['wpath']))) {
	$wrapperallow=false;
}
?>
<br/>
<i>Exe wrapper allows you to use paths of the form /usr/bin/perl for SendMail, Perl etc.</i>
<br/>
<? if (!$wrapperallow) {?>
<br/>Exe wrapper not installed. <a href="wrapperconf.php?mode=install">install</a><br/><br/>
<? }?>
<table border="0" class="txt">
<? foreach ($wrapperconf as $n=>$v) {?>
	<tr>
		<td width="30%"><?=$v['path']?></td>
		<td width="50%"><?=$v['rpath']?></td>
		<td><a href="wrapperconf.php?mode=del&amp;n=<?=$n?>">[del]</a></td>
	</tr>
<? }?>
</table>
<div class="zag">Create path</div>
<form action="wrapperconf.php" method="post">
<input type="hidden" name="mode" value="add"/>
<table border="0" class="txt">
<tr>
	<td>Wrapper path:</td>
	<td width="70%"><input type="text" name="path" class="txt"/></td>
</tr>
<tr>
	<td>Real path:</td>
	<td><input type="text" name="rpath" class="txt"/></td>
</tr>
<tr>
	<td colspan="2" align="center"><input type="submit" value="Create"/></td>
</tr>
</table>
</form>
<? include_once("../scripts/foother.php");?>