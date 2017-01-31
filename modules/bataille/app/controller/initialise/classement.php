<?php
	require_once("header.php");
	
	$points = new \modules\bataille\app\controller\Points($_GET['next']);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();