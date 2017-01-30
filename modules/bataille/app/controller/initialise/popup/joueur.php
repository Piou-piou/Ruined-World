<?php
	
	\modules\bataille\app\controller\Bataille::getBase()->getBasesJoueur($_POST['id_identite']);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();