<?php
	require_once("header.php");
	
	\modules\bataille\app\controller\Bataille::getPoints($_GET['next']);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();