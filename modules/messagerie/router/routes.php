<?php

	$pages_messagerie = array("index", "messages-envoyes", "messages-supprimes", "message", "ecrire-message");

	if (\core\modules\GestionModule::getModuleActiver("messagerie")) {


		if (!in_array($this->page, $pages_messagerie)) {
			\core\HTML\flashmessage\FlashMessage::setFlash("Cette page n'existe pas ou plus");
			header("location:".WEBROOT);
		}


		if ($this->page == "index") {
			$this->controller = "messagerie/app/controller/initialise/index.php";
		}

		if ($this->page == "messages-envoyes") {
			$this->controller = "messagerie/app/controller/initialise/messages_envoyes.php";
		}

		if ($this->page == "messages-supprimes") {
			$this->controller = "messagerie/app/controller/initialise/messages_supprimes.php";
		}

		if ($this->page == "message") {
			$this->controller = "messagerie/app/controller/initialise/message.php";
			\modules\messagerie\app\controller\Messagerie::$url_message = $this->parametre;
		}
	}
	else {
		\core\HTML\flashmessage\FlashMessage::setFlash("L'accès à ce module n'est pas configurer ou ne fais pas partie de votre offre, contactez votre administrateur pour résoudre ce problème", "info");
		header("location:".WEBROOT);
	}