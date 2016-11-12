<?php
	use \modules\bataille\app\controller\Bataille;
	Bataille::getRessource()->setActualiserRessource();

	echo(json_encode(Bataille::getValues()));