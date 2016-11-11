<?php
	$messagerie = new \modules\messagerie\app\controller\Messagerie();

	if ($messagerie->setEnvoyerMessage($_POST['objet'], $_POST['destinataire'], $_POST['message']) === true) {
		\core\HTML\flashmessage\FlashMessage::setFlash("Votre message à bien été envoyé", "success");
		header("location:".WEBROOT."messagerie");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::setFlash("Votre ou vos destinataire(s) n'existe pas");

		$_SESSION['objet'] = $_POST['objet'];
		$_SESSION['destinataire'] = $_POST['destinataire'];
		$_SESSION['message'] = $_POST['message'];

		header("location:".WEBROOT."messagerie/ecrire-message");
	}