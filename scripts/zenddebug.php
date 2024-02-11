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

$HomServDebug=false;//Debug label. Do not change.
if (isset($_SERVER['HomServDebug'])) {
	$serverdir=str_replace("\\tools\\scripts\\zenddebug.php", "", __FILE__);
	extract(unserialize(file_get_contents("$serverdir\\temp\\debugenv.txt")));
	$_SERVER['HomServDebug']=true;
}
if ((!isset($_SERVER['HomServDebug'])) and 
(isset($_REQUEST['debug_host']) and isset($_REQUEST['debug_port']) 
and isset($_REQUEST['original_url']) or $HomServDebug)) {
	set_time_limit(0);
	$serverdir=str_replace("\\tools\\scripts\\zenddebug.php", "", __FILE__);
	$fp=fopen("$serverdir\\temp\\debugenv.txt", 'w');
	fwrite($fp, serialize($GLOBALS));
	fclose($fp);
	
	$cnflines=file("$serverdir\\tools\\config.ini");
	foreach ($cnflines as $line) {
		if (strstr($line, 'zendstudiodir')) {
			$f=explode('=', $line);
			$zendstudiodir=trim($f[1]);
		}
	}
	if ($HomServDebug) {
		$zdcont=file_get_contents(__FILE__);
		$zdcont=str_replace('$HomServDebug=true;//'.'Debug label', '$HomServDebug=false;//'.'Debug label', $zdcont);
		$fp=fopen(__FILE__, 'w');
		fwrite($fp, $zdcont);
		fclose($fp);
	}
	
	$c="php-cgi.exe";
	$debuglib="$zendstudiodir\\lib\php5\ZendDebuggerLocal.dll";
	$phpinicont=file_get_contents("$serverdir\\apache\\bin\\php.ini");
	
	
	$phpinicont=preg_replace('/\s*zend\_extension\_.+\n/i', '', $phpinicont);
	$phpinicont.="\r\n\r\nzend_extension_ts=\"$debuglib\"\r\nzend_debugger.allow_hosts=127.0.0.1/32\r\n";
	$fp=fopen("$serverdir\\temp\\php.ini", 'w');
	fwrite($fp, $phpinicont);
	fclose($fp);
	putenv("HomServDebug=run");
	putenv("PHPRC=$serverdir\\temp");
	putenv("SCRIPT_FILENAME=$_SERVER[SCRIPT_FILENAME]");
		
	$descriptorspec=array(
		0=>array("pipe", "r"),
		1=>array("pipe", "w"),
		2=>array("file", "$serverdir\\temp\\zenddebug.log", "a")
	);
	
	$process=proc_open($c, $descriptorspec, $pipes);
	$output="";
	while (!feof($pipes[1])) {
		$output.=fread($pipes[1], 1);
	}
	fclose($pipes[1]);
	$res=explode("\r\n\r\n", $output);
	if (isset($res[1])) {
		$headers=explode("\r\n", $res[0]);
		foreach ($headers as $h) {
			if ($h!="") {header($h);}
		}
		echo @$res[1];
	} else {
		echo $output;
	}
	exit;
}
?>