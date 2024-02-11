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

$title="HomServ | MySQL databases";
include_once("../scripts/config.php");
?>
<div class="zag">MySQL databases</div>
<?
if (!@$db=mysqli_connect('127.0.0.1', 'root', $mysqlpass)) {
	echo "Error. No MySQL connection<br/>";
	include_once("../scripts/foother.php");
	exit;
}?>
<table border="0">
<?$res=mysqli_query($db, "SHOW DATABASES");
for ($n=0; $n<mysqli_num_rows($res); $n++) {
	$a=mysqli_fetch_array($res);?>
<tr>
	<td><a href="/pma/index.php?db=<?=$a['Database']?>"><?=$a['Database']?></a></td>
	<? if (($a['Database']!='information_schema') and ($a['Database']!='mysql') and ($a['Database']!='phpmyadmin') and ($a['Database']!='performance_schema') and ($a['Database']!='sys')) {?>
	<td><a href="delmysqlbase.php?db=<?=$a['Database']?>">[del]</a></td>
	<? }?>
</tr>
<? }?>
</table>
<div class="zag">Create database and user</div>
<i>Using this form, you can also add a user to an existing database
or change user password</i>
<form action="addmysqlbase.php" method="POST">
<table border="0" class="txt">
	<tr>
		<td>Database name:</td>
		<td><input type="text" name="db" class="txt"/></td>
	</tr>
	<tr>
		<td>Collation:</td>
		<td><select xml:lang="en" dir="ltr" name="collation">
                    <option value="">���������</option>
                    <option value="">&nbsp;</option>
                    <optgroup label="armscii8" title="ARMSCII-8 Armenian">
                        <option value="armscii8_bin" title="���������, ��������">armscii8_bin</option>

                        <option value="armscii8_general_ci" title="���������, �������������������">armscii8_general_ci</option>
                    </optgroup>
                    <optgroup label="ascii" title="US ASCII">
                        <option value="ascii_bin" title="�������-����������� (������������), ��������">ascii_bin</option>
                        <option value="ascii_general_ci" title="�������-����������� (������������), �������������������">ascii_general_ci</option>
                    </optgroup>
                    <optgroup label="big5" title="Big5 Traditional Chinese">

                        <option value="big5_bin" title="��������� ������������, ��������">big5_bin</option>
                        <option value="big5_chinese_ci" title="��������� ������������, �������������������">big5_chinese_ci</option>
                    </optgroup>
                    <optgroup label="binary" title="Binary pseudo charset">
                        <option value="binary" title="��������">binary</option>
                    </optgroup>
                    <optgroup label="cp1250" title="Windows Central European">

                        <option value="cp1250_bin" title="����������-����������� (������������), ��������">cp1250_bin</option>
                        <option value="cp1250_croatian_ci" title="����������, �������������������">cp1250_croatian_ci</option>
                        <option value="cp1250_czech_cs" title="�������, �����������������">cp1250_czech_cs</option>
                        <option value="cp1250_general_ci" title="����������-����������� (������������), �������������������">cp1250_general_ci</option>
                    </optgroup>
                    <optgroup label="cp1251" title="Windows Cyrillic">
                        <option value="cp1251_bin" title="������������� (������������), ��������">cp1251_bin</option>

                        <option value="cp1251_bulgarian_ci" title="����������, �������������������">cp1251_bulgarian_ci</option>
                        <option value="cp1251_general_ci" title="������������� (������������), �������������������">cp1251_general_ci</option>
                        <option value="cp1251_general_cs" title="������������� (������������), �����������������">cp1251_general_cs</option>
                        <option value="cp1251_ukrainian_ci" title="����������, �������������������">cp1251_ukrainian_ci</option>
                    </optgroup>
                    <optgroup label="cp1256" title="Windows Arabic">
                        <option value="cp1256_bin" title="��������, ��������">cp1256_bin</option>

                        <option value="cp1256_general_ci" title="��������, �������������������">cp1256_general_ci</option>
                    </optgroup>
                    <optgroup label="cp1257" title="Windows Baltic">
                        <option value="cp1257_bin" title="���������� (������������), ��������">cp1257_bin</option>
                        <option value="cp1257_general_ci" title="���������� (������������), �������������������">cp1257_general_ci</option>
                        <option value="cp1257_lithuanian_ci" title="���������, �������������������">cp1257_lithuanian_ci</option>
                    </optgroup>

                    <optgroup label="cp850" title="DOS West European">
                        <option value="cp850_bin" title="�������-����������� (������������), ��������">cp850_bin</option>
                        <option value="cp850_general_ci" title="�������-����������� (������������), �������������������">cp850_general_ci</option>
                    </optgroup>
                    <optgroup label="cp852" title="DOS Central European">
                        <option value="cp852_bin" title="����������-����������� (������������), ��������">cp852_bin</option>
                        <option value="cp852_general_ci" title="����������-����������� (������������), �������������������">cp852_general_ci</option>

                    </optgroup>
                    <optgroup label="cp866" title="DOS Russian">
                        <option value="cp866_bin" title="�������, ��������">cp866_bin</option>
                        <option value="cp866_general_ci" title="�������, �������������������">cp866_general_ci</option>
                    </optgroup>
                    <optgroup label="cp932" title="SJIS for Windows Japanese">
                        <option value="cp932_bin" title="��������, ��������">cp932_bin</option>

                        <option value="cp932_japanese_ci" title="��������, �������������������">cp932_japanese_ci</option>
                    </optgroup>
                    <optgroup label="dec8" title="DEC West European">
                        <option value="dec8_bin" title="�������-����������� (������������), ��������">dec8_bin</option>
                        <option value="dec8_swedish_ci" title="��������, �������������������">dec8_swedish_ci</option>
                    </optgroup>
                    <optgroup label="eucjpms" title="UJIS for Windows Japanese">

                        <option value="eucjpms_bin" title="��������, ��������">eucjpms_bin</option>
                        <option value="eucjpms_japanese_ci" title="��������, �������������������">eucjpms_japanese_ci</option>
                    </optgroup>
                    <optgroup label="euckr" title="EUC-KR Korean">
                        <option value="euckr_bin" title="���������, ��������">euckr_bin</option>
                        <option value="euckr_korean_ci" title="���������, �������������������">euckr_korean_ci</option>
                    </optgroup>

                    <optgroup label="gb2312" title="GB2312 Simplified Chinese">
                        <option value="gb2312_bin" title="��������� ����������, ��������">gb2312_bin</option>
                        <option value="gb2312_chinese_ci" title="��������� ����������, �������������������">gb2312_chinese_ci</option>
                    </optgroup>
                    <optgroup label="gbk" title="GBK Simplified Chinese">
                        <option value="gbk_bin" title="��������� ����������, ��������">gbk_bin</option>
                        <option value="gbk_chinese_ci" title="��������� ����������, �������������������">gbk_chinese_ci</option>

                    </optgroup>
                    <optgroup label="geostd8" title="GEOSTD8 Georgian">
                        <option value="geostd8_bin" title="����������, ��������">geostd8_bin</option>
                        <option value="geostd8_general_ci" title="����������, �������������������">geostd8_general_ci</option>
                    </optgroup>
                    <optgroup label="greek" title="ISO 8859-7 Greek">
                        <option value="greek_bin" title="���������, ��������">greek_bin</option>

                        <option value="greek_general_ci" title="���������, �������������������">greek_general_ci</option>
                    </optgroup>
                    <optgroup label="hebrew" title="ISO 8859-8 Hebrew">
                        <option value="hebrew_bin" title="�����, ��������">hebrew_bin</option>
                        <option value="hebrew_general_ci" title="�����, �������������������">hebrew_general_ci</option>
                    </optgroup>
                    <optgroup label="hp8" title="HP West European">

                        <option value="hp8_bin" title="�������-����������� (������������), ��������">hp8_bin</option>
                        <option value="hp8_english_ci" title="����������, �������������������">hp8_english_ci</option>
                    </optgroup>
                    <optgroup label="keybcs2" title="DOS Kamenicky Czech-Slovak">
                        <option value="keybcs2_bin" title="�������������, ��������">keybcs2_bin</option>
                        <option value="keybcs2_general_ci" title="�������������, �������������������">keybcs2_general_ci</option>
                    </optgroup>

                    <optgroup label="koi8r" title="KOI8-R Relcom Russian">
                        <option value="koi8r_bin" title="�������, ��������">koi8r_bin</option>
                        <option value="koi8r_general_ci" title="�������, �������������������">koi8r_general_ci</option>
                    </optgroup>
                    <optgroup label="koi8u" title="KOI8-U Ukrainian">
                        <option value="koi8u_bin" title="����������, ��������">koi8u_bin</option>
                        <option value="koi8u_general_ci" title="����������, �������������������">koi8u_general_ci</option>

                    </optgroup>
                    <optgroup label="latin1" title="cp1252 West European">
                        <option value="latin1_bin" title="�������-����������� (������������), ��������">latin1_bin</option>
                        <option value="latin1_danish_ci" title="�������, �������������������">latin1_danish_ci</option>
                        <option value="latin1_general_ci" title="�������-����������� (������������), �������������������">latin1_general_ci</option>
                        <option value="latin1_general_cs" title="�������-����������� (������������), �����������������">latin1_general_cs</option>
                        <option value="latin1_german1_ci" title="�������� (�������), �������������������">latin1_german1_ci</option>

                        <option value="latin1_german2_ci" title="�������� (���������� �����), �������������������">latin1_german2_ci</option>
                        <option value="latin1_spanish_ci" title="���������, �������������������">latin1_spanish_ci</option>
                        <option value="latin1_swedish_ci" title="��������, �������������������">latin1_swedish_ci</option>
                    </optgroup>
                    <optgroup label="latin2" title="ISO 8859-2 Central European">
                        <option value="latin2_bin" title="����������-����������� (������������), ��������">latin2_bin</option>
                        <option value="latin2_croatian_ci" title="����������, �������������������">latin2_croatian_ci</option>

                        <option value="latin2_czech_cs" title="�������, �����������������">latin2_czech_cs</option>
                        <option value="latin2_general_ci" title="����������-����������� (������������), �������������������">latin2_general_ci</option>
                        <option value="latin2_hungarian_ci" title="����������, �������������������">latin2_hungarian_ci</option>
                    </optgroup>
                    <optgroup label="latin5" title="ISO 8859-9 Turkish">
                        <option value="latin5_bin" title="��������, ��������">latin5_bin</option>
                        <option value="latin5_turkish_ci" title="��������, �������������������">latin5_turkish_ci</option>

                    </optgroup>
                    <optgroup label="latin7" title="ISO 8859-13 Baltic">
                        <option value="latin7_bin" title="���������� (������������), ��������">latin7_bin</option>
                        <option value="latin7_estonian_cs" title="���������, �����������������">latin7_estonian_cs</option>
                        <option value="latin7_general_ci" title="���������� (������������), �������������������">latin7_general_ci</option>
                        <option value="latin7_general_cs" title="���������� (������������), �����������������">latin7_general_cs</option>
                    </optgroup>

                    <optgroup label="macce" title="Mac Central European">
                        <option value="macce_bin" title="����������-����������� (������������), ��������">macce_bin</option>
                        <option value="macce_general_ci" title="����������-����������� (������������), �������������������">macce_general_ci</option>
                    </optgroup>
                    <optgroup label="macroman" title="Mac West European">
                        <option value="macroman_bin" title="�������-����������� (������������), ��������">macroman_bin</option>
                        <option value="macroman_general_ci" title="�������-����������� (������������), �������������������">macroman_general_ci</option>

                    </optgroup>
                    <optgroup label="sjis" title="Shift-JIS Japanese">
                        <option value="sjis_bin" title="��������, ��������">sjis_bin</option>
                        <option value="sjis_japanese_ci" title="��������, �������������������">sjis_japanese_ci</option>
                    </optgroup>
                    <optgroup label="swe7" title="7bit Swedish">
                        <option value="swe7_bin" title="��������, ��������">swe7_bin</option>

                        <option value="swe7_swedish_ci" title="��������, �������������������">swe7_swedish_ci</option>
                    </optgroup>
                    <optgroup label="tis620" title="TIS620 Thai">
                        <option value="tis620_bin" title="���, ��������">tis620_bin</option>
                        <option value="tis620_thai_ci" title="���, �������������������">tis620_thai_ci</option>
                    </optgroup>
                    <optgroup label="ucs2" title="UCS-2 Unicode">

                        <option value="ucs2_bin" title="������ (������������), ��������">ucs2_bin</option>
                        <option value="ucs2_czech_ci" title="�������, �������������������">ucs2_czech_ci</option>
                        <option value="ucs2_danish_ci" title="�������, �������������������">ucs2_danish_ci</option>
                        <option value="ucs2_esperanto_ci" title="���������, �������������������">ucs2_esperanto_ci</option>
                        <option value="ucs2_estonian_ci" title="���������, �������������������">ucs2_estonian_ci</option>
                        <option value="ucs2_general_ci" title="������ (������������), �������������������">ucs2_general_ci</option>

                        <option value="ucs2_hungarian_ci" title="����������, �������������������">ucs2_hungarian_ci</option>
                        <option value="ucs2_icelandic_ci" title="����������, �������������������">ucs2_icelandic_ci</option>
                        <option value="ucs2_latvian_ci" title="����������, �������������������">ucs2_latvian_ci</option>
                        <option value="ucs2_lithuanian_ci" title="���������, �������������������">ucs2_lithuanian_ci</option>
                        <option value="ucs2_persian_ci" title="����������, �������������������">ucs2_persian_ci</option>
                        <option value="ucs2_polish_ci" title="��������, �������������������">ucs2_polish_ci</option>

                        <option value="ucs2_roman_ci" title="�������-�����������, �������������������">ucs2_roman_ci</option>
                        <option value="ucs2_romanian_ci" title="���������, �������������������">ucs2_romanian_ci</option>
                        <option value="ucs2_slovak_ci" title="���������, �������������������">ucs2_slovak_ci</option>
                        <option value="ucs2_slovenian_ci" title="����������, �������������������">ucs2_slovenian_ci</option>
                        <option value="ucs2_spanish2_ci" title="��������� ������������, �������������������">ucs2_spanish2_ci</option>
                        <option value="ucs2_spanish_ci" title="���������, �������������������">ucs2_spanish_ci</option>

                        <option value="ucs2_swedish_ci" title="��������, �������������������">ucs2_swedish_ci</option>
                        <option value="ucs2_turkish_ci" title="��������, �������������������">ucs2_turkish_ci</option>
                        <option value="ucs2_unicode_ci" title="������ (������������), �������������������">ucs2_unicode_ci</option>
                    </optgroup>
                    <optgroup label="ujis" title="EUC-JP Japanese">
                        <option value="ujis_bin" title="��������, ��������">ujis_bin</option>
                        <option value="ujis_japanese_ci" title="��������, �������������������">ujis_japanese_ci</option>

                    </optgroup>
                    <optgroup label="utf8" title="UTF-8 Unicode">
                        <option value="utf8_bin" title="������ (������������), ��������">utf8_bin</option>
                        <option value="utf8_czech_ci" title="�������, �������������������">utf8_czech_ci</option>
                        <option value="utf8_danish_ci" title="�������, �������������������">utf8_danish_ci</option>
                        <option value="utf8_esperanto_ci" title="���������, �������������������">utf8_esperanto_ci</option>
                        <option value="utf8_estonian_ci" title="���������, �������������������">utf8_estonian_ci</option>

                        <option value="utf8_general_ci" title="������ (������������), �������������������" selected="selected">utf8_general_ci</option>
                        <option value="utf8_hungarian_ci" title="����������, �������������������">utf8_hungarian_ci</option>
                        <option value="utf8_icelandic_ci" title="����������, �������������������">utf8_icelandic_ci</option>
                        <option value="utf8_latvian_ci" title="����������, �������������������">utf8_latvian_ci</option>
                        <option value="utf8_lithuanian_ci" title="���������, �������������������">utf8_lithuanian_ci</option>
                        <option value="utf8_persian_ci" title="����������, �������������������">utf8_persian_ci</option>

                        <option value="utf8_polish_ci" title="��������, �������������������">utf8_polish_ci</option>
                        <option value="utf8_roman_ci" title="�������-�����������, �������������������">utf8_roman_ci</option>
                        <option value="utf8_romanian_ci" title="���������, �������������������">utf8_romanian_ci</option>
                        <option value="utf8_slovak_ci" title="���������, �������������������">utf8_slovak_ci</option>
                        <option value="utf8_slovenian_ci" title="����������, �������������������">utf8_slovenian_ci</option>
                        <option value="utf8_spanish2_ci" title="��������� ������������, �������������������">utf8_spanish2_ci</option>

                        <option value="utf8_spanish_ci" title="���������, �������������������">utf8_spanish_ci</option>
                        <option value="utf8_swedish_ci" title="��������, �������������������">utf8_swedish_ci</option>
                        <option value="utf8_turkish_ci" title="��������, �������������������">utf8_turkish_ci</option>
                        <option value="utf8_unicode_ci" title="������ (������������), �������������������">utf8_unicode_ci</option>
                    </optgroup>
                </select></td>
	</tr>
	<tr>
		<td>Username:</td>
		<td><input type="text" name="userlogin" class="txt"/></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="text" name="userpass" class="txt"/></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="Create"/></td>
	</tr>
</table>
</form>
<br/>
<a href="changemysqlpass.php">Change MySQL root password</a><br/>
<? include_once("../scripts/foother.php");?>