<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class Profil {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet d'activer le mode vacances
		 */
		public static function setActiverModeVacances() {
			$dbc = App::getDb();
			
			$dbc->update("mode_vacances", 1)->update("last_connexion", date("Y-m-d H:i:s"))->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}