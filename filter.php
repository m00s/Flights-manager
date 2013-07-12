<?php
require_once "component/db_connection.php";

echo"<form name=\"form1\" method=\"GET\" action=\"research.php\">
		<table cellpadding=\"8\"style=\"background-color:white; border-right:1px solid #99FFFF; border-bottom:2px solid #99FFFF; padding:7px\">
			<tr>
				<td align=\"right\" class=\"sm\">Da:</td>
				<td>
				<select name=\"da\">";
				$query="SELECT nomeCitta FROM Luoghi ORDER BY nomeCitta ASC";
				$partenze=mysql_query($query,$conn);
					while ($row = mysql_fetch_array($partenze))
						echo "<option>$row[0]</option>"	;
			echo"
				</select>
				</td>
			</tr>
			<tr>
				<td align=\"right\" class=\"sm\">A:</td>
				<td><select name=\"a\">";
				$query="SELECT nomeCitta FROM Luoghi ORDER BY nomeCitta ASC";
				$arrivi=mysql_query($query,$conn);
					while ($row = mysql_fetch_array($arrivi))
						echo "<option>$row[0]</option>"	;
			echo"
				</select>
				</td>
			</tr>
				<tr>
					<td align=\"right\" class=\"mm\">Giorno Andata:</td>
					<td align=\"left\"><input name=\"giornoa\" type=\"TEXT\" value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" /></td>
				</tr>
				<tr>
					<td align=\"right\" class=\"mm\">Giorno Ritorno:</td>
					<td align=\"left\"><input name=\"giornor\" type=\"TEXT\" value=\"(aaaa/mm/dd)\" onblur=\"if(this.value=='') this.value='(aaaa/mm/dd)';\" onfocus=\"if(this.value=='(aaaa/mm/dd)') this.value='';\" /></td>
				</tr>
				<tr>
					<td align=\"right\" class=\"mm\">Solo Andata:</td>
					<td align=\"left\"><input name=\"tipo\" type=\"radio\" value=\"andata\"></td>
				</tr>
				<tr>
					<td align=\"right\" class=\"mm\">Andata Ritorno:</td>
					<td align=\"left\"><input name=\"tipo\" type=\"radio\" value=\"andatarit\"></td>
				</tr>
				<tr>
					<td align=\"right\" class=\"mm\">Con scali:</td>
					<td align=\"left\"><input name=\"checkscali\" type=\"checkbox\"></td>
				</tr>
					<tr align=\"right\">
						<td></td>
							<td height=\"40px\">
								<input type=\"image\" src =\"images/search.png\" height=\"50\" width=\"50\" alt=\"Submit\" />
							</td>
							</tr>
						</table>
		</form>";
?>