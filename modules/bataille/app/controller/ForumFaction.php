<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class ForumFaction extends Faction {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getListeForum() {
			$dbc = App::getDb();
			echo $this->id_faction."dg";
			
			$query = $dbc->select()->from("_bataille_faction_forum")->where("ID_faction", "=", $this->id_faction)->get();
			
			$forums = [];
			if ((count($query) > 0)) {
				foreach ($query as $obj) {
					$forums[] = [
						"id_forum" => $obj->ID_faction_forum,
						"titre" => $obj->titre,
						"url" => $obj->url,
						"texte" => $obj->texte,
						"date_creation" => $obj->date_creation
					];
				}
			}
			
			Bataille::setValues(["forums" => $forums]);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}