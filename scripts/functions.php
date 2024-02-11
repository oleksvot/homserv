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

// checks if the substring needle is present in the haystack
function instr($haystack, $needle) {
    if (strpos($haystack, $needle)===false) return false; else return true; 
}

# remove slash at the end of a line
function delslash($str) {
	$str=trim($str);
	if ($str[strlen($str)-1]=="\\") {
		$str=substr($str, 0, strlen($str)-1);
	}
	return $str;
}

# creates a directory, if there are non-existent directories in the path, then they are also created
function forsedir($dir, &$firstdir=false) {
	$dir=str_replace("/", "\\", $dir);
	$dir=delslash($dir);
	$e=explode("\\", $dir);
	$d=$e[0];
	$fdir=false;
	for ($i=1; $i<count($e); $i++) {
		$d.="\\".$e[$i];
		if (!is_dir($d)) {
			mkdir($d);
			if (!$fdir) {$firstdir=$d;$fdir=true;}
		}
	}
}

# checks if the program is running
function checkrun($filename) {
	$fp=@fopen($filename, 'a+');
	if (!$fp) {
		return true;
	} else {
		fclose($fp);
		return false;
	}
	
}

function exectool($command, $mode="show", $dir="") {
	global $serverdir;
	$c="START /D \"$serverdir\\tools\\scripts\" exectool.exe";
	if (is_array($command)) {
		foreach ($command as $v) {
			if (isset($v['dir'])) {$c.=" -cd \"$v[dir]\"";}
			$v['command']=str_replace('"', '::quot::', $v['command']);
			$c.=" -$v[mode] \"$v[command]\"";
		}
	} else {
		if ($dir!="") {$c.=" -cd \"$dir\"";}
		$command=str_replace('"', '::quot::', $command);
		$c.=" -$mode \"$command\"";
	}
	exec($c);
	
	/*$fp=fopen("$serverdir\\tools\\exectool.log", 'a+');
	fwrite($fp, "$c\n\n");
	fclose($fp);*/
}

# load config.ini
function loadconfig() {
	global $serverdir;
	$configpath="$serverdir\\tools\\config.ini";
	$lines=file($configpath);
	foreach ($lines as $line) {
		if (($line[0]!="#") and  ($line[0]!=";")) {
			$p=explode("=", $line, 2);
			if (count($p)>1) {$cnf[trim($p[0])]=trim($p[1]);}  
		}
	}
	return $cnf;
}

# save config.ini
function saveconfig($cnf) {
	global $serverdir;
	$configpath="$serverdir\\tools\\config.ini";
	$lines=file($configpath);
	$names=array_keys($cnf);
	foreach ($lines as $n => $line) {
		if (($line[0]!="#") and  ($line[0]!=";")) {
			$p=explode("=", $line, 2);
			if (count($p)>1) {
				$name=trim($p[0]);
				if (in_array($name, $names)) {
					$lines[$n]="$name=$cnf[$name]\r\n";
					unset($cnf[$name]);
				} else {
					unset($lines[$n]);
					unset($cnf[$name]);
				}
			}  
		}
	}
	foreach ($cnf as $n=>$v) {$lines[]="$n=$v\r\n";}
	file_put_contents($configpath, implode("", $lines));
}



# components management
function hcontrol($action, $program, $wait=true, $printmes=false, $mode='hide') {
	global $cnf, $programs, $api, $phpexe, $serverdir, $icmd, $apachepid;
	if (!isset($icmd)) {$icmd="";}
	if ($program=='all') {
		$plist=array_keys($programs);
	} elseif ($program=='cnf') {
		foreach ($programs as $n => $v) {
			if ((@$v['option'] and strstr($v['option'], 'runalways')) or @$cnf[$n]) {$plist[]=$n;}
		}
	} elseif (isset($programs[$program])) {
		$plist[]=$program;
	} else {
		return;
	}
	
	if (in_array('apache', $plist) and ($api=='apache')) {
		exectool("$phpexe -n ..\\..\\tools\\scripts\\control.php $action $program", "hide", dirname($phpexe));
		return false;
	}
	
	if ($api!='apache') {
		putenv("AP_PARENT_PID=");
	}
	
	if (($action=='stop') or ($action=='restart')) {
		$etime=time()+60;
		foreach ($programs as $n => $v) {
			if (!in_array($n, $plist)) {continue;}
			if (($action=='restart') and @$v['option'] and strstr($v['option'], 'norestart')) {continue;}
			if (!checkrun($v['exe'])) {
				if ($printmes) {echo "$v[name] is not running\r\n";}
				continue;
			}
			if ($printmes) {echo "Shutdown $v[name]\r\n";}
			exec($v['stop']);
			if ($wait and !(@$v['option'] and strstr($v['option'], 'nowait'))) {
				sleep(1);
				while (checkrun($v['exe'])) {
					sleep(1);
					if (time()>$etime) {exit;}
				}
			}
			if ($printmes) {echo "$v[name] was shutdown\r\n";}
		}
	}
	
	if (($action=='start') or ($action=='restart')) {
		foreach ($programs as $n => $v) {
			if (!in_array($n, $plist)) {continue;}
			if (($action=='restart') and @$v['option'] and strstr($v['option'], 'norestart')) {continue;}
			if (checkrun($v['exe'])) {
				if ($printmes) {echo "$v[name] already running\r\n";}
				continue;
			}
			if ($n=='apache') {
				file_put_contents("$serverdir\\apache\\logs\\error.log", "");
				file_put_contents("$serverdir\\apache\\logs\\runerr.log", "");
				if (file_exists($apachepid)) {@unlink($apachepid);}
			}
			if ($n=='mysql5') {
				foreach (glob("$serverdir\\mysql5\\data\\*.err") as $fn) unlink($fn);
				foreach (glob("$serverdir\\mysql5\\data\\*.pid") as $fn) unlink($fn);
			}
			if ($printmes) {echo "Start $v[name]\r\n";}
			if (!empty($v['mode'])) {$m=$v['mode'];} else {$m=$mode;} 
			$execlist[]=array('dir'=>dirname($v['exe']), 'mode'=>$m, 'command'=>$icmd.$v['start']);
		}
	}
	
	if (!empty($execlist)) exectool($execlist);
}

# call apache restart
function restartapache($goto="") {
	if (empty($goto)) {
		$goto='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$goto=urlencode($goto);
	header("Location: http://$_SERVER[HTTP_HOST]/cp/restart.php?goto=$goto");
	ob_end_clean();
	exit;
}

function merror($mes, $title="Error - HomServ", $restart=true) {
	global $serverdir;
	if ($restart) $restart='restart'; else $restart='norestart';
	$mes="$restart\r\n$title\r\n$mes";
	file_put_contents("$serverdir\\temp\\error.txt", $mes);
	exectool("$serverdir\\StartHomServ.exe -error");
}

# replacement via preg_replace in a multidimensional array
function arrayreplace($s, $r, $ar) {
	foreach ($ar as $n=>$a) {
		unset($ar[$n]);
		$n=preg_replace($s, $r, $n);
		$ar[$n]=$a;
		if (is_array($ar[$n])) {
			$ar[$n]=arrayreplace($s, $r, $ar[$n]);
		} else {
			$ar[$n]=preg_replace($s, $r, $ar[$n]);
		}
	}
	return $ar;
}

# ftp sync - check changes on local
function readlocal($ignore, $list, $mdir, $apath="/", $up=false) {
	$dh=opendir($mdir.$apath);
	while (($file=readdir($dh))!==false) {
		if (($file=='.') or ($file=='..')) {continue;}
		if (is_file($mdir.$apath.$file)) {
			if (in_array($apath.$file, $ignore)) {continue;}
			if ((@$list[$apath.$file]['size']!=filesize($mdir.$apath.$file)) or (@$list[$apath.$file]['date']!=filemtime($mdir.$apath.$file))) {
				if (!$up) $up = [];
				$up[]=array('type'=>'load', 'path'=>$apath.$file , 'size'=>filesize($mdir.$apath.$file), 'date'=>filemtime($mdir.$apath.$file));
			}
		} else {
			if (in_array($apath.$file, $ignore)) {continue;}
			if (empty($list[$apath.$file])) {
				if (!$up) $up = [];
				$up[]=array('type'=>'mkdir', 'path'=>$apath.$file);
			}
			$up=readlocal($ignore, $list, $mdir, $apath.$file."/", $up);
		}
	}
	closedir($dh);
	return $up;
}

# ftp sync - upload from local to remove
function updatelocal($ftp, $ldir, $rdir, $up, &$llist, &$rlist) {
	foreach ($up as $u) {
		if ($u['type']=='mkdir') {
			if (!empty($ftp)) {$ftp->mkdir($rdir.$u['path']);}
			$llist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'dir');
			$rlist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'dir');
		}
		if ($u['type']=='load') {
			if (!empty($ftp)) {$ftp->put($ldir.$u['path'], $rdir.$u['path'], true, FTP_BINARY);}
			$llist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'file', 'size'=>$u['size'], 'date'=>$u['date']);
			if (!empty($ftp)) {
				if (@$lastls!=$u['path']) {
					$ls=$ftp->ls(dirname($rdir.$u['path']));
					$lastls=$u['path'];
				}
				foreach ($ls as $l) {
					if ($l['name']==basename($u['path'])) {
						$rlist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'file', 'size'=>$l['size'], 'date'=>$l['stamp']);
						break;
					}
				}
			}
			
		}
	}
}

# ftp sync - check changes on ftp
function readftp($ignore, $list, $ftp, $mdir, $apath="/", $up=false) {
	$ftp->cd($mdir.$apath);
	$ls=$ftp->ls();
	foreach ($ls as $l) {
		$file=$l['name'];
		if (($file=='.') or ($file=='..')) {continue;}
		if ($l['is_dir']!='d') {
			if (in_array($apath.$file, $ignore)) {continue;}
			if ((@$list[$apath.$file]['size']!=$l['size']) or (@$list[$apath.$file]['date']!=$l['stamp'])) {
				if (!$up) $up = [];
				$up[]=array('type'=>'load', 'path'=>$apath.$file, 'size'=>$l['size'], 'date'=>$l['stamp']);
			}
		} else {
			if (in_array($apath.$file, $ignore)) {continue;}
			if (empty($list[$apath.$file])) {
				if (!$up) $up = [];
				$up[]=array('type'=>'mkdir', 'path'=>$apath.$file);
			}
			$up=readftp($ignore, $list, $ftp, $mdir, $apath.$file."/", $up);
		}
	}
	return $up;
}

# ftp sync - download from ftp to local
function updateftp($ftp, $rdir, $ldir, $up, &$llist, &$rlist) {
	if (!empty($ftp)) {$ftp->cd($rdir);}
	foreach ($up as $u) {
		if ($u['type']=='mkdir') {
			if (!empty($ftp)) {mkdir($ldir.$u['path']);}
			$rlist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'dir');
			$llist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'dir');
		}
		if ($u['type']=='load') {
			if (!empty($ftp)) {$ftp->get($rdir.$u['path'], $ldir.$u['path'], true, FTP_BINARY);}
			$rlist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'file', 'size'=>$u['size'], 'date'=>$u['date']);
			if (!empty($ftp)) {$llist[$u['path']]=array('parent'=>dirname($u['path']), 'name'=>basename($u['path']), 'type'=>'file', 'size'=>filesize($ldir.$u['path']), 'date'=>filemtime($ldir.$u['path']));}
		}
	}
}

// install exe wrapper
function installwrapper(&$wrapperconf) {
	global $serverdir;
	if (empty($wrapperconf)) {return;}
	foreach ($wrapperconf as $n=>$w) {
		if (isset($w['wpath']) and file_exists($w['wpath'])) {continue;}
		$wpath=str_replace("/", "\\", $w['path']);
		if (!preg_match('/^\w\:/', $wpath)) {
			$wpath=substr($serverdir, 0, 2).$wpath;
		}
		if (!preg_match('/\.exe$/i', $wpath)) {
			$wpath.='.exe';
		}
		$wrapperconf[$n]['wpath']=$wpath;
		forsedir(dirname($wpath), $firstdir);
		if (!$firstdir) {$firstdir=$wpath;}
		$f=file_get_contents("$serverdir\\tools\\wrapper.bin");
		$rpath=$w['rpath'];
		# filename 3377-3642
		for ($i=0; $i<strlen($rpath); $i++) {
			$f[3377+$i]=$rpath[$i];
		}
		file_put_contents($wpath, $f);
		exec("ATTRIB +H \"$firstdir\"");
	}
}

// set browser path
function setbrowser($b=false) {
	global $cnf, $serverdir;
	if (!empty($cnf['browser'])) {$oldb=$cnf['browser'];} else {$oldb='explorer';}

	if (!$b) {
		$chrome = getenv('SystemDrive')."\\Program Files\\Google\\Chrome\\Application\\chrome.exe";
		if (file_exists($chrome)) {
			$b = "\"$chrome\"";
		}
	}

	if (!$b) {
		$c="REG QUERY HKEY_CLASSES_ROOT\http\shell\open\command /ve";
		$descriptorspec=array(0=>array("pipe", "r"),1=>array("pipe", "w"),2=>array("pipe", "a"));
		$process=proc_open($c, $descriptorspec, $pipes);
		$output="";
		while (!feof($pipes[1])) {$output.=fread($pipes[1], 1);}
		fclose($pipes[1]);
		preg_match('/REG\_SZ\s*(.*)$/s', $output, $m);
		$b=@$m[1];
		$b=str_replace('"%1"', "", $b);
		$b=str_replace('%1', "", $b);
		$b=trim($b);
		if (empty($b)) {$b="explorer";}
	}
	$cnf['browser']=$b;
	$traycont=file_get_contents("$serverdir\\tools\\TrayTool.ini");
	$traycont=str_replace($oldb, $b, $traycont);
	file_put_contents("$serverdir\\tools\\TrayTool.ini", $traycont);
	saveconfig($cnf);
}

?>