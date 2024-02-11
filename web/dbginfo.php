<?php
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

$title="HomServ | dbg info";
include_once("../scripts/config.php");
?>
<br/>
Drag this link <a href="javascript:location.href='http://localhost/cp/phpxdbg.php?'+location.href">Debug this page</a> 
to your browser's link bar or bookmark it. To start debugging click on this link, being on the required page.<br>
Alternative: add a parameter to the url <b>?DBGSESSID=1@clienthost:7869</b> (DbgListener.exe should be running).<br><br>
<? include_once("../scripts/foother.php");?>