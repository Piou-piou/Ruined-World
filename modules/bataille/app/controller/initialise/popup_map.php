<?php
	$map = new \modules\bataille\app\controller\Map($_POST['id_base']);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();