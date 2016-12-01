<?php
	//file sstem to add new building in core
	/*$tab = [
		[
			"centre de commandement",
			"centre_commandement",
			5
		],
		[
			"caserne",
			"caserne",
			5
		]
	];

	echo(serialize($tab));*/

	/*$tab_ressource = [
		"eau" => 0,
		"electricite" => 1000,
		"fer" => 0,
		"fuel" => 0,
		"nourriture" => 0,
	];

	echo(serialize($tab_ressource));*/

	$lvl_max = 30;

	//foinction qui va générer le tableau pour les 30 lvl

	for ($i=1 ; $i<=$lvl_max ; $i++) {
		//pour les ressources
		if ($i == 1) {
			$eau = 760;
			$electricite = 820;
			$fer = 780;
			$fuel = 800;
		}
		else if (($i > 1) && ($i < 7)) {
			$eau = floor($eau*1.5);
			$electricite = floor($electricite*1.5);
			$fer = floor($fer*1.5);
			$fuel = floor($fuel*1.5);
		}
		else if (($i > 6) && ($i < 26)) {
			$eau = floor($eau*1.2);
			$electricite = floor($electricite*1.2);
			$fer = floor($fer*1.2);
			$fuel = floor($fuel*1.2);
		}
		else {
			$eau = floor($eau*1.1);
			$electricite = floor($electricite*1.1);
			$fer = floor($fer*1.1);
			$fuel = floor($fuel*1.1);
		}

		//pour le temps de construction
		if ($i == 1) {
			$temps = 1450;
		}
		else if (($i > 1) && ($i < 16))  {
			$temps = floor($temps*1.28);
		}
		else {
			$temps = $temps+3400;

		}

		$pour_construire = "$eau, $electricite, $fer, $fuel";

		/*echo("$temps +++++ $pour_construire<br>");

		\modules\bataille\app\controller\Bataille::getDb()->insert("ressource_construire", $pour_construire)
			->insert("temps_construction", $temps)
			->into("ambassade")
			->set();*/
	}