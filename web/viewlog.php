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

$title="HomServ | log viewer";
include_once("../scripts/config.php");
$file=$_GET['file'];
$vhostlist=getvhostlist();
foreach ($vhostlist as $n=>$v) {
	if ($v['errorlog']==$file) {
		$dec="$n - error log";
		break;
	}
	if ($v['customlog']==$file) {
		$dec="$n - access customlog";
		break;
	}
}

?>
<div class="zag"><?=$dec?></div>

<a href="viewlog.php?file=<?=$file?>">[Update]</a>&nbsp;
<a href="viewlog.php?file=<?=$file?>&amp;unlink=1">[Clear]</a><br/>
<table border="0" class="txt"><tr><td>
<?
if (!isset($_GET['unlink']) and file_exists($file)) {
	$body=file_get_contents($file);
	$body=htmlspecialchars($body);
	$body=str_replace("\n", "</td></tr><tr><td>\n", $body);
	echo $body;
}
if (isset($_GET['unlink'])) {
	if (file_exists("$serverdir\\temp\\unlink.ini")) {
		$unlinkconf=file("$serverdir\\temp\\unlink.ini");
	}
	$unlinkconf[]=$file;
	file_put_contents("$serverdir\\temp\\unlink.ini", implode("", $unlinkconf));
	restartapache("http://localhost/cp/viewlog.php?file=$file");
}
?>
</td></tr></table>
<? include_once("../scripts/foother.php");?>