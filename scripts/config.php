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

error_reporting(E_ALL);
if (function_exists('apache_get_version')) {
	$api='apache';
	ob_start();
} else {
	$api='cgi';
}

include_once("functions.php");
include_once("parsevhost.php");

# HomServ path
$serverdir=str_replace("\\tools\\scripts\\config.php", "", __FILE__);
$cnf=loadconfig();

# MySQL root password
$mysqlpass="homserv";

# HomServ version
$homservversion='3.0.1';

$programs['apache']=array('name'=>'Apache', 'version'=>'2.4.58',
'exe'=>"$serverdir\\apache\\bin\httpd.exe",
'start'=>"cmd.exe /V /C \"set AP_PARENT_PID=&& $serverdir\\apache\\bin\\httpd.exe 2>$serverdir\\apache\\logs\\runerr.log\"",
'stop'=>"TASKKILL /F /IM httpd.exe", 'option'=>'runalways');

$programs['mysql5']=array('name'=>'MySQL', 'version'=>'5.7.44',
'exe'=>"$serverdir\\mysql5\\bin\\mysqld.exe",
'start'=>"$serverdir\\mysql5\\bin\\mysqld.exe   --defaults-file=\"$serverdir\\mysql5\\my.ini\"   --user=root",
'stop'=>"TASKKILL /F /IM mysqld.exe");

$apacheconf="$serverdir\\apache\\conf\\httpd.conf";
$vhostfile="$serverdir\\tools\\vhost.conf";
$apachepid="$serverdir\\apache\\logs\\httpd.pid";
$updatefile="$serverdir\\tools\\updateconf.txt";

if (@$cnf['homedir']!="") {
	$homedir=$cnf['homedir'];
} else {
	$homedir="$serverdir\\home";
}

if (empty($cnf['browser'])) {
	setbrowser();
}

$phpexe="$serverdir\\apache\\bin\\php.exe";
$phpini="$serverdir\\apache\\bin\\php.ini";

if (is_dir("$serverdir\\temp\\upgrade")) {
	$dh=opendir("$serverdir\\temp\\upgrade");
	while (($file=readdir($dh))!==false) {
		if (preg_match('/\.php$/', $file)) {
			include_once("$serverdir\\temp\\upgrade\\$file");
		}
	}
	closedir($dh);
}

if ($cnf['update'] and $cnf['itime'] and (!in_array('startup', $_SERVER['argv'])) and (time()>$cnf['utime'])) {
    $cnf['utime']=time()+60*60*24*7;
    saveconfig($cnf);
    $buf=@file_get_contents("https://homserv.net/update.txt?v=$homservversion&d=$cnf[itime]");
    if (preg_match('|^https?://\S+$|', $buf)) {
        if (!instr($buf, "-$homservversion-")) exectool("$cnf[browser] \"https://homserv.net/\"");
    }
}

if (isset($title)) {include_once("header.php");}

?>