<?php
	$nom_batiment = $_POST['nom_batiment'];
	$nom_batiment_sql = $_POST['nom_batiment_sql'];

	\modules\bataille\app\controller\Bataille::getBatiment()->setCommencerConstruireBatiment($nom_batiment, $nom_batiment_sql);