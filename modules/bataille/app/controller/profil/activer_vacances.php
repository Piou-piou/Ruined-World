<?php
	\modules\bataille\app\controller\Profil::setActiverModeVacances();
	
	\core\HTML\flashmessage\FlashMessage::setFlash("Le mode vacances a bien été activé");
	\core\auth\Connexion::setDeconnexion(WEBROOT."bataille/login");