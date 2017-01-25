<?php
	$nombre_unite = $_POST["nombre_unite"];
	$nom_unite = $_POST["nom_unite"];
	$type_unite = $_POST["type_unite"];
	
	$count = count($nombre_unite);
	for ($i=0 ; $i<$count ; $i++) {
		$new_tab[] = [
			"nombre_unite" =>  $nombre_unite[$i],
			"nom_unite" =>  $nom_unite[$i],
			"type_unite" =>  $type_unite[$i]
		];
	}
	
	echo("<pre>");
	print_r($new_tab);
	echo("</pre>");