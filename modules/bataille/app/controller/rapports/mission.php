<?php
	$message = "
	<h3>Mission : ".$infos["mission"]["nom_mission"]."</h3>
	<article>".$infos["mission"]["description"]."</article>
	
	<h3>Ressources gagnées</h3>
	<ul>
		<li>Eau : 0</li>
		<li>Electricite : 0</li>
		<li>Fer : 0</li>
		<li>Fuel : 0</li>
		<li>Nourriture : ".$infos["ressource_gagnee"]."</li>
	</ul>
	
	<h3>Rapport de troupes</h3>
	<ul>
		<li>Troupes envoyés : ".$infos["unites_envoyees"]."</li>
		<li>Troupes revenues : ".$infos["unites_revenues"]."</li>
	</ul>
	";