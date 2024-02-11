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


$db=mysqli_connect('localhost', 'root', $mysqlpass);
if (isset($_REQUEST['del'])) {
	mysqli_query($db, "DROP DATABASE $_REQUEST[db]");
	echo "Database $_REQUEST[db] successfully deleted";
} else {?>
<form action="delmysqlbase.php" method="GET">
<input type="hidden" name="del" value="true"/>
<input type="hidden" name="db" value="<?=$_REQUEST['db']?>"/>
Are you sure you want to delete the database <?=$_REQUEST['db']?>?<br/>
<br/>

<input type="submit" value="Yes"/>&nbsp;&nbsp;
<input type="button" value="No" onclick="history.back()"/>

</form>
<?}?>
<br/><div align="center"><a href="mysql.php">[Back]</a></div>
<? include_once("../scripts/foother.php");?>