<?php
	
	namespace modules\bataille\app\controller;
	
	
	class Unite {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getInfosUnite($unite, $niveau) {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select()->from("unites")->where("nom", "=", $unite)->get();

			if ((is_array($query)) && (count($query) == 1)) {

			}
			else {
				return false;
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}