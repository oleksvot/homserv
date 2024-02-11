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

$zdpath=str_replace("debugnext.php", "zenddebug.php", __FILE__);
$zdcont=file_get_contents($zdpath);
$zdcont=str_replace('$HomServDebug=false;//'.'Debug label', '$HomServDebug=true;//'.'Debug label', $zdcont);
$fp=fopen($zdpath, 'w');
fwrite($fp, $zdcont);
fclose($fp);
?>