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

$windir=getenv("windir");
$os=getenv("OS");
$username=getenv("USERNAME");
if (stristr($os, "NT")) {
	$hostpath="$windir\\system32\\drivers\\etc\\hosts";
} else {
	$hostpath="$windir\\hosts";
}

if (!file_exists($hostpath)) @file_put_contents($hostpath, '');

file_put_contents("y.txt", "Y\r\n");

$bat="CACLS \"$hostpath\" /G \"$username\":F < y.txt\r\n";
$bat=@iconv('CP1251', 'CP866', $bat);
file_put_contents("temp.bat", $bat);
exec('cmd.exe /C temp.bat');
unlink("temp.bat");

$bat="CACLS \"$hostpath\" /G \"$username\":F < y.txt\r\n";
$bat=@iconv('CP1251', 'CP866', $bat);
file_put_contents("temp.bat", $bat);
exec('cmd.exe /C temp.bat');
unlink("temp.bat");

$bat="CACLS \"$hostpath\" /G Все:F < y.txt\r\n";
$bat=@iconv('CP1251', 'CP866', $bat);
file_put_contents("temp.bat", $bat);
exec('cmd.exe /C temp.bat');
unlink("temp.bat");

$bat="CACLS \"$hostpath\" /G Все:F < y.txt\r\n";
$bat=@iconv('CP1251', 'UTF-8', $bat);
file_put_contents("temp.bat", $bat);
exec('cmd.exe /C temp.bat');
unlink("temp.bat");

$bat="CACLS \"$hostpath\" /G Users:F < y.txt\r\n";
$bat=@iconv('CP1251', 'UTF-8', $bat);
file_put_contents("temp.bat", $bat);
exec('cmd.exe /C temp.bat');
unlink("temp.bat");

unlink("y.txt");

exec("ATTRIB \"$hostpath\" -R");
$fp=fopen($hostpath, 'a+');
$hoststatus=false;
if ($fp) {
	$hoststatus=true;
	fclose($fp);
}


if (in_array('mes', $_SERVER['argv'])) {
    if ($hoststatus) {
    	echo 'OK';
    } else {
    	echo 'Error. Please run this file as Administrator.';
    }
    echo "\n";
}

?>
