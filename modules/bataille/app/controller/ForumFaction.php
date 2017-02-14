<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\ChaineCaractere;
	use core\HTML\flashmessage\FlashMessage;
	
	class ForumFaction extends Faction {
		private $id_forum;
		
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
		
		/**
		 * @param $id_forum
		 * fonction qui va chercher un forum en particulier
		 */
		public function getForum($id_forum) {
			$dbc = App::getDb();
			$this->id_forum = $id_forum;
			
			$query = $dbc->select()->from("_bataille_faction_forum")
				->where("ID_faction", "=", $this->id_faction, "AND")
				->where("ID_faction_forum", "=", $this->id_forum)
				->get();
		
			if (count($query) == 1) {
				foreach ($query as $obj) {
					Bataille::setValues([
						"forum" => [
							"id_forum" => $obj->ID_forum_faction,
							"titre" => $obj->titre,
							"texte" => $obj->texte,
							"date_creation" => $obj->date_creation
						]
					]);
				}
				
				$this->getCommentaireForum();
			}
		}
		
		/**
		 * fonction qui récupère les commentaires d'un forum en particulier
		 */
		private function getCommentaireForum() {
			$dbc = App::getDb();
			
			$query = $dbc->select()
				->from("_bataille_faction_forum_commentaire")
				->from("identite")
				->where("ID_faction_forum", "=", $this->id_forum, "AND")
				->where("_bataille_faction_forum_commentaire.ID_identite", "=", "identite.ID_identite", "", true)
				->get();
			
			if (count($query) > 0) {
				$commentaires = [];
				
				foreach ($query as $obj) {
					$commentaires[] = [
						"id_commentaire" => $obj->ID_faction_forum_commentaire,
						"commentaire" => $obj->commentaire,
						"date_creation" => $obj->date_creation,
						"pseudo" => $obj->pseudo
					];
				}
				
				Bataille::setValues(["forum_commentaires" => $commentaires]);
			}
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
		
		/**
		 * @param $url
		 * fonction qui supprime un forum
		 */
		public function setSupprimerForum($id_forum) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("GERER_POST_FORUM", $permissions_membre)) {
				$dbc->delete()->from("_bataille_faction_forum")->where("ID_faction_forum", "=", $id_forum, "AND")->where("ID_faction", "=", $this->id_faction)->del();
				$dbc->delete()->from("_bataille_faction_forum_commentaire")->where("ID_faction_forum", "=", $id_forum)->del();
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas la permission de supprimer un forum");
			return false;
		}
		
		public function setSupprimerCommentaire($id_commentaire) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("GESTION_FORUM", $permissions_membre)) {
				$dbc->delete()->from("_bataille_faction_forum_commentaire")
					->where("ID_faction_forum_commentaire", "=", $id_commentaire, "AND")
					->where("ID_faction_forum", "=", $this->id_faction)
					->del();
				
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas la permission de supprimer un commentaire");
			return false;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}