<?php
	namespace modules\bataille\app\controller;
	
	
	use modules\messagerie\app\controller\Messagerie;
	
	class GenerationRapport {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		public static function setGenererRapport($titre, $infos, $type) {
			$chemin = MODULEROOT."bataille/app/controller/rapports/";
			
			if ($type == "mission") {
				require_once($chemin."mission.php");
			}
			
			$messagerie = new Messagerie();
			$messagerie->setEnvoyerMessage($titre, Bataille::getIdIdentite(), $message);
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}