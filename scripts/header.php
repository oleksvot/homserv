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

header("Content-Type: text/html; charset=windows-1251");
header("Cache-Control: no-cache,no-store,must-revalidate");
header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<?=@$headerinc?><title><?=$title?></title>
	<link href="favicon.ico" rel="shortcut icon" type="image/x-icon"/>
	<link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="maindiv">
	<div class="top">
		<div style="float: left"><img src="homserv.gif" alt="HomServ"/></div>
		<div style="float: none" align="right">
			<a href="http://localhost/cp/">Control panel</a>&nbsp;&nbsp;&nbsp;
			<a href="https://t.me/oleksvot">Telegram</a>&nbsp;&nbsp;&nbsp;
			<a href="https://homserv.net/">HomServ website</a>
		</div>
	</div>
<? if(empty($nomenu)):?>
	<div class="leftcol">
		<a href="index.php">Dashboard</a><br>
		<a href="setup.php">General settings</a><br>
		<a href="vhost.php">Virtual hosts</a><br>
		<a href="updateconf.php">FTP sync</a><br>
		<a href="mysql.php">MySQL databases</a><br>
		<a href="phpmodules.php">PHP modules</a><br>
		<a href="wrapperconf.php">Exe wrapper</a><br>
		<a href="test.php">Testing</a><br>
		<a href="phpinfo.php">PHP info</a><br>
		<a href="http://localhost/pma/">phpMyAdmin</a><br>
	</div>
<? endif;?>
	<div class="content">
<? if (@$cnf['hostsnotwrite'] and !instr($_SERVER['REQUEST_URI'], 'restart.php')):?>
<div align="left"><br/>
<b>Attention!</b> The hosts file is not writable. Virtual hosts will not work except for localhost.<br>
Execute in command prompt with administrator privileges<a href="" onclick="alert('Click Start, System Tools - Windows. Then right-click on Command Prompt and select Advanced, Run as administrator from the context menu.'); return false;">[?]</a>:<br>
<pre>
cd <?="$serverdir\\tools"?>

sethosts.bat
[Enter]

</pre>
<br/>
</div>
<? endif;?>