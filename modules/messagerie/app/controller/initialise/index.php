<?php
	
	require_once(MODULEROOT."bataille/app/controller/initialise/index.php");

	$messagerie = new modules\messagerie\app\controller\Messagerie("boite réception");

	$arr = array_merge($arr, $messagerie->getValues());