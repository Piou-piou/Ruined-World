<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class GroupeUnite extends Unite {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui récupère tous les groupes de la bases
		 */
		public function getAllGroupeUnite() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_groupe_unite")->get();
			
			if ((is_array($query)) && (count($query))) {
				$groupe = [];
				
				foreach ($query as $obj) {
					$groupe[] = [
						"id_groupe" => $obj->ID_groupe_unite,
						"nom_groue" => $obj->nom_groupe,
						"unites" => $this->getAllUnites(Bataille::getIdBase(), $obj->ID_groupe_unite)
					];
				}
				
				Bataille::setValues(["groupe_unites" => $groupe]);
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}