<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	$forum->getFactionPlayer();
	
	$forum->setSupprimerForum($_POST['id_forum']);