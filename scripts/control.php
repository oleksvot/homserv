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

// control.php - start/stop HomServ
include_once("config.php");
echo "HomServ ver. $homservversion\r\n\r\n";



// action (start, stop, restart, startup)
if (!empty($_SERVER['argv'][1])) {
	$action=$_SERVER['argv'][1];
} else {
	$action=false;
}
// nohide mode - show console windows
if (in_array('nohide', $_SERVER['argv'])) {
	$mode='show';
	$icmd='cmd.exe /K';
} else {
	$mode='hide';
	$icmd='';
}
// start on windows login
if (($action=='startup') and $cnf['runstartup']) {$action='start';}
// traytool control file
$tstatus=file_exists("$serverdir\\tools\\control.txt");
if (($action=='start') or ($action=='restart')) {
	file_put_contents("$serverdir\\tools\\control.txt", time()); 
}
if (($action=='stop') or ($action=='startup')) {
	if (file_exists("$serverdir\\tools\\control.txt")) unlink("$serverdir\\tools\\control.txt");
}
if (file_exists("$serverdir\\tools\\TrayToolWait.ini")) {
	unlink("$serverdir\\tools\\TrayToolWait.ini");
}

if ($action=='trayhosts') {
	$vlist=array_keys(getvhostlist());
	trayhosts($vlist);
	exit;
}

// opening control panel
if (in_array('opencp', $_SERVER['argv'])) {
	if (checkrun($programs['apache']['exe'])) {
		exectool("$cnf[browser] \"http://localhost/cp/\"");
	} else {
		file_put_contents("$serverdir\\temp\\go.txt", "");
		exectool("$cnf[browser] \"$serverdir\\tools\\cp.html\"");
		$gowait=true;
	}
}
// opening vhost
if (in_array('openvhost', $_SERVER['argv'])) {
	$openvhost=$_SERVER['argv'][array_search('openvhost', $_SERVER['argv'])+1];
	if (checkrun($programs['apache']['exe'])) {
		exectool("$cnf[browser] \"http://$openvhost/\"");
	} else {
		file_put_contents("$serverdir\\temp\\go.txt", "http://$openvhost/");
		exectool("$cnf[browser] \"$serverdir\\tools\\cp.html\"");
		$gowait=true;
	}
}
// replace paths in config files
include_once("initialize.php");

if (($action=='start') or ($action=='stop') or ($action=='restart')) {
	// called components (all, by config, one)
	$program=@$_SERVER['argv'][2];
	if (($program!='all') and ($program!='cnf') and empty($programs[$program])) {
		$program='cnf';
	}
	// action
	hcontrol($action, $program, true, true, $mode);
}
// log removing
if (file_exists("$serverdir\\temp\\unlink.ini")) {
	$unlinkconf=file("$serverdir\\temp\\unlink.ini");
	foreach ($unlinkconf as $file) {
		@unlink(trim($file));
	}
	unlink("$serverdir\\temp\\unlink.ini");
}

if (($tstatus!=file_exists("$serverdir\\tools\\control.txt")) and !in_array('tray', $_SERVER['argv']) and checkrun("$serverdir\\tools\\TrayHomServ.exe")) {
	exec("TASKKILL /IM TrayHomServ.exe");
	exec("TASKKILL /IM TrayHomServ.exe");
	exectool("TrayHomServ.exe", 'show', "$serverdir\\tools");
} elseif (in_array('closetraytool', $_SERVER['argv'])) {
	// завершение traytool
	exec("TASKKILL /IM TrayHomServ.exe");
} elseif ((!checkrun("$serverdir\\tools\\TrayHomServ.exe")) and ($action!='stop')) {
	// запуск traytool
	exectool("TrayHomServ.exe", 'show', "$serverdir\\tools");
}
// auto vhost creation
if ($cnf['autohosts']) {
	autohosts();
}

$vlist=array_keys(getvhostlist());
if (($action=='stop') or ($action=='startup')) {
	// hosts file cleaning
	delfromhosts($vlist);
}

$cx = stream_context_create(array('http'=>array('proxy'=>'tcp://127.0.0.1:80')));

if (($action=='start') or ($action=='restart')) {
	// hosts file updating
	addtohosts($vlist);
	file_put_contents("$serverdir\\temp\\apachestart.txt", time());
	trayhosts($vlist);
	// awaiting Apache
	ini_set('default_socket_timeout', 5);
	$etime=time()+$cnf['swait'];
	while(1) {
		sleep(2);
		$elog=file_get_contents("$serverdir\\apache\\logs\\error.log");
		$rlog=file_get_contents("$serverdir\\apache\\logs\\runerr.log");
		if (!empty($rlog)) {
			if (instr($rlog, 'could not bind to address')) {
				$emes="The Apache port is occupied by another application. Check if you have other servers running, and also try disabling the firewall.\r\n";
			} else {
				$emes="An error occurred while starting Apache. If this error has not occurred before, reinstalling HomServ may solve the problem.\r\n";
			}
			$emes.="\r\nrunerr.log:\r\n$rlog\r\nerror.log:\r\n$elog\r\n";
			merror($emes);
			exit;
		}
		echo "Check Apache log\r\n";
		if (instr($elog, ': Child: Starting')) {
			$itest=file_get_contents("http://localhost/cp/itest.php", false, $cx);
			if (instr($itest, '{HomServ:OK}')) {
				preg_match('|<tr><td class="e">Loaded Configuration File\s*</td><td class="v">([^<]+)\s*</td></tr>|', $itest, $ma);
				$rphpini=trim($ma[1]);
				if (!file_exists($rphpini))  {
					merror("php.ini not found\r\n$rphpini\r\n");
					exit;
				} 
				if ($rphpini!=$phpini) {
					echo "Incorrect php.ini location\r\n";
					$rphpbak="$rphpini.bak";
					rename($rphpini, $rphpbak);
					clearstatcache();
					if (!file_exists($rphpini)) {
						merror("Contfict file $rphpini renamed to $rphpbak", "Warning - HomServ", false);
						exectool("$phpexe -n ..\\..\\tools\\scripts\\control.php restart", "hide", dirname($phpexe));
					} else {
						merror("Found conflict file $rphpini\r\nPlease remove or rename it.");
					}
					exit;
				}
				echo "Apache OK\r\n";
				break;
			}
		}
		if (time()>$etime) {
			$emes="Apache startup timed out.\r\n";
			if (checkrun($programs['apache']['exe'])) {
				$fp=fsockopen('127.0.0.1', 80, $errno, $errstr, 5);
				if ($fp) {
					$emes.="The process is running, the port is open, but not responding.\r\n";
				} else {
					$errstr = iconv('utf-8', 'windows-1251', $errstr);
					$emes.="The process is running, the port is not available.\r\n$errstr ($errno)\r\n";
				}
			} else {
				$emes.="The process is not running.\r\n";
			}
			$emes.="Check if you have other servers running, and also try disabling the firewall.\r\n";
			$emes.="\r\nrunerr.log:\r\n$rlog\r\nerror.log:\r\n$elog\r\n";
			merror($emes);
			exit;
		}
	}
	// awaiting MySQL
	if ($cnf['mysql5']) {
		$etime=time()+$cnf['swait'];
		foreach (glob("$serverdir\\mysql5\\data\\*.err") as $fn) $mlogfile=$fn;
		while(1) {
			sleep(2);
			$mlog=file_get_contents($mlogfile);
			echo "Check MySQL log\r\n";
			if (instr($mlog, 'ready for connections')) {
				echo "MySQL OK\r\n";
				break;
			}
			if (instr($mlog, 'Do you already have another mysqld server running on port')) {
				$emes="The MySQL port is being occupied by another application. Check if you have other servers running, and also try disabling the firewall.\r\n";
				$emes.="\r\n$mlog\r\n";
				merror($emes);
				exit;
			}
			if (time()>$etime) {
				$emes="MySQL startup timed out.\r\n";
				$emes.="\r\n$mlog\r\n";
				merror($emes);
				exit;
			}
		}
	}
	// browser startup test
	if (!empty($gowait)) {
		$etime=time()+$cnf['swait'];
		while(1) {
			sleep(2);
			echo "Check browser\r\n";
			if (!file_exists("$serverdir\\temp\\go.txt")) {
				echo "Browser OK\r\n";
				break;
			}
			if (time()>$etime) {
				echo "Timeout. Use explorer as default browser\r\n";
				setbrowser('explorer');
				exectool("$cnf[browser] \"$serverdir\\tools\\cp.html\"");
				break;
			}
		}
	}
}

$tempdir=getenv('TEMP');
if (!is_dir($tempdir)) @mkdir($tempdir);
$dfile="$tempdir\\HomServDir.txt";
if ((!file_exists($dfile)) or (file_get_contents($dfile)!=$serverdir)) file_put_contents($dfile, $serverdir);

?>