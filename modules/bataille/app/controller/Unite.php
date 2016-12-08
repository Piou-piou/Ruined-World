<?php
	
	namespace modules\bataille\app\controller;
	
	
	class Unite {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getInfosBaseUnite($unite) {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select()->from("unites")->where("nom", "=", $unite)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$caracteristique = unserialize($obj->caracteristique);
				}
			}
			else {
				return false;
			}
		}

		public function getUnitePossibleRecruter($type) {
			//on recup toutes les unites deja recherchée donc que l'on peut faire
			$unites = Bataille::getCentreRecherche()->getAllRechercheType($type);

			//si pas d'unites encore recherchees on renvoit un array juste avec 0 dedans
			Bataille::setValues(["unites" => $unites]);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}