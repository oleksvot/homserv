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

include_once("../scripts/config.php");
if (!checkrun($cnf['dbglistener'])) {
	exectool(basename($cnf['dbglistener']), 'show', dirname($cnf['dbglistener']));
	sleep(2);
}
$url=urldecode(trim($_SERVER['QUERY_STRING']));
if (empty($url)) exit('no url');
$url=str_replace('?DBGSESSID=1@clienthost:7869', '', $url);
$url=str_replace('&DBGSESSID=1@clienthost:7869', '', $url);
if (strstr($url, '?')) {
	$url.="&DBGSESSID=1@clienthost:7869";
} else {
	$url.="?DBGSESSID=1@clienthost:7869";
}
header("Location: $url");
?>