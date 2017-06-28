<?php
	chdir(dirname(__FILE__));
	
	$page_root = "cron.php";
	require("vendor/autoload.php");
	require("config/initialise.php");
	
	$dbc = \core\App::getDb();
	
	//récupération de chaque fichier cron
	require("modules/messagerie/app/cron/delete_message.php");
	require("modules/bataille/app/cron/delete_compte.php");