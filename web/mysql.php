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
                    <option value="">Сравнение</option>
                    <option value="">&nbsp;</option>
                    <optgroup label="armscii8" title="ARMSCII-8 Armenian">
                        <option value="armscii8_bin" title="Армянский, Двоичный">armscii8_bin</option>

                        <option value="armscii8_general_ci" title="Армянский, регистронезависимый">armscii8_general_ci</option>
                    </optgroup>
                    <optgroup label="ascii" title="US ASCII">
                        <option value="ascii_bin" title="Западно-Европейский (многоязычный), Двоичный">ascii_bin</option>
                        <option value="ascii_general_ci" title="Западно-Европейский (многоязычный), регистронезависимый">ascii_general_ci</option>
                    </optgroup>
                    <optgroup label="big5" title="Big5 Traditional Chinese">

                        <option value="big5_bin" title="Китайский традиционный, Двоичный">big5_bin</option>
                        <option value="big5_chinese_ci" title="Китайский традиционный, регистронезависимый">big5_chinese_ci</option>
                    </optgroup>
                    <optgroup label="binary" title="Binary pseudo charset">
                        <option value="binary" title="Двоичный">binary</option>
                    </optgroup>
                    <optgroup label="cp1250" title="Windows Central European">

                        <option value="cp1250_bin" title="Центрально-Европейский (многоязычный), Двоичный">cp1250_bin</option>
                        <option value="cp1250_croatian_ci" title="Хорватский, регистронезависимый">cp1250_croatian_ci</option>
                        <option value="cp1250_czech_cs" title="Чешский, регистрозависымый">cp1250_czech_cs</option>
                        <option value="cp1250_general_ci" title="Центрально-Европейский (многоязычный), регистронезависимый">cp1250_general_ci</option>
                    </optgroup>
                    <optgroup label="cp1251" title="Windows Cyrillic">
                        <option value="cp1251_bin" title="Кириллический (многоязычный), Двоичный">cp1251_bin</option>

                        <option value="cp1251_bulgarian_ci" title="Болгарский, регистронезависимый">cp1251_bulgarian_ci</option>
                        <option value="cp1251_general_ci" title="Кириллический (многоязычный), регистронезависимый">cp1251_general_ci</option>
                        <option value="cp1251_general_cs" title="Кириллический (многоязычный), регистрозависымый">cp1251_general_cs</option>
                        <option value="cp1251_ukrainian_ci" title="Украинский, регистронезависимый">cp1251_ukrainian_ci</option>
                    </optgroup>
                    <optgroup label="cp1256" title="Windows Arabic">
                        <option value="cp1256_bin" title="Арабский, Двоичный">cp1256_bin</option>

                        <option value="cp1256_general_ci" title="Арабский, регистронезависимый">cp1256_general_ci</option>
                    </optgroup>
                    <optgroup label="cp1257" title="Windows Baltic">
                        <option value="cp1257_bin" title="Балтийский (многоязычный), Двоичный">cp1257_bin</option>
                        <option value="cp1257_general_ci" title="Балтийский (многоязычный), регистронезависимый">cp1257_general_ci</option>
                        <option value="cp1257_lithuanian_ci" title="Литовский, регистронезависимый">cp1257_lithuanian_ci</option>
                    </optgroup>

                    <optgroup label="cp850" title="DOS West European">
                        <option value="cp850_bin" title="Западно-Европейский (многоязычный), Двоичный">cp850_bin</option>
                        <option value="cp850_general_ci" title="Западно-Европейский (многоязычный), регистронезависимый">cp850_general_ci</option>
                    </optgroup>
                    <optgroup label="cp852" title="DOS Central European">
                        <option value="cp852_bin" title="Центрально-Европейский (многоязычный), Двоичный">cp852_bin</option>
                        <option value="cp852_general_ci" title="Центрально-Европейский (многоязычный), регистронезависимый">cp852_general_ci</option>

                    </optgroup>
                    <optgroup label="cp866" title="DOS Russian">
                        <option value="cp866_bin" title="Русский, Двоичный">cp866_bin</option>
                        <option value="cp866_general_ci" title="Русский, регистронезависимый">cp866_general_ci</option>
                    </optgroup>
                    <optgroup label="cp932" title="SJIS for Windows Japanese">
                        <option value="cp932_bin" title="Японский, Двоичный">cp932_bin</option>

                        <option value="cp932_japanese_ci" title="Японский, регистронезависимый">cp932_japanese_ci</option>
                    </optgroup>
                    <optgroup label="dec8" title="DEC West European">
                        <option value="dec8_bin" title="Западно-Европейский (многоязычный), Двоичный">dec8_bin</option>
                        <option value="dec8_swedish_ci" title="Шведский, регистронезависимый">dec8_swedish_ci</option>
                    </optgroup>
                    <optgroup label="eucjpms" title="UJIS for Windows Japanese">

                        <option value="eucjpms_bin" title="Японский, Двоичный">eucjpms_bin</option>
                        <option value="eucjpms_japanese_ci" title="Японский, регистронезависимый">eucjpms_japanese_ci</option>
                    </optgroup>
                    <optgroup label="euckr" title="EUC-KR Korean">
                        <option value="euckr_bin" title="Корейский, Двоичный">euckr_bin</option>
                        <option value="euckr_korean_ci" title="Корейский, регистронезависимый">euckr_korean_ci</option>
                    </optgroup>

                    <optgroup label="gb2312" title="GB2312 Simplified Chinese">
                        <option value="gb2312_bin" title="Китайский упрощенный, Двоичный">gb2312_bin</option>
                        <option value="gb2312_chinese_ci" title="Китайский упрощенный, регистронезависимый">gb2312_chinese_ci</option>
                    </optgroup>
                    <optgroup label="gbk" title="GBK Simplified Chinese">
                        <option value="gbk_bin" title="Китайский упрощенный, Двоичный">gbk_bin</option>
                        <option value="gbk_chinese_ci" title="Китайский упрощенный, регистронезависимый">gbk_chinese_ci</option>

                    </optgroup>
                    <optgroup label="geostd8" title="GEOSTD8 Georgian">
                        <option value="geostd8_bin" title="Грузинский, Двоичный">geostd8_bin</option>
                        <option value="geostd8_general_ci" title="Грузинский, регистронезависимый">geostd8_general_ci</option>
                    </optgroup>
                    <optgroup label="greek" title="ISO 8859-7 Greek">
                        <option value="greek_bin" title="Греческий, Двоичный">greek_bin</option>

                        <option value="greek_general_ci" title="Греческий, регистронезависимый">greek_general_ci</option>
                    </optgroup>
                    <optgroup label="hebrew" title="ISO 8859-8 Hebrew">
                        <option value="hebrew_bin" title="Иврит, Двоичный">hebrew_bin</option>
                        <option value="hebrew_general_ci" title="Иврит, регистронезависимый">hebrew_general_ci</option>
                    </optgroup>
                    <optgroup label="hp8" title="HP West European">

                        <option value="hp8_bin" title="Западно-Европейский (многоязычный), Двоичный">hp8_bin</option>
                        <option value="hp8_english_ci" title="Английский, регистронезависимый">hp8_english_ci</option>
                    </optgroup>
                    <optgroup label="keybcs2" title="DOS Kamenicky Czech-Slovak">
                        <option value="keybcs2_bin" title="Чехословацкий, Двоичный">keybcs2_bin</option>
                        <option value="keybcs2_general_ci" title="Чехословацкий, регистронезависимый">keybcs2_general_ci</option>
                    </optgroup>

                    <optgroup label="koi8r" title="KOI8-R Relcom Russian">
                        <option value="koi8r_bin" title="Русский, Двоичный">koi8r_bin</option>
                        <option value="koi8r_general_ci" title="Русский, регистронезависимый">koi8r_general_ci</option>
                    </optgroup>
                    <optgroup label="koi8u" title="KOI8-U Ukrainian">
                        <option value="koi8u_bin" title="Украинский, Двоичный">koi8u_bin</option>
                        <option value="koi8u_general_ci" title="Украинский, регистронезависимый">koi8u_general_ci</option>

                    </optgroup>
                    <optgroup label="latin1" title="cp1252 West European">
                        <option value="latin1_bin" title="Западно-Европейский (многоязычный), Двоичный">latin1_bin</option>
                        <option value="latin1_danish_ci" title="Датский, регистронезависимый">latin1_danish_ci</option>
                        <option value="latin1_general_ci" title="Западно-Европейский (многоязычный), регистронезависимый">latin1_general_ci</option>
                        <option value="latin1_general_cs" title="Западно-Европейский (многоязычный), регистрозависымый">latin1_general_cs</option>
                        <option value="latin1_german1_ci" title="Немецкий (словарь), регистронезависимый">latin1_german1_ci</option>

                        <option value="latin1_german2_ci" title="Немецкий (телефонная книга), регистронезависимый">latin1_german2_ci</option>
                        <option value="latin1_spanish_ci" title="Испанский, регистронезависимый">latin1_spanish_ci</option>
                        <option value="latin1_swedish_ci" title="Шведский, регистронезависимый">latin1_swedish_ci</option>
                    </optgroup>
                    <optgroup label="latin2" title="ISO 8859-2 Central European">
                        <option value="latin2_bin" title="Центрально-Европейский (многоязычный), Двоичный">latin2_bin</option>
                        <option value="latin2_croatian_ci" title="Хорватский, регистронезависимый">latin2_croatian_ci</option>

                        <option value="latin2_czech_cs" title="Чешский, регистрозависымый">latin2_czech_cs</option>
                        <option value="latin2_general_ci" title="Центрально-Европейский (многоязычный), регистронезависимый">latin2_general_ci</option>
                        <option value="latin2_hungarian_ci" title="Венгерский, регистронезависимый">latin2_hungarian_ci</option>
                    </optgroup>
                    <optgroup label="latin5" title="ISO 8859-9 Turkish">
                        <option value="latin5_bin" title="Турецкий, Двоичный">latin5_bin</option>
                        <option value="latin5_turkish_ci" title="Турецкий, регистронезависимый">latin5_turkish_ci</option>

                    </optgroup>
                    <optgroup label="latin7" title="ISO 8859-13 Baltic">
                        <option value="latin7_bin" title="Балтийский (многоязычный), Двоичный">latin7_bin</option>
                        <option value="latin7_estonian_cs" title="Эстонский, регистрозависымый">latin7_estonian_cs</option>
                        <option value="latin7_general_ci" title="Балтийский (многоязычный), регистронезависимый">latin7_general_ci</option>
                        <option value="latin7_general_cs" title="Балтийский (многоязычный), регистрозависымый">latin7_general_cs</option>
                    </optgroup>

                    <optgroup label="macce" title="Mac Central European">
                        <option value="macce_bin" title="Центрально-Европейский (многоязычный), Двоичный">macce_bin</option>
                        <option value="macce_general_ci" title="Центрально-Европейский (многоязычный), регистронезависимый">macce_general_ci</option>
                    </optgroup>
                    <optgroup label="macroman" title="Mac West European">
                        <option value="macroman_bin" title="Западно-Европейский (многоязычный), Двоичный">macroman_bin</option>
                        <option value="macroman_general_ci" title="Западно-Европейский (многоязычный), регистронезависимый">macroman_general_ci</option>

                    </optgroup>
                    <optgroup label="sjis" title="Shift-JIS Japanese">
                        <option value="sjis_bin" title="Японский, Двоичный">sjis_bin</option>
                        <option value="sjis_japanese_ci" title="Японский, регистронезависимый">sjis_japanese_ci</option>
                    </optgroup>
                    <optgroup label="swe7" title="7bit Swedish">
                        <option value="swe7_bin" title="Шведский, Двоичный">swe7_bin</option>

                        <option value="swe7_swedish_ci" title="Шведский, регистронезависимый">swe7_swedish_ci</option>
                    </optgroup>
                    <optgroup label="tis620" title="TIS620 Thai">
                        <option value="tis620_bin" title="Таи, Двоичный">tis620_bin</option>
                        <option value="tis620_thai_ci" title="Таи, регистронезависимый">tis620_thai_ci</option>
                    </optgroup>
                    <optgroup label="ucs2" title="UCS-2 Unicode">

                        <option value="ucs2_bin" title="Юникод (многоязычный), Двоичный">ucs2_bin</option>
                        <option value="ucs2_czech_ci" title="Чешский, регистронезависимый">ucs2_czech_ci</option>
                        <option value="ucs2_danish_ci" title="Датский, регистронезависимый">ucs2_danish_ci</option>
                        <option value="ucs2_esperanto_ci" title="Эсперанто, регистронезависимый">ucs2_esperanto_ci</option>
                        <option value="ucs2_estonian_ci" title="Эстонский, регистронезависимый">ucs2_estonian_ci</option>
                        <option value="ucs2_general_ci" title="Юникод (многоязычный), регистронезависимый">ucs2_general_ci</option>

                        <option value="ucs2_hungarian_ci" title="Венгерский, регистронезависимый">ucs2_hungarian_ci</option>
                        <option value="ucs2_icelandic_ci" title="Исландский, регистронезависимый">ucs2_icelandic_ci</option>
                        <option value="ucs2_latvian_ci" title="Латвийский, регистронезависимый">ucs2_latvian_ci</option>
                        <option value="ucs2_lithuanian_ci" title="Литовский, регистронезависимый">ucs2_lithuanian_ci</option>
                        <option value="ucs2_persian_ci" title="Персидский, регистронезависимый">ucs2_persian_ci</option>
                        <option value="ucs2_polish_ci" title="Польский, регистронезависимый">ucs2_polish_ci</option>

                        <option value="ucs2_roman_ci" title="Западно-Европейский, регистронезависимый">ucs2_roman_ci</option>
                        <option value="ucs2_romanian_ci" title="Румынский, регистронезависимый">ucs2_romanian_ci</option>
                        <option value="ucs2_slovak_ci" title="Словацкий, регистронезависимый">ucs2_slovak_ci</option>
                        <option value="ucs2_slovenian_ci" title="Словенский, регистронезависимый">ucs2_slovenian_ci</option>
                        <option value="ucs2_spanish2_ci" title="Испанский традиционный, регистронезависимый">ucs2_spanish2_ci</option>
                        <option value="ucs2_spanish_ci" title="Испанский, регистронезависимый">ucs2_spanish_ci</option>

                        <option value="ucs2_swedish_ci" title="Шведский, регистронезависимый">ucs2_swedish_ci</option>
                        <option value="ucs2_turkish_ci" title="Турецкий, регистронезависимый">ucs2_turkish_ci</option>
                        <option value="ucs2_unicode_ci" title="Юникод (многоязычный), регистронезависимый">ucs2_unicode_ci</option>
                    </optgroup>
                    <optgroup label="ujis" title="EUC-JP Japanese">
                        <option value="ujis_bin" title="Японский, Двоичный">ujis_bin</option>
                        <option value="ujis_japanese_ci" title="Японский, регистронезависимый">ujis_japanese_ci</option>

                    </optgroup>
                    <optgroup label="utf8" title="UTF-8 Unicode">
                        <option value="utf8_bin" title="Юникод (многоязычный), Двоичный">utf8_bin</option>
                        <option value="utf8_czech_ci" title="Чешский, регистронезависимый">utf8_czech_ci</option>
                        <option value="utf8_danish_ci" title="Датский, регистронезависимый">utf8_danish_ci</option>
                        <option value="utf8_esperanto_ci" title="Эсперанто, регистронезависимый">utf8_esperanto_ci</option>
                        <option value="utf8_estonian_ci" title="Эстонский, регистронезависимый">utf8_estonian_ci</option>

                        <option value="utf8_general_ci" title="Юникод (многоязычный), регистронезависимый" selected="selected">utf8_general_ci</option>
                        <option value="utf8_hungarian_ci" title="Венгерский, регистронезависимый">utf8_hungarian_ci</option>
                        <option value="utf8_icelandic_ci" title="Исландский, регистронезависимый">utf8_icelandic_ci</option>
                        <option value="utf8_latvian_ci" title="Латвийский, регистронезависимый">utf8_latvian_ci</option>
                        <option value="utf8_lithuanian_ci" title="Литовский, регистронезависимый">utf8_lithuanian_ci</option>
                        <option value="utf8_persian_ci" title="Персидский, регистронезависимый">utf8_persian_ci</option>

                        <option value="utf8_polish_ci" title="Польский, регистронезависимый">utf8_polish_ci</option>
                        <option value="utf8_roman_ci" title="Западно-Европейский, регистронезависимый">utf8_roman_ci</option>
                        <option value="utf8_romanian_ci" title="Румынский, регистронезависимый">utf8_romanian_ci</option>
                        <option value="utf8_slovak_ci" title="Словацкий, регистронезависимый">utf8_slovak_ci</option>
                        <option value="utf8_slovenian_ci" title="Словенский, регистронезависимый">utf8_slovenian_ci</option>
                        <option value="utf8_spanish2_ci" title="Испанский традиционный, регистронезависимый">utf8_spanish2_ci</option>

                        <option value="utf8_spanish_ci" title="Испанский, регистронезависимый">utf8_spanish_ci</option>
                        <option value="utf8_swedish_ci" title="Шведский, регистронезависимый">utf8_swedish_ci</option>
                        <option value="utf8_turkish_ci" title="Турецкий, регистронезависимый">utf8_turkish_ci</option>
                        <option value="utf8_unicode_ci" title="Юникод (многоязычный), регистронезависимый">utf8_unicode_ci</option>
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