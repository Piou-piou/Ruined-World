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

		public function getUnitePossibleRecruter() {

		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}