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

$title="HomServ | change MySQL root password";
include_once("../scripts/config.php");
?>
<div class="zag">Change MySQL root password</div>
<?
if (isset($_POST['submit'])) {
	if ($_POST['passa']!=$_POST['passb']) {
		echo "Error. Passwords do not match<br/>";
	} elseif (!preg_match('/^[A-Za-z0-9\_]+$/', $_POST['passa'])) {
		echo "Error. Incorrect password.";
	} elseif (!$db=mysqli_connect('localhost', 'root', $mysqlpass)) {
		echo "Error. No MySQL connection<br/>";
	} else {
		mysqli_select_db($db, "mysql");
		$passa=$_POST['passa'];
		mysqli_query($db, "UPDATE user SET authentication_string=PASSWORD('$passa') WHERE User='root'");
		$pconf=file_get_contents("$serverdir\\pma\\config.inc.php");
		$pconf=str_replace("\$cfg['Servers'][\$i]['password'] = '$mysqlpass';", "\$cfg['Servers'][\$i]['password'] = '$passa';", $pconf);
		file_put_contents("$serverdir\\pma\\config.inc.php", $pconf);
		$hconf=file_get_contents("$serverdir\\tools\\scripts\\config.php");
		$hconf=str_replace("\$mysqlpass=\"$mysqlpass\";", "\$mysqlpass=\"$passa\";", $hconf);
		file_put_contents("$serverdir\\tools\\scripts\\config.php", $hconf);
		mysqli_query($db, "FLUSH PRIVILEGES");
		echo "Password changed<br/>";
	}
}
?>
<form action="changemysqlpass.php" method="POST">
<table border="0" class="txt">
	<tr>
		<td>New password:</td>
		<td><input class="txt" type="password" name="passa"/></td>
	</tr>
	<tr>
		<td>Retype password:</td>
		<td><input class="txt" type="password" name="passb"/></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="submit" value="Save"/></td>
	</tr>
</table>
</form>

<? include_once("../scripts/foother.php");?>