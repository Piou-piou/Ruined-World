<?php
	chdir(dirname(__FILE__));
	
	$page_root = "cron.php";
	require("vendor/autoload.php");
	require("config/initialise.php");
	
	$dbc = \core\App::getDb();
	
	//récupération de chaque fichier cron
	require("modules/messagerie/app/cron/delete_message.php");
	require("modules/bataille/app/cron/desactier_vacances.php");
	require("modules/bataille/app/cron/terminer_marche_transport.php");
	require("modules/bataille/app/cron/terminer_recrutement.php");
	require("modules/bataille/app/cron/terminer_recherche.php");
	require("modules/bataille/app/cron/terminer_mission.php");
	require("modules/bataille/app/cron/actualiser_ressources.php");
	require("modules/bataille/app/cron/actualiser_nourriture.php");
	require("modules/bataille/app/cron/terminer_construction.php");
	require("modules/bataille/app/cron/delete_compte.php");