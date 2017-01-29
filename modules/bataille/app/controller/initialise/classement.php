<?php
	require_once("header.php");
	
	\modules\bataille\app\controller\Bataille::getPoints();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();