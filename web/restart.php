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

if (empty($_REQUEST['mode'])) {
	$title="HomServ | control panel";
	$nomenu=true;
	include_once("../scripts/config.php");
	if (file_exists("$serverdir\\temp\\apachestart.txt")) {unlink("$serverdir\\temp\\apachestart.txt");}
	$goto=@$_REQUEST['goto'];
?>
<div class="zag">Restarting Apache...</div><br>
<img src="restart.php?mode=exec" style="visibility: hidden;"/>
<div id="i"></div>
<script language="JavaScript">
function up() {
	document.getElementById("i").innerHTML='<iframe src="restart.php?mode=check&goto=<?=urlencode($goto)?>" style="visibility: hidden;"/>';
	window.setTimeout('up()', 500);
}
window.setTimeout('up()', 1);
</script>
<? include_once("../scripts/foother.php");
}

if (@$_REQUEST['mode']=='exec') {
	include_once("../scripts/config.php");
	hcontrol('restart', 'apache');
}


if ((@$_REQUEST['mode']=='check')) {
	include_once("../scripts/config.php");
	if (!file_exists("$serverdir\\temp\\apachestart.txt")) {exit;}
	header("Cache-Control: no-cache,no-store,must-revalidate");
	header("Pragma: no-cache");
	
?>
<html><head>
<script language="JavaScript">
<? if (empty($_REQUEST['goto'])) {?>
parent.history.back();
<?} else {?>
parent.location="<?=$_REQUEST['goto']?>";
<?}?>
</script>
</head>
<body>
</body></html>
<?
}

?>