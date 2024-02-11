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

$title="HomServ | MySQL databases";
include_once("../scripts/config.php");


if (!$db=mysqli_connect('localhost', 'root', $mysqlpass)) {
	echo "Error. No MySQL connection<br/>";
}
if (!preg_match('/^[A-Za-z0-9\_]+$/', $_POST['db'])) {
	echo "Error. Incorrect database name.";
} elseif (!preg_match('/^[A-Za-z0-9\_]+$/', $_POST['userlogin'])) {
	echo "Error. Incorrect user name.";
} elseif (!preg_match('/^[A-Za-z0-9\_]+$/', $_POST['userpass'])) {
	echo "Error. Incorrect password.";
} elseif (strtolower($_POST['userlogin'])=='root') {
	echo "This username is not allowed. To change root password, use link bellow.";
} else {
	if (empty($_POST['collation'])) {
		mysqli_query($db, "CREATE DATABASE $_POST[db]");
	} else {
		if (!preg_match('/^[A-Za-z0-9\_]+$/', $_POST['collation'])) {exit;}
		preg_match('/([A-Za-z0-9]+)\_.*/', $_POST['collation'], $m);
		$charset=$m[1];
		$collation=$_POST['collation'];
		mysqli_query($db, "CREATE DATABASE $_POST[db] DEFAULT CHARACTER SET $charset COLLATE $collation");
	}
	
	
	mysqli_select_db($db, "mysql");
	$res=mysqli_query($db, "SELECT * FROM user WHERE user='$_POST[userlogin]'");
	if (mysqli_num_rows($res)!=0) {
		mysqli_query($db, "DELETE FROM user WHERE user='$_POST[userlogin]'");
	}
	mysqli_query($db, "INSERT INTO user (Host, User, authentication_string, File_priv, ssl_cipher, x509_issuer, x509_subject)
				VALUES ('localhost', '$_POST[userlogin]', PASSWORD('$_POST[userpass]'), 'Y', '', '', '')");
	
	mysqli_query($db, "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, References_priv, Index_priv, Alter_priv, Create_tmp_table_priv, Lock_tables_priv)
				VALUES ('localhost', '$_POST[db]', '$_POST[userlogin]', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')");
	mysqli_query($db, "FLUSH PRIVILEGES");
	echo "Database $_POST[db] with user $_POST[userlogin] successfully created";
}
?>
<br/><div align="center"><a href="mysql.php">[Back]</a></div>
<? include_once("../scripts/foother.php");?>