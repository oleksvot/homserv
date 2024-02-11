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
?>
<div class="zag">Virtual hosts</div>
<table border="0" class="txt">
<? foreach ($vhostlist as $n=>$v){ 
if (preg_match('/^https\-(.*)$/', $n, $m)) {
	$url="https://$m[1]";
} else {
	$url="http://$n";
}
if ($n=='hstools') continue;
?>
	<tr>
		<td><a href="<?=$url?>/"><?=$n?></a></td>
		<td><?=$v['documentroot']?></td>
		<td>
		<? if (isset($v['errorlog'])) {?>
		<a href="viewlog.php?file=<?=$v['errorlog']?>">[error]</a>&nbsp;
		<? }?>
		<? if (isset($v['customlog'])) {?>
		<a href="viewlog.php?file=<?=$v['customlog']?>">[access]</a>&nbsp;
		<? }?>
		</td>
		<td>
		<? if ($n!='localhost') {?>
		<a href="changevhost.php?hostname=<?=$n?>">[edit]</a>&nbsp;
		<a href="delvhost.php?hostname=<?=$n?>">[del]</a>
		<? }?>
		</td>
	</tr>
<? }?>
</table>



<div class="zag">Create virtual host</div>

<script language="JavaScript">
var oldroot="";
function hostnamechange() {
	if (document.aform.hostname.value!="") {
		if ((document.aform.documentroot.value=="") || (document.aform.documentroot.value==oldroot)) {
			oldroot="<?=addslashes($homedir)?>\\"+document.aform.hostname.value;
			document.aform.documentroot.value=oldroot;
		}
	}
}
</script>
<br/><i>To add a virtual host, just enter its name,
the remaining fields will be filled in automatically.<br/>
For the www-directory, you can specify the path to the www-directory
from your hosting provider (for example <b>/home/user/public_html</b>) is
will make it easier to debug scripts.</i><br/><br/>
<form name="aform" action="addvhost.php" method="POST">
<table border="0" class="txt">
	<tr>
		<td>Hostname:*</td>
		<td width="50%"><input class="txt" type="text" name="hostname" onchange="hostnamechange()"/></td>
	</tr>
	<tr>
		<td>WWW-directory:</td>
		<td><input class="txt" type="text" name="documentroot"/></td>
	</tr>
	<tr>
		<td>CGI-directory (ScriptAlias):</td>
		<td><input type="text" name="cgidir" value="/cgi-bin/" class="txt"/></td>
	</tr>
	<tr>
		<td>Protocol:</td>
		<td>
		<input type="radio" name="protokol" value="http" onclick="document.getElementById('sslopt').className='closed'" checked/>http&nbsp;&nbsp;
		<input type="radio" name="protokol" value="https" onclick="document.getElementById('sslopt').className='txt'"/>https
		</td>
	</tr>
</table>
<table id="sslopt" class="closed">
	<tr>
		<td>SSLCertificateFile:</td>
		<td width="50%"><input type="text" name="cf" value="<?=$serverdir?>\apache\conf\server.crt" class="txt"/></td>
	</tr>
	<tr>
		<td>SSLCertificateKeyFile:</td>
		<td><input type="text" name="ckf" value="<?=$serverdir?>\apache\conf\server.key" class="txt"/></td>
	</tr>
</table>
<div align="center">
		<input type="submit" name="submit" value="Create"/>
</div>
</form>

<? include_once("../scripts/foother.php");?>