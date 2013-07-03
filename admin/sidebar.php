<?
/*
<div id=\"sidebar\">
	<ul>
		<h2>Categoria</h2>
		<li><a href=\"managevoli.php?option=insert\">Voli</a></li>
		<li><a href=\"manageviaggi.php?option=insert\">Viaggi diretti</a></li>
		<li><a href=\"manageviaggiscali.php?option=insert\">Viaggi con scali</a></li>
		<li><a href=\"manageassistenze.php\">Assistenze</a></li>	
		<li><a href=\"manageofferte.php\">Offerte</a></li>
		<li><a href=\"manageprivileges.php\">Gestisci privilegi</a></li>
	</ul>
</div>

*/
?>

<div id='cssmenu'>
<ul>
   <li><a href='#'><span>Home</span></a></li>
   <li><a href='managevoli.php?option=insert'><span>Voli</span></a></li>
   <li class='active has-sub'><a href='#'><span>Viaggi</span></a>
      <ul>
         <li><a href='manageviaggi.php?option=insert'><span>Diretti</span></a></li>
         <li><a href='manageviaggiscali.php?option=insert'><span>Con scali</span></a></li>
      </ul>
   </li>
   <li><a href='manageassistenze.php'><span>Assistenze</span></a></li>
   <li class='active has-sub'><a href='#'><span>Offerte</span></a>
      <ul>
         <li><a href='manageofferte.php?option=insert'><span>Inserisci</span></a></li>
         <li><a href='manageofferte.php?option=edit'><span>Modifica</span></a></li>
      </ul>
   </li>
   <li><a href='manageprivileges.php'><span>Gestisci privilegi</span></a></li>
</ul>
</div>