<?php
	\modules\bataille\app\controller\Bataille::getMissionsAleatoire()->setCommencerMission($_POST["id_mission"], $_POST["nombre_unite"], $_POST["nom_unite"], $_POST["type_unite"], $_POST['id_groupe']);
	
	header("location:".WEBROOT."bataille");