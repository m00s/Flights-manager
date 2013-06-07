<? session_start(); ?>
<?
echo"
<div id=\"bannerAdmin\">
	<table>
		<tr width=\"100%\">
			<td> Benvenuto $_SESSION[email] </td>
			<td align=\"right\"> <a href=\"../login.php?cmd=out\"> esci </a> </td>
		</tr>
	</table>
</div>";
?>
