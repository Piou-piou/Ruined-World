<?php
	$pages_bataille = [
		"index",
		"popup/unbatiment",
		"popup/listebatiments",
		"aide",
		"aide-detail",
		"map",
		"popup/map",
		"popup/marche/marche",
		"popup/marche/offre-et-demande"
	];
	
	if (\core\modules\GestionModule::getModuleActiver("bataille")) {
		if (!in_array($this->page, $pages_bataille)) {
			\core\HTML\flashmessage\FlashMessage::setFlash("Cette page n'existe pas ou plus");
			header("location:".WEBROOT);
		}

		//pour l'index -> on récupere les derniers articles
		if ($this->page == "index") {
			$this->controller = "bataille/app/controller/initialise/index.php";
		}

		if ($this->page == "aide-detail") {
			\modules\bataille\app\controller\Aide::$parametre_router = $this->parametre;
			$this->controller = "bataille/app/controller/initialise/aide.php";
		}

		//------------------------------- POUR LA MAP ----------------------------------//
		if ($this->page == "map") {
			$this->controller = "bataille/app/controller/initialise/map.php";
		}
		if ($this->page == "popup/map") {
			$this->controller = "bataille/app/controller/initialise/popup/map.php";
		}

		//------------------------------- POUR LA POPUP POUR CONSTRUIRE BATIMENT ----------------------------------//
		if ($this->page == "popup/unbatiment") {
			$this->controller = "bataille/app/controller/initialise/popup/unbatiment.php";
		}
		if ($this->page == "popup/listebatiments") {
			$this->controller = "bataille/app/controller/initialise/popup/batiments_construire.php";
		}

		//------------------------------- POUR LA POPUP POUR LE MARCHE ----------------------------------//
		if ($this->page == "popup/marche/marche") {
			$this->controller = "bataille/app/controller/initialise/popup/unbatiment.php";
		}
		/*if ($this->page == "popup/marche/offre-et-demande") {
			$this->controller = "bataille/app/controller/initialise/popup_marche.php";
		}*/
	}
	else {
		\core\HTML\flashmessage\FlashMessage::setFlash("L'accès à ce module n'est pas configurer ou ne fais pas partie de votre offre, contactez votre administrateur pour résoudre ce problème", "info");
		header("location:".WEBROOT);
	}