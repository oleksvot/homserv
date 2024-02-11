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

# hosts file path
function gethostspath() {
	global $cnf;
	if (@$cnf['hostsnotwrite']) {
		$cnf['hostsnotwrite']=false;
		saveconfig($cnf);
	}
	$windir=getenv("windir");
	$os=getenv("OS");
	if (stristr($os, "NT")) {
		$hostpath="$windir\\system32\\drivers\\etc\\hosts";
	} else {
		$hostpath="$windir\\hosts";
	}
	$fp=@fopen($hostpath, 'a+');
	if (!$fp) {
		exec("ATTRIB \"$hostpath\" -R");
		$fp=@fopen($hostpath, 'a+');
		if (!$fp) {
			$cnf['hostsnotwrite']=true;
			saveconfig($cnf);
		}
	}
	if ($fp) {fclose($fp);}
	return $hostpath;
}

function addtohosts($values) {
	if (!is_array($values)) {$values[0]=$values;}
	foreach ($values as $n=>$v) {
		$values[$n]=preg_replace('/^https\-/', '', $v);
	}
	$values=array_unique($values);
	$hostspath=gethostspath();
	$lines=file($hostspath);
	foreach ($lines as $n=>$line) {
		if (preg_match('/^(\S+)\s+(\S+)/', $line, $m)) {
			if (in_array($m[2], $values) or ($m[2]=='localhost')) {
				unset($lines[$n]);
			}
		}
	}
	$lines[]="127.0.0.1\tlocalhost\r\n";
	$lines[]=rtrim(array_pop($lines))."\r\n";
	foreach ($values as $v) {
		$v=trim($v);
		if (($v=="") or ($v=='localhost')) {continue;}
		if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $v)) {continue;}
		$lines[]="127.0.0.1\t$v\r\n";
	}
	$lines[]=rtrim(array_pop($lines))."\r\n";
	file_put_contents($hostspath, ltrim(implode("", $lines)));
}

function delfromhosts($values) {
	if (!is_array($values)) {$values=array($values);}
	$hostspath=gethostspath();
	$lines=file($hostspath);
	foreach ($lines as $n=>$line) {
		if (preg_match('/^(\S+)\s+(\S+)/', $line, $m)) {
			if (in_array($m[2], $values) or ($m[2]=='localhost')) {
				unset($lines[$n]);
			}
		}
	}
	$lines[]="127.0.0.1\tlocalhost\r\n";
	$lines[]=rtrim(array_pop($lines))."\r\n";
	file_put_contents($hostspath, ltrim(implode("", $lines)));
}

# virtual hosts list
function getvhostlist() {
	global $vhostfile;
	$lines=file($vhostfile);
	$vhost=false;
	$vhostname="";
	$vhosts=array();
	foreach ($lines as $n=>$line) {
		$line=trim($line);
		if ($line=="") {continue;}
		if (($line[0]=="#") and (substr($line, 0, 6)!="#vhost")) {continue;}
		$m=preg_split("/\s+/", $line);
		if ($m[0]=="#vhost") {
			$vhostname=$m[1];
		}
		if (preg_match("/^\<\s*VirtualHost.*/is", $line)) {
			$vhost=true;
			continue;
		}
		if (preg_match("/^\<\s*\/\s*VirtualHost.*/is", $line)) {
			$vhost=false;
			continue;
		}
		
		if ($vhost and $vhostname and isset($m[0])) {
			$p=preg_split("/\s+/", $line, 2);
			if (isset($p[0]) and isset($p[1])) {
				$pname=strtolower($p[0]);
				$pvalue=$p[1];
				preg_match('/^\s*\"(.*)\"\s*$/s', $pvalue, $mq);
				if (isset($mq[1])) {
					$qvalue=$mq[1];
				} else {
					preg_match('/^\s*\"(.*)\"\s*common\s*$/s', $pvalue, $mqc);
					if (isset($mqc[1])) {
						$qvalue=$mqc[1];
					} else {
						$qvalue=$pvalue;
					}
				}
				
				if ($pname=='documentroot') {
					$vhosts[$vhostname]['documentroot']=$qvalue;
				} elseif ($pname=='servername') {
					$vhosts[$vhostname]['servername']=$qvalue;
				} elseif ($pname=='customlog') {
					$vhosts[$vhostname]['customlog']=$qvalue;
				} elseif ($pname=='errorlog') {
					$vhosts[$vhostname]['errorlog']=$qvalue;
				} else {
					@$vhosts[$vhostname]['other'].=$line."\r\n";
				}
				
			} 	
		}
	}
	return $vhosts;
}

# removing virtual host
function delvhost($hostname) {
	global $vhostfile;
	$lines=file($vhostfile);
	$delvhost=false;
	$vhostname="";
	foreach ($lines as $n=>$line) {
		$line=trim($line);
		if (($line=="") and (trim(@$lines[$n+1])=="")) {unset($lines[$n]);}
		if ($line=="") {continue;}
		if (($line[0]=="#") and (substr($line, 0, 6)!="#vhost")) {continue;}
		$m=preg_split("/\s+/", $line);
		if ($m[0]=="#vhost") {
			$vhostname=$m[1];
			if ($vhostname==$hostname) {
				unset($lines[$n]);
			}
			
		}
		if (preg_match("/^\<\s*VirtualHost.*/is", $line)) {
			if ($vhostname==$hostname) {
				unset($lines[$n]);
				$delvhost=true;
			}
			continue;
		}
		if (preg_match("/^\<\s*\/\s*VirtualHost.*/is", $line)) {
			if ($vhostname==$hostname) {
				unset($lines[$n]);
				$delvhost=false;
			}
			continue;
		}
		if ($delvhost) {
			unset($lines[$n]);
		}
	}
	file_put_contents($vhostfile, implode("", $lines));
	delfromhosts($hostname);
}

# creating virtual host
function addvhost($hostname, $documentroot, $errorlog="", $customlog="", $other="", $cf="", $ckf="") {
	$vhostlist=getvhostlist();
	if (array_key_exists($hostname, $vhostlist)) {
		delvhost($hostname);
	}
	$dhostname=preg_replace('/^https\-/', '', $hostname);
	forsedir($documentroot);
	$documentroot=str_replace("\\", "/", delslash($documentroot));
	$errorlog=str_replace("\\", "/", $errorlog);
	$customlog=str_replace("\\", "/", $customlog);
	$cf=str_replace("\\", "/", $cf);
	$ckf=str_replace("\\", "/", $ckf);
	
	$other=trim($other);
	$other=str_replace("\r\n", "\n", $other);
	$other=str_replace("\n", "\r\n\t", $other);
	global $vhostfile;
	$vcont=file_get_contents($vhostfile);
	$hostcont="\r\n";
	$hostcont.="#vhost $hostname\r\n";
	if (preg_match('/^https\-/', $hostname)) {
		$hostcont.="<VirtualHost *:443>\r\n";
		$hostcont.="SSLEngine on\r\n";
		$hostcont.="SSLCertificateFile \"$cf\"\r\n";
		$hostcont.="SSLCertificateKeyFile \"$ckf\"\r\n";
	} else {
		$hostcont.="<VirtualHost *:80>\r\n";
	}
	$hostcont.="\tDocumentRoot \"$documentroot\"\r\n";
	$hostcont.="\tServerName \"$dhostname\"\r\n";
	if ($errorlog!="") {
		$hostcont.="\tErrorLog \"$errorlog\"\r\n";
	}
	if ($customlog!="") {
		$hostcont.="\tCustomLog \"$customlog\" common\r\n";
	}
	if ($other!="") {
		$hostcont.="\t$other\r\n";
	}
	$hostcont.="</VirtualHost>\r\n";
	$vcont=rtrim($vcont)."\r\n".$hostcont;
	file_put_contents($vhostfile, $vcont);
}

function autohosts() {
	global $serverdir, $homedir, $vhostfile;
	if (!is_dir("$homedir\\localhost")) mkdir("$homedir\\localhost");
	$aserverdir=str_replace("\\", "/", $serverdir);
	$ahomedir=str_replace("\\", "/", $homedir);
	$vhostlist=getvhostlist();
	foreach ($vhostlist as $n=>$v) {
		if (strstr($v['documentroot'], $ahomedir)) {
			if (!is_dir($v['documentroot'])) {
				delvhost($n);
			}
		}
	}
	$vcont=file_get_contents($vhostfile);
	$dh=opendir($homedir);
	$add=false;
	while (($dir=readdir($dh))!==false) {
		if (($dir=='.') or ($dir=='..') or ($dir=='default')) {continue;}
		if (!preg_match('/^[A-Za-z0-9\.\_\-]+$/', $dir)) {continue;}
		if (!is_dir("$homedir\\$dir")) {continue;}
		if (!isset($vhostlist[$dir])) {
			$add=true;
			$hostcont="\r\n";
			$hostcont.="#vhost $dir\r\n";
			$hostcont.="<VirtualHost *:80>\r\n";
			$hostcont.="\tDocumentRoot \"$ahomedir/$dir\"\r\n";
			$hostcont.="\tServerName \"$dir\"\r\n";
			$hostcont.="\tErrorLog \"$aserverdir/apache/logs/{$dir}_error.log\"\r\n";
			$hostcont.="\tCustomLog \"$aserverdir/apache/logs/{$dir}_access.log\" common\r\n";
			$hostcont.="\tScriptAlias \"/cgi-bin/\" \"$ahomedir/$dir/cgi-bin/\"\r\n";
			$hostcont.="</VirtualHost>\r\n";
			$vcont=rtrim($vcont)."\r\n".$hostcont;
		}
	}
	closedir($dh);
	if ($add) {
		file_put_contents($vhostfile, $vcont);
	}
}

function trayhosts($vlist) {
	global $serverdir, $cnf;
	$traycont=file_get_contents("$serverdir\\tools\\TrayTool.ini");
	$oldtrayconf=$traycont;
	if (stristr($traycont, '#vhosts')) {
		$traycont=preg_replace('/#vhosts.*$/is', '', $traycont);
	}
	$traycont=rtrim($traycont);
	$traycont.="\r\n\r\n#vhosts\r\n";
	$i=12;
	while (strstr($traycont, $i)) {$i++;}
	foreach ($vlist as $v) {
		if ($v=='hstools') continue;
		if (preg_match('/^https\-/', $v)) {continue;}
		$uvlist[]=$v;
		$traycont.="item4.$i=$v\r\n";
		$traycont.="action$i=show $cnf[browser] http://$v\r\n";
		$i++;
	}
	if ($traycont!=$oldtrayconf) {
		file_put_contents("$serverdir\\tools\\TrayTool.ini", $traycont);
	}
	file_put_contents("$serverdir\\tools\\vhostlist.txt", implode("\r\n", $uvlist));
}
?>