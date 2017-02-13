<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	
	if ($forum->getFactionPlayer() == true) {
		$forum->getPermissionsMembre($forum->getIdFaction());
	}
	
	$forum->getListeForum();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();