<?php
	$message = "
	<h3>Invitation à rejoindre la faction : ".$infos["nom_faction"]."</h3>
	<article>Pour rejoindre la faction <a  href='#'class='open-popup' popup='popup-base' faction='".$infos["id_faction"]."'>".$infos["nom_faction"]."</a>
	il vous suffit <a href='#' class='open-popup' popup='popup-base'  nom_batiment='amabssade' nom_batiment_sql='ambassade'>d'accepter l'invitation</a></article>
	";