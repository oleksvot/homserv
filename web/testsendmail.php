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

$title="HomServ | sendmail test";
include_once("../scripts/config.php");
if (isset($_POST['submit'])) {
	mail($_POST['to'], $_POST['subject'], $_POST['body'], "From: $cnf[smtpfrom]\r\n");
}
?>

<div class="zag">Send a test email</div>

<form action="testsendmail.php" method="POST">
<table border="0">
  <tr>
    <td>To:</td>
    <td><input class="txt" type="text" name="to" value="test@localhost"/></td>
  </tr>
  <tr>
    <td>Subject:</td>
    <td><input class="txt" type="text" name="subject" value="The test message"/></td>
  </tr>
  <tr>
    <td>Body:</td>
    <td><textarea class="txt" name="body" rows="3">This body of the message</textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="Send"/>
    </td>
  </tr>
</table>
</form>


<? include_once("../scripts/foother.php");?>