<?php
	$marche =  new \modules\bataille\app\controller\Marche();

	$marche->setCommencerTransport($_POST['eau'], $_POST['electricite'], $_POST['fer'], $_POST['fuel'], $_POST['nourriture'], $_POST['posx'], $_POST['posy']);