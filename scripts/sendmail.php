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

include_once("config.php");
ini_set('log_errors', 'on');
ini_set('error_log', "$serverdir\\temp\\sendmail.log");
ini_set('include_path', "$serverdir\\php8\\pear");

$dir="$serverdir\\email";
$d=date("d_m_Y_H_i_s");
$stdin = fopen('php://stdin', 'r');
$mes="";
while (!feof($stdin)) {
	$mes.=fread($stdin,1);
}
$mes=str_replace("\r\n", "\n", $mes);
$mes=str_replace("\n", "\r\n", $mes);
$fp=fopen("$dir\\$d.eml", "w");
fwrite($fp, $mes);
fclose($fp);

if ($cnf['phpmail']=='auto') {$smtphost='localhost';} else {$smtphost=$cnf['smtphost'];}
if (($cnf['phpmail']=='auto') or ($cnf['phpmail']=='smtp')) {
	preg_match('/^To:(.*)$/im', $mes, $ma);
	preg_match('/^CC:(.*)$/im', $mes, $mb);
	preg_match_all('/([a-z0-9\-\_\.]+\@[a-z0-9\-\_\.]+)/i', @$ma[1]." ".@$mb[1], $to);
	$emails=$to[1];
	if (count($to)==0) {exit;}
	if (!empty($cnf['smtpfrom'])) {
		$from=$cnf['smtpfrom'];
	} else {
		preg_match('/^From:(.*)$/im', $mes, $mc);
		preg_match('/([a-z0-9\-\_\.]+\@[a-z0-9\-\_\.]+)/i', $mc[1], $md);
		$from=@$md[1];
		if (empty($from)) {$from='admin@localhost';}
	}
	
	include_once('Net/SMTP.php');
	$smtp = new Net_SMTP($cnf['smtphost']);
	if (PEAR::isError($e = $smtp->connect())) {
    	errexit($e->getMessage());
	}
	if (!empty($cnf['smtplogin'])) {
		if (PEAR::isError($e = $smtp->auth($cnf['smtplogin'], $cnf['smtppass']))) {
    		errexit($e->getMessage());
		}
	}
	if (PEAR::isError($e = $smtp->mailFrom($from))) {
    	errexit($e->getMessage());
	}
	
	foreach ($emails as $email) {
		if (PEAR::isError($e = $smtp->rcptTo($email))) {
        	errexit($e->getMessage());
    	}
	}
	if (PEAR::isError($e = $smtp->data($mes))) {
    	errexit($e->getMessage());
	}
	$smtp->disconnect();
	
}

function errexit($text) {
	global $serverdir;
	$log=fopen("$serverdir\\temp\\sendmail.log", 'a+');
	fwrite($log, $text."\r\n");
	fclose($log);
	exit;
}

?>