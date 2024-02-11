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

$title="HomServ | installation";
$nomenu=true;
include_once("../scripts/config.php");
$restarturl="http://$_SERVER[HTTP_HOST]/cp/restart.php?goto=".urlencode("http://localhost/cp/install.php");
$step=@$_REQUEST['step'];

// step 1 - virtual host test
if (empty($step)) {
	if ($cnf['hostsnotwrite']) {?>
<input type="button" value="Try again" onclick="location.href='<?=$restarturl?>'"/>&nbsp;&nbsp;&nbsp;
<input type="button" value="Skip" onclick="location.href='install.php?step=2'"/>
<? include_once("../scripts/foother.php");
exit;
}
	$hconf=file_get_contents(gethostspath());
	if (!instr($hconf, "127.0.0.1\thstools")) {
		$cnf['hostsnotwrite']=true;
		saveconfig($cnf);
		header("Location: http://localhost/cp/install.php");
		exit;
	}
	$atest=file_get_contents('http://hstools/atest.php');
	if (!instr($atest, "{HomServ:OK}")) {
		echo "<br/><b>Error!</b>The hosts file is not involved. The reason may be various conflicting software (proxy servers, freewalls, vpn, traffic compressors). ";
		echo "Try disabling all third-party programs and services.<br/>";
	} else {?>
<div class="zag">Testing virtual host support</div><br/>
<iframe src="http://hstools/htest.php" frameborder="0" height="150" width="500"></iframe>
<br/><br/><b>Error!</b> Your browser settings do not allow the use of virtual hosts<br/>
<ul>
<li>
	Check if your proxy server is disabled.<br/>
	<b>IE</b>: Tools > Internet Options > Connections.<br/>
	<b>Firefox</b>: Tools > Settings > Advanced > Network
</li>
<li>
	<b>IE</b>: Disable automatic internet connection.<br/>
	Tools > Internet Options > Connection > Remote Access Settings > Do Not Use
</li>
<li>
	<b>IE</b>: If you are prompted to connect to the Internet when you try to open a link,
	choose &quot;Connect&quot; or &quot;Retry&quot;.<br/>
</li>
<li>
	Try to go directly:<br/>
	<a href="http://hstools/">hstools</a>&nbsp;&nbsp;
	<a href="http://first/">first</a>&nbsp;&nbsp;
	<a href="http://second/">second</a><br/>
	If these links work, then click <b>Skip</b>.<br/>
</li>
<li>
	If the problem continues to occur, then use a different browser.<br/>
</li>
</ul>
<?}
?>
<input type="button" value="Try again" onclick="location.href='<?=$restarturl?>'"/>&nbsp;&nbsp;&nbsp;
<input type="button" value="Skip" onclick="location.href='install.php?step=2'"/>
<?}

// шаг 2 - начальные параметры
if ($step==2) {?>
<div class="zag">Completing the installation</div><br/>
<form action="install.php" method="POST">
<input type="hidden" name="step" value="3"/>
<table border="0">
	<tr>
		<td colspan="2">
		<input type="checkbox" name="desktop" checked/>
		Create desktop shortcut
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="allusers" checked/>
		Install for all users
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="update" checked/>
		Check for updates (weekly)
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="trayicon"/>
		Always display tray icon
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="runstartup"/>
		Start HomServ at windows login
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<input type="submit" name="submit" value="Finish"/>
		</td>
	</tr>
</table>
</form>
<?}
if ($step==3) {
	$cnf['allusers']=isset($_POST['allusers']);
	$cnf['trayicon']=isset($_POST['trayicon']);
	$cnf['runstartup']=isset($_POST['runstartup']);
	$cnf['update']=isset($_POST['update']);
	if ($cnf['allusers']) {
		$ilnk='c';
		include_once("../scripts/sethosts.php");
	} else {
		$ilnk='';
	}
	if ($cnf['trayicon']) {
		exec("$serverdir\\StartHomServ.exe -lnk {$ilnk}startup create");
	}
	if (isset($_POST['desktop'])) {
		exec("$serverdir\\StartHomServ.exe -lnk {$ilnk}desktop create");
	}
	if (!file_exists("$homedir\\first\\index.php")) copy("$serverdir\\tools\\web\\testpage.php", "$homedir\\first\\index.php");
	if (!file_exists("$homedir\\second\\index.php")) copy("$serverdir\\tools\\web\\testpage.php", "$homedir\\second\\index.php");
	if (!file_exists("$homedir\\localhost\\index.php")) copy("$serverdir\\tools\\web\\testpage.php", "$homedir\\localhost\\index.php");
	if (!file_exists("$homedir\\default\\index.php")) copy("$serverdir\\tools\\web\\testpage.php", "$homedir\\default\\index.php");
	$cnf['itime']=time();
	saveconfig($cnf);
	header("Location: http://localhost/cp/");
}

include_once("../scripts/foother.php");
?>