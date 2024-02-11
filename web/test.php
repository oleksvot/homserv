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

$title="HomServ | testing";
include_once("../scripts/config.php");
?>
<div class="zag">HTTPS support</div>
<a href="https://localhost">https://localhost</a><br/>
When you click on this link, the browser may display a message stating that the peer's certificate could not be verified. This is normal for self-signed certificates.<br/><br/>
<div class="zag">Virtual hosts support</div>
<a href="http://first/">first</a>&nbsp;&nbsp;
<a href="http://second/">second</a><br/><br/>
<b>If these links don't work:</b>
<ul>
<li>
	Check if your proxy server is disabled.<br/>
	<b>IE</b>: Tools > Internet Options > Connections.<br/>
	<b>Firefox</b>: Tools > Settings > Advanced > Network
</li>
<li>
	<b>IE</b>: Turn off automatic Internet connection.<br/>
	Tools > Internet Options > Connection > Remote Access Settings > Do Not Use
</li>
<li>
	<b>IE</b>: If you are prompted to connect to the Internet when you try to open a link, select &quot;Connect&quot; or &quot;Retry&quot;.<br/>
</li>
<li>
	If the problem continues to occur, then use a different browser.<br/>
</li>
<li>
	Open file <b>C:\WINDOWS\system32\drivers\etc\hosts</b> (Windows XP) or 
	<b>C:\WINDOWS\hosts</b> (Windows 98/2000) and make sure the lines are there 
	&quot;127.0.0.1 first&quot; è &quot;127.0.0.1 second&quot;. If they are missing,
	then this file is probably not writable or modification is blocked
	antivirus program. Please note that in order to correctly start HomServ
	You must be logged into Windows with administrator rights.
</li>
</ul><br/>
<div class="zag">MySQL</div>
<?
if (checkrun($programs['mysql5']['exe'])) {
	if (mysqli_connect('localhost', 'root', $mysqlpass)) {
		echo "MySQL connection successfully established";
	} else {
		echo "Failed to connect to MySQL";
	}
} else {
		echo "MySQL is not running";
}
?>
<br/><br/>
<div class="zag">SendMail emulator</div>
The SendMail emulator can work in three modes - only saving sent
emails, saving and sending to external SMTP or local SMTP.<br/>
In any case, sent emails are stored in the email directory.<br/>
You can change your SendMail settings in the "general settings" section<br/>
<br/>
<a href="testsendmail.php">Send a test email</a><br/>

<? include_once("../scripts/foother.php");?>