<?php

	//pour le dossier racine du blog
	define("MESSAGERIEWEBROOT", str_replace("$page_root", '', $_SERVER['SCRIPT_NAME'])."modules/messagerie/app/views/");

	//pour le dossier racine du blog -> for include and require
	define('MESSAGERIEROOT', str_replace("$page_root", '', $_SERVER['SCRIPT_FILENAME'])."modules/messagerie/app/views/");