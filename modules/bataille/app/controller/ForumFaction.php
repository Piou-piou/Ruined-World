<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\ChaineCaractere;
	use core\HTML\flashmessage\FlashMessage;
	
	class ForumFaction extends Faction {
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui récupère les forums de la faction
		 */
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
		
		/**
		 * @param $titre
		 * @return bool
		 * fonction qui test si un forum aec ce titre existe déjà
		 */
		private function getForumExist($titre) {
			$dbc = App::getDb();
			
			$query = $dbc->select("titre")->from("_bataille_faction_forum")
				->where("titre", "=", $titre, "AND")
				->where("ID_faction", "=", $this->id_faction)
				->get();
			
			if (count($query) > 0) {
				return true;
			}
			
			return false;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $titre
		 * @param $texte
		 * @return bool
		 */
		public function setCreerForum($titre, $texte) {
			$dbc = App::getDb();
			
			if ((strlen($titre) < 3) || (strlen($texte) < 3)) {
				FlashMessage::setFlash("Le tittre et le texte de votre forum doivent faire plus de 2 caractères");
				return false;
			}
			
			if ($this->getForumExist($titre) === true) {
				FlashMessage::setFlash("Un forum portant ce nom existe déjà, merci d'en choisir un autre");
				return false;
			}
			
			$dbc->insert("titre", $titre)
				->insert("url", ChaineCaractere::setUrl($titre))
				->insert("texte", $texte)
				->insert("date_creation", date("Y-m-d H:i:s"))
				->insert("ID_faction", $this->id_faction)
				->into("_bataille_faction_forum")
				->set();
			
			return true;
		}
		
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}