<?php
	\modules\bataille\app\controller\Bataille::getGoupeUnite()->setCreerGroupe($_POST['nombre_unite'], $_POST['nom_unite'], $_POST['type_unite'], $_POST['nom_groupe']);
	
	header("location:".WEBROOT."bataille");