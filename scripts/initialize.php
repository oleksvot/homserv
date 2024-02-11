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

if (!is_dir("$serverdir\\mysql5\\data")) rename("$serverdir\\mysql5\\data-default", "$serverdir\\mysql5\\data");

# list of files in which paths need to be replaced
$configfiles[]=$apacheconf;
$configfiles[]=$vhostfile;
$configfiles[]="$serverdir\\apache\\bin\\php.ini";
$configfiles[]="$serverdir\\apache\\conf\\extra";
$configfiles[]="$serverdir\\mysql5\\my.ini";
$configfiles[]="$serverdir\\tools\\updateconf.txt";
$configfiles[]="$serverdir\\tools\\wrapperconf.txt";
$configfiles[]="$serverdir\\tools\\sethosts.bat";
$configfiles[]="$serverdir\\php8";
$configfiles[]="$serverdir\\php8\\pear";
$configfiles[]="$serverdir\\php8\\pear\\.registry";

# replacement of paths in conf. files
if ((strtolower($cnf['oldserverdir'])!=strtolower($serverdir)) or 
(strtolower($cnf['oldhomedir'])!=strtolower($homedir))) {
	$r1oldhomedir=preg_quote($cnf['oldhomedir'], '/');
	$r2oldhomedir=preg_quote(str_replace("\\", "/", $cnf['oldhomedir']), '/');
	$r1oldserverdir=preg_quote($cnf['oldserverdir'], '/');
	$r2oldserverdir=preg_quote(str_replace("\\", "/", $cnf['oldserverdir']), '/');
	foreach ($configfiles as $dir) {
		if (!is_file($dir)) {
			$dh=opendir($dir);
			while (($f=readdir($dh))!==false) {
				$configfiles[]="$dir\\$f";
			}
			closedir($dh);
		}
	}
	foreach ($configfiles as $file) {
		if (!is_file($file)) {continue;}
		echo "Update $file\r\n";
		$text=file_get_contents($file);
		$text=str_replace("#PEAR_Config 0.9\n", "", $text);
		if ($a=@unserialize($text)) {
			if (strtolower($cnf['oldhomedir'])!=strtolower($homedir)) {
				$a=arrayreplace("/$r1oldhomedir/i", $homedir, $a);
				$a=arrayreplace("/$r2oldhomedir/i", str_replace("\\", "/", $homedir), $a);
			}
			if (strtolower($cnf['oldserverdir'])!=strtolower($serverdir)) {
				$a=arrayreplace("/$r1oldserverdir/i", $serverdir, $a);
				$a=arrayreplace("/$r2oldserverdir/i", str_replace("\\", "/", $serverdir), $a);
			}
			$text=serialize($a);
		} else {
			if (strtolower($cnf['oldhomedir'])!=strtolower($homedir)) {
				$text=preg_replace("/$r1oldhomedir/i", $homedir, $text);
				$text=preg_replace("/$r2oldhomedir/i", str_replace("\\", "/", $homedir), $text);
			}
			if (strtolower($cnf['oldserverdir'])!=strtolower($serverdir)) {
				$text=preg_replace("/$r1oldserverdir/i", $serverdir, $text);
				$text=preg_replace("/$r2oldserverdir/i", str_replace("\\", "/", $serverdir), $text);
			}
		}
		file_put_contents($file, $text);
	}
	$cnf['oldserverdir']=$serverdir;
	$cnf['oldhomedir']=$homedir;
	if ($cnf['homedir']!="") {$cnf['homedir']=$homedir;}
	saveconfig($cnf);
}

?>