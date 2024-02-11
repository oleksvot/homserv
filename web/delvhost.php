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

$title="HomServ | virtual hosts";
include_once("../scripts/config.php");
$vhostlist=getvhostlist();
if (isset($_GET['del'])) {
	$dr=$vhostlist[$_GET['hostname']]['documentroot'];
	if (@$_GET['delfiles']=='true') {
		exec("RMDIR /S /Q \"$dr\"");
		if (file_exists("$serverdir\\temp\\unlink.ini")) {
			$unlinkconf=file("$serverdir\\temp\\unlink.ini");
		}
		$unlinkconf[]=$vhostlist[$_GET['hostname']]['customlog'];
		$unlinkconf[]=$vhostlist[$_GET['hostname']]['errorlog'];
		file_put_contents("$serverdir\\temp\\unlink.ini", implode("\n", $unlinkconf)."\n");
	}
	delvhost($_GET['hostname']);
	header("Location: http://localhost/cp/vhost.php");
	ob_end_clean();
	exit;
}

?>

<div class="zag">Vhost deletion</div>

<form action="delvhost.php" method="GET">
<input type="hidden" name="del" value="true"/>
<input type="hidden" name="hostname" value="<?=$_GET['hostname']?>"/>
Are you sure you want to remove the host <?=$_GET['hostname']?>?<br/>
<input type="checkbox" name="delfiles" value="true"/>&nbsp;
Delete also files from <?=$vhostlist[$_GET['hostname']]['documentroot']?><br/>
<br/>

<input type="submit" value="Yes"/>&nbsp;&nbsp;
<input type="button" value="No" onclick="history.back()"/>

</form>
<br/>
<div align="center">
<a href="vhost.php">[Back]</a>
</div>


<? include_once("../scripts/foother.php");?>