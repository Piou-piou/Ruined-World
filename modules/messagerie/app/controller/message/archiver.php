<?php
	$message = new \modules\messagerie\app\controller\Messagerie();

	$message->setArchiverMessage($_GET['id_message']);

	header("location:".WEBROOT."messagerie");