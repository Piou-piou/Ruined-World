<?php
	
	$groupe = new \modules\bataille\app\controller\GroupeUnite();
	$groupe->getAllGroupeUnite();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();