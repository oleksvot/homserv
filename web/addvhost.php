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

if (isset($_POST['submit'])) {
	$vhostlist=getvhostlist();
	$hostname=strtolower(trim($_POST['hostname']));
	if ($_POST['protokol']=='https') {
		$hostname='https-'.$hostname;
	}
	if ($hostname=="") {
		echo "Error. No vhost name";
	} elseif (!preg_match('/^[A-Za-z0-9\.\_\-]+$/', $hostname)) {
		echo "Error. Vhost name contains not allowed chars";
	} elseif (trim($_POST['documentroot'])=="") {
		echo "Error. No home dir";
	} elseif (array_key_exists($hostname, $vhostlist) and !isset($_POST['change'])) {
		echo "Error. Vhost $hostname already exists";
	} else {
		$documentroot=$_POST['documentroot'];
		if (!preg_match('/^\w\:/', $documentroot)) {
			$documentroot=substr($serverdir, 0, 2).$documentroot;
		}
		
		$other=trim(@$_POST['other']);
		if (!empty($_POST['cgidir'])) {
			$cgiroot=str_replace("\\", "/", delslash($documentroot)).$_POST['cgidir'];
			@forsedir($cgiroot);
			$other.="\nScriptAlias \"$_POST[cgidir]\" \"$cgiroot\"";
		}
		
		$errorlog="$serverdir\\apache\\logs\\{$hostname}_error.log";
		$customlog="$serverdir\\apache\\logs\\{$hostname}_access.log";
		$goto='http://'.$_SERVER['HTTP_HOST'].'/cp/vhost.php';
		addvhost($hostname, $documentroot, $errorlog, $customlog, $other, $_POST['cf'], $_POST['ckf']);
		restartapache($goto);
	}
}
?>
<br/>
<div align="center">
<? if (@$ok):?>
<a href="vhost.php">[Back]</a>
<? else: ?>
<a href="javascript:history.back(1)">[Back]</a>
<? endif?>
</div>


<? include_once("../scripts/foother.php");?>