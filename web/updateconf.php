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

$title="HomServ | FTP Sync";
include_once("../scripts/config.php");
if (file_exists($updatefile)) {
	$updateconf=unserialize(file_get_contents($updatefile));
} else {
	$updateconf=array();
}
$vlist=getvhostlist();

if (@$_REQUEST['mode']=='save') {
	if (empty($_REQUEST['name'])) {echo "Error no name.";exit;};
	$name=$_REQUEST['name'];
	$updateconf[$name]['localdir']=htmlspecialchars($_POST['localdir']);
	$updateconf[$name]['ftphost']=htmlspecialchars($_POST['ftphost']);
	$updateconf[$name]['ftplogin']=htmlspecialchars($_POST['ftplogin']);
	if (!empty($_POST['ftppass'])) {
		$updateconf[$name]['ftppass']=htmlspecialchars($_POST['ftppass']);
	}
	$updateconf[$name]['remotedir']=htmlspecialchars($_POST['remotedir']);
	$ignore=htmlspecialchars($_POST['ignore']);
	if (empty($ignore)) {
		$updateconf[$name]['ignore']=array();
	} else {
		$ar=explode("\n", $ignore);
		foreach ($ar as $n=>$v) {
			if (trim($v)=="") {unset($ar[$n]);}
			$ar[$n]=trim($v);
		}
		$updateconf[$name]['ignore']=array_values($ar);
	}
	if (empty($updateconf[$name]['ftplist'])) {
		$updateconf[$name]['ftplist']=array();
		$updateconf[$name]['locallist']=array();
		file_put_contents($updatefile, serialize($updateconf));
		header("Location: http://localhost/cp/runupdate.php?first=1&name=$name&new=$_REQUEST[new]");
		ob_end_clean();
		exit;
	} else {
		$_REQUEST['mode']=false;
	}
	file_put_contents($updatefile, serialize($updateconf));
}

if (@$_REQUEST['mode']=='del') {
	$name=$_REQUEST['name'];
	unset($updateconf[$name]);
	file_put_contents($updatefile, serialize($updateconf));
	$_REQUEST['mode']=false;
}

if (empty($_REQUEST['mode'])) {?>
<table border="0" class="txt">
	<?foreach ($updateconf as $n => $v) {?>
	<tr>
	<td><?=$n?></td>
	<td><a href="runupdate.php?name=<?=$n?>&amp;m=local">[hs &gt; host]</a></td>
	<td><a href="runupdate.php?name=<?=$n?>&amp;m=ftp">[host &gt; hs]</a></td>
	<td><a href="updateconf.php?mode=edit&amp;name=<?=$n?>">[edit]</a></td>
	<td><a href="updateconf.php?mode=del&amp;name=<?=$n?>">[del]</a></td>
	</tr>
	<?}?>
</table>
<a href="updateconf.php?mode=edit">Add task</a><br/>
<?}
if (@$_REQUEST['mode']=='edit') {
	$name=@$_REQUEST['name'];
	$c=@$updateconf[$name];
?>
<br/>
<a href="http://homserv.net?page=faq#ftp" target="_blank">Help</a><br/><br/>
<form action="updateconf.php" method="post">
<input type="hidden" name="mode" value="save"/>
<table border="0" class="txt">
<tr>
	<td>Task name*:</td>
	<td><input type="text" name="name" class="txt" value="<?=$name?>"/></td>
</tr>
<tr>
	<td>Local folder*:</td>
	<td>
	<input type="text" name="localdir" style="width: 50%;" value="<?=@$c['localdir']?>"/>
	<select name="localvhost" onchange="localdir.value=localvhost.value" style="width: 39%;">
	<option value="">From vhost</option>
	<? foreach ($vlist as $hn=>$hv) {
	if ($hn=='hstools') continue;?>
	<option value="<?=str_replace("/", "\\", $hv['documentroot'])?>"><?=$hn?></option>
	<?}?>
	</select>
	</td>
</tr>
<tr>
	<td>FTP-hostname*:</td>
	<td><input type="text" name="ftphost" class="txt" value="<?=@$c['ftphost']?>"/></td>
</tr>
<tr>
	<td>Username*:</td>
	<td><input type="text" name="ftplogin" class="txt" value="<?=@$c['ftplogin']?>"/></td>
</tr>
<tr>
	<td>Password*:</td>
	<td><input type="password" name="ftppass" class="txt"/></td>
</tr>
<tr>
	<td>Remote folder (starting with /):</td>
	<td><input type="text" name="remotedir" class="txt" value="<?=@$c['remotedir']?>"/></td>
</tr>
<tr>
	<td>Folders to exclude</td>
	<td width="60%"><textarea name="ignore" class="txt"><?if (is_array(@$c['ignore'])) echo implode("\r\n", $c['ignore'])?></textarea></td>
</tr>
<?if (empty($name)){?>
<tr>
	<td colspan="2">
	<input type="radio" name="new" value="no" checked/> Treat directories as synced<br/>
	<input type="radio" name="new" value="local"/> Sync all hs &gt; host<br/>
	<input type="radio" name="new" value="ftp"/> Sync all host &gt; hs<br/>
	</td>
</tr>
<?}?>
<tr>
	<td colspan="2" align="center"><input type="submit" name="submit" value="Save"/></td>
</tr>
</table>
</form>

<?
}

include_once("../scripts/foother.php");
?>