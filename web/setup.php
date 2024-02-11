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

$title="HomServ | general settings";
include_once("../scripts/config.php");
if (isset($_POST['submit'])) {
	$cnf['runstartup']=isset($_POST['runstartup']);
	$cnf['cleanhosts']=isset($_POST['cleanhosts']);
	$cnf['trayicon']=isset($_POST['trayicon']);
	$cnf['autohosts']=isset($_POST['autohosts']);
	if ($cnf['runstartup']) {$cnf['trayicon']=true;}
	$cnf['update']=isset($_POST['update']);
	$cnf['swait']=intval($_POST['swait']);
	$cnf['zenddebug']=isset($_POST['zenddebug']);
	$cnf['zendstudiodir']=htmlspecialchars(delslash($_POST['zendstudiodir']));
	$cnf['dbg']=isset($_POST['dbg']);
	$cnf['dbglistener']=htmlspecialchars(strtolower(delslash($_POST['dbglistener'])));
	
		$php5cont=file_get_contents($phpini);
	if ($cnf['zenddebug']) {
		
		$php5cont=preg_replace('/^\s*auto\_prepend\_file\s*\=.*$/im', "auto_prepend_file=\"$serverdir\\tools\\scripts\\zenddebug.php\"", $php5cont);
	} else {
		
		$php5cont=preg_replace('/^\s*auto\_prepend\_file\s*\=.*$/im', "auto_prepend_file=", $php5cont);
	}
	if ($cnf['dbg']) {
		$php5cont=str_replace("\n;extension=php_dbg.dll", "\nextension=php_dbg.dll", $php5cont);
		$php5cont=str_replace("\n;zend_extension_ts=\"$serverdir\php5\ext\php_dbg.dll\"", "\nzend_extension_ts=\"$serverdir\php5\ext\php_dbg.dll\"", $php5cont);
	} else {
		$php5cont=str_replace("\nextension=php_dbg.dll", "\n;extension=php_dbg.dll", $php5cont);
		$php5cont=str_replace("\nzend_extension_ts=\"$serverdir\php5\ext\php_dbg.dll\"", "\n;zend_extension_ts=\"$serverdir\php5\ext\php_dbg.dll\"", $php5cont);
	}
	
	file_put_contents($phpini, $php5cont);
	if ($cnf['allusers']) {
		$ilnk='c';
	} else {
		$ilnk='';
	}
	// creating/removing link for traytool
	if ($cnf['trayicon']) {
		exec("$serverdir\\StartHomServ.exe -lnk {$ilnk}startup create");
	} else {
		exec("$serverdir\\StartHomServ.exe -lnk {$ilnk}startup del");
	}
	
	if (in_array($_POST['phpmail'], array('auto', 'smtp', 'sendmail'))) {
		$cnf['phpmail']=$_POST['phpmail'];
		$cnf['smtphost']=htmlspecialchars($_POST['smtphost']);
		$cnf['smtplogin']=htmlspecialchars($_POST['smtplogin']);
		$cnf['smtppass']=htmlspecialchars($_POST['smtppass']);
		$cnf['smtpfrom']=htmlspecialchars($_POST['smtpfrom']);
	}
	// set browser
	if ($_POST['browser']!=$cnf['browser']) {
		setbrowser($_POST['browser']);
	}
	saveconfig($cnf);
	restartapache();
}
?>


<form name="sf" action="setup.php" method="POST">
<table border="0" class="txt">
	<tr>
		<td colspan="2"><div class="zag">General settings</div></td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="runstartup" <?=$cnf['runstartup']?'checked':''?>/>
		Start HomServ at windows login
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="trayicon" <?=$cnf['trayicon']?'checked':''?>/>
		Always display tray icon
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="cleanhosts" <?=$cnf['cleanhosts']?'checked':''?>/>
		Clean up hosts file when HomServ shuts down
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="autohosts" <?=$cnf['autohosts']?'checked':''?>/>
		Automatic creation of virtual hosts
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="update" <?=$cnf['update']?'checked':''?>/>
		Check for updates (weekly)
		</td>
	</tr>
	<tr>
		<td colspan="2">
		Component launch timeout &nbsp;&nbsp;&nbsp;
		<input size="3" type="text" name="swait" value="<?=$cnf['swait']?>"/> сек.
		</td>
	</tr>
	<tr>
		<td>Browser path</td>
		<td>
		<input class="txt" type="text" name="browser" value="<?=htmlspecialchars($cnf['browser'])?>"/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div class="zag">SendMail</div></td>
	</tr>
	<tr>
		<td>Sending Mode <a href="http://homserv.net?page=faq#sendmail" target="_blank">[?]</a></td>
		<td>
		<input type="radio" name="phpmail" value="sendmail" <?=$cnf['phpmail']=='sendmail'?'checked':''?>/>Save only<br/>
		<input type="radio" name="phpmail" value="smtp" <?=$cnf['phpmail']=='smtp'?'checked':''?>/>Save and send to external SMTP server<br/>
		<input type="radio" name="phpmail" value="auto" <?=$cnf['phpmail']=='auto'?'checked':''?>/>Save and send to local SMTP
		</td>
	</tr>
	<tr>
		<td colspan="2">External SMTP options:</td>
	</tr>
	<tr>
		<td>Hostname</td>
		<td>
		<input class="txt" type="text" name="smtphost" value="<?=$cnf['smtphost']?>"/>
		</td>
	</tr>
	<tr>
		<td>Username (optional.)</td>
		<td>
		<input class="txt" type="text" name="smtplogin" value="<?=$cnf['smtplogin']?>"/>
		</td>
	</tr>
	<tr>
		<td>Password (optional.)</td>
		<td>
		<input class="txt" type="text" name="smtppass" value="<?=$cnf['smtppass']?>"/>
		</td>
	</tr>
	<tr>
		<td>Email from</td>
		<td>
		<input class="txt" type="text" name="smtpfrom" value="<?=$cnf['smtpfrom']?>"/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div class="zag">Zend debugger</div></td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="zenddebug" <?=$cnf['zenddebug']?'checked':''?>/>
		Enable Zend Debugger
		</td>
	</tr>
	<tr>
		<td>Zend Studio path</td>
		<td>
		<input class="txt" type="text" name="zendstudiodir" value="<?=$cnf['zendstudiodir']?>"/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div class="zag">DBG (ExpertDebugger)</div></td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="checkbox" name="dbg" <?=$cnf['dbg']?'checked':''?>/>
		Enable DBG
		</td>
	</tr>
	<tr>
		<td valign="top">DbgListener.exe path <a href="dbginfo.php" target="_blank">[?]</a></td>
		<td>
		<input class="txt" type="text" name="dbglistener" value="<?=$cnf['dbglistener']?>"/><br/>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<input type="submit" name="submit" value="Save"/>
		</td>
	</tr>
</table>
</form>


<? include_once("../scripts/foother.php");?>