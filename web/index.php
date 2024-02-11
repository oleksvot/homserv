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

$title="HomServ | control panel";
include_once("../scripts/config.php");
if (empty($cnf['itime'])) {
	header("Location: http://localhost/cp/install.php");
	exit;
}
$vlist=getvhostlist();
$defaultdir="$homedir\\default";

if (isset($_REQUEST['start'])) {
	$c=$_REQUEST['start'];
	if (!checkrun($programs[$c]['exe'])) {
		$cnf[$c]=true;
		saveconfig($cnf);
		hcontrol('start', $c, false);
	}
}
if (isset($_REQUEST['stop'])) {
	$c=$_REQUEST['stop'];
	if (checkrun($programs[$c]['exe'])) {
		$cnf[$c]=false;
		saveconfig($cnf);
		hcontrol('stop', $c, false);
	}
}
?>
<table border="0" class="txt">
<tr>
	<td nowrap>HomServ version</td>
	<td><?=$homservversion?></td>
</tr>
<tr>
	<td nowrap>Root folder</td>
	<td><?=$serverdir?></td>
</tr>
<tr>
	<td nowrap>Apache version</td>
	<td><?=function_exists('apache_get_version')?apache_get_version():"Недоступно"?></td>
</tr>
<tr>
	<td nowrap>About Apache</td>
	<td><a href="http://localhost/server-info/">[server-info]</a>&nbsp;<a href="http://localhost/server-status/">[server-status]</a></td>
</tr>
<tr>
	<td nowrap>About PHP</td>
	<td><a href="phpinfo.php">[phpinfo]</a></td>
</tr>
<tr>
	<td nowrap>Default host</td>
	<td><?=$defaultdir?></td>
</tr>
</table>
<br/>
<table border="0" class="txt">
<? 
foreach ($programs as $n=>$c) {
	echo '<tr><td>';
	echo $c['name'].' '.$c['version'];
	echo '</td><td>';
	if ((checkrun($c['exe']) or @$_REQUEST['start']==$n) and (@$_REQUEST['stop']!=$n)) {
		echo '<font color="green">Running</font>';
		echo '</td><td>';
		if (!@$c['option'] or !strstr($c['option'], 'runalways')) {
			echo '<a href="index.php?stop='.$n.'">[stop]</a>';
		}
	} else {
		echo '<font color="red">Stopped</font>';
		echo '</td><td>';
		echo '<a href="index.php?start='.$n.'">[start]</a>';
	}
	echo '</td></tr>';
}
?>
</table>
<br/>
<?
include_once("../scripts/foother.php");
?>