<?php
	require_once(MODULEROOT."bataille/app/controller/initialise/index.php");
	
	$messagerie = new modules\messagerie\app\controller\Messagerie("messages envoyés");
	
	$arr = array_merge($arr, $messagerie->getValues());