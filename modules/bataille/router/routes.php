<?php
	$pages_bataille = [
		"index",
		"popup_unbatiment",
		"popup_listebatiments",
		"aide",
		"aide-detail",
		"map",
		"popup_map",
		"popup_marche"
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

		if ($this->page == "popup_unbatiment") {
			$this->controller = "bataille/app/controller/initialise/popup_unbatiment.php";
		}
		if ($this->page == "popup_listebatiments") {
			$this->controller = "bataille/app/controller/initialise/popup.php";
		}

		if ($this->page == "aide-detail") {
			\modules\bataille\app\controller\Aide::$parametre_router = $this->parametre;
			$this->controller = "bataille/app/controller/initialise/aide.php";
		}

		if ($this->page == "map") {
			$this->controller = "bataille/app/controller/initialise/map.php";
		}
		if ($this->page == "popup_map") {
			$this->controller = "bataille/app/controller/initialise/popup_map.php";
		}
	}
	else {
		\core\HTML\flashmessage\FlashMessage::setFlash("L'accès à ce module n'est pas configurer ou ne fais pas partie de votre offre, contactez votre administrateur pour résoudre ce problème", "info");
		header("location:".WEBROOT);
	}