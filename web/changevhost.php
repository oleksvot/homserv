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
$hp=$vhostlist[$_REQUEST['hostname']];
?>
<div class="zag">Settings for <?=$_REQUEST['hostname']?></div>

<form name="aform" action="addvhost.php" method="POST">
<table border="0" class="txt">
<input type="hidden" name="change" value="1"/>
<input type="hidden" name="hostname" value="<?=$_REQUEST['hostname']?>"/>
  <tr>
  	<td>Home folder:</td>
    <td><input class="txt" type="text" name="documentroot" value="<?=$hp['documentroot']?>"/></td>
  </tr>
  <tr>
  	<td>Extra options:</td>
    <td><textarea class="txt" name="other" cols="3" rows="3" wrap="off"><?=$hp['other']?></textarea></td>
  </tr>
  <tr>
  	<td colspan="2" align="center">
  	  <input type="submit" name="submit" value="Save"/>
  	</td>
  </tr>
</table>
</form>

<? include_once("../scripts/foother.php");?>