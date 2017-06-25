<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\HTML\flashmessage\FlashMessage;
	use modules\messagerie\app\controller\Messagerie;
	
	class Faction extends PermissionsFaction {
		protected $id_faction;
		protected $id_autre_faction;
		protected $nom_faction;
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getIdFaction(){
		    return $this->id_faction;
		}
		
		/**
		 * @param $id_faction
		 * @return bool
		 * permet de tester si le joueur est dans la faction affichée
		 */
		private function getTestFactionPlayer($id_faction) {
			$dbc = App::getDb();
			$id_ma_faction = 0;
			
			$query = $dbc->select("ID_faction")->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
			foreach ($query as $obj) {
				$id_ma_faction = $obj->ID_faction;
			}
			
			if ($id_ma_faction == $id_faction) {
				Bataille::setValues([
					"ma_faction" => true,
					"id_identite_player" => Bataille::getIdIdentite()
				]);
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return mixed
		 * fonction qui renvoi l'ID de la faction du joueur
		 */
		public function getFactionPlayer($id_identite = null) {
			$dbc = App::getDb();
			
			if ($id_identite === null) {
				$id_identite = Bataille::getIdIdentite();
			}
			
			$query = $dbc->select("ID_faction")->from("_bataille_infos_player")
				->where("ID_identite", "=", $id_identite, "AND")
				->where("ID_faction", ">", 0)
				->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$this->id_faction = $obj->ID_faction;
					$this->getInfosFaction();
				}
				
				return true;
			}
			
			$this->id_faction = "";
			return false;
		}
		
		/**
		 * @param null $id_faction
		 * fonction qui récupère les infos de la faction
		 */
		public function getInfosFaction($id_faction = null) {
			$dbc = App::getDb();
			
			if ($id_faction === null) {
				$id_faction = $this->id_faction;
			}
			
			$this->getTestFactionPlayer($id_faction);
			
			$query = $dbc->select("identite.pseudo")
				->select("_bataille_faction.ID_faction")
				->select("_bataille_faction.nom_faction")
				->select("_bataille_faction.points_faction")
				->select("_bataille_faction.img_profil")
				->select("_bataille_faction.description")
				->from("_bataille_faction")
				->from("identite")
				->where("_bataille_faction.ID_faction", "=", $id_faction, "AND")
				->where("_bataille_faction.ID_identite", "=", "identite.ID_identite", "", true)
				->get();
			
			if ((count($query) == 1)) {
				foreach ($query as $obj) {
					Bataille::setValues(["faction" => [
						"id_faction" => $obj->ID_faction,
						"nom" => $obj->nom_faction,
						"points_faction" => $obj->points_faction,
						"description" => $obj->description,
						"url_img" => $obj->img_profil,
						"pseudo_chef" => $obj->pseudo
					]]);
					
					$this->nom_faction = $obj->nom_faction;
				}
			}
		}
		
		/**
		 * @return array
		 * fonction qui récupère les membres d'un faction
		 */
		public function getMembreFaction() {
			$dbc = App::getDb();
			
			$query = $dbc->select()
				->from("_bataille_infos_player")
				->from("identite")
				->where("_bataille_infos_player.ID_faction", "=", $this->id_faction, "AND")
				->where("_bataille_infos_player.ID_identite", "=", "identite.ID_identite", "", true)
				->orderBy("_bataille_infos_player.points", "DESC")
				->get();
			
			$membre = [];
			$liste_membre = [];
			foreach ($query as $obj) {
				$membre[] = [
					"id_identite" => $obj->ID_identite,
					"pseudo" => $obj->pseudo,
					"points" => $obj->points,
					"rang_faction" => $obj->rang_faction,
					"chef" => $this->getTestChefFaction($obj->ID_identite, $this->id_faction),
					"permissions" => $this->getMembrePermissions($obj->ID_identite, $this->id_faction)
				];
				
				$liste_membre[] = $obj->pseudo;
			}
			
			Bataille::setValues(["membres_faction" => $membre]);
			
			return $liste_membre;
		}
		
		/**
		 * @param $nom_faction
		 * @return bool
		 * ajout d'une fonction pour tester si une faction existe ou non renvoi true si elle existe
		 */
		protected function getFactionExist($nom_faction) {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_faction")->from("_bataille_faction")->where("nom_faction", "=", $nom_faction)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$this->id_autre_faction = $obj->ID_faction;
				}
				
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return int
		 * fonciton qui renvoi le nombre d'invitations possible
		 * sachant que dès le niveau 3 on peut en envoyer 5 et ensuite une seule par niveau jusqu'au niveau 30
		 */
		private function getNbInvitationPossible() {
			$dbc = App::getDb();
			$nb_inv = 5;
			
			$nb_inv = (Bataille::getBatiment()->getNiveauBatiment("ambassade")+$nb_inv)-3;
			
			$query = $dbc->select("ID_identite")->from("_bataille_faction_invitation")->where("ID_faction", "=", $this->id_faction)->get();
			$nb_invitation_envoyees = count($query);
			
			$nb_inv = $nb_inv-$nb_invitation_envoyees-count($this->getMembreFaction());
			
			if ($nb_inv < 0) {
				return 0;
			}
			
			return $nb_inv;
		}
		
		/**
		 * @return array
		 * foncitons qui renvoit les informations sur les joueurs invités à rejoindre la faction
		 */
		public function getInvitationsEnvoyees() {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			$invitations = [];
			$pseudos = [];
			
			if ($permissions_membre == "chef" || in_array("INVITER_MEMBRE", $permissions_membre)) {
				$query = $dbc->select()->from("_bataille_faction_invitation, identite, _bataille_infos_player")
					->where("_bataille_faction_invitation.ID_faction", "=", $this->id_faction, "AND")
					->where("_bataille_faction_invitation.ID_identite", "=", "identite.ID_identite", "AND", true)
					->where("_bataille_faction_invitation.ID_identite", "=", "_bataille_infos_player.ID_identite", "", true)->get();
			}
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$invitations[] = [
						"id_identite" => $obj->ID_identite,
						"points" => $obj->points,
						"pseudo" => $obj->pseudo,
						"vacances" => $obj->mode_vacances,
					];
					
					$pseudos[] = $obj->pseudo;
				}
			}
			$invitations["nb_invitation_possible"] = $this->getNbInvitationPossible();
			
			Bataille::setValues(["invitations" => $invitations]);
			
			return $pseudos;
		}
		
		/**
		 * fonction qui renvoi les invitations recues par un joueur
		 */
		public function getInvitationsMembre() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_faction_invitation, _bataille_faction")
				->where("_bataille_faction_invitation.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_faction_invitation.ID_faction", "=", "_bataille_faction.ID_faction", "", true)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$invitations[] = [
						"id_faction" => $obj->ID_faction,
						"nom_faction" => $obj->nom_faction,
						"points_faction" => $obj->points_faction,
					];
					
					Bataille::setValues(["invitations" => $invitations]);
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_identite
		 * @return bool
		 * fonction qui permet de renvoyer un membre d'un faction
		 */
		public function setRenvoyerMembre($id_identite) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("RENVOYER_MEMBRE", $permissions_membre)) {
				$dbc->update("ID_faction", 0)
					->update("rang_faction", "")
					->from("_bataille_infos_player")
					->where("ID_identite", "=", $id_identite, "AND")
					->where("ID_faction", "=", $this->id_faction, "", true)
					->set();
				
				$this->setSupprilerAllPermissions($id_identite);
				
				FlashMessage::setFlash("Le membre a bien été renvoyé de la faction", "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de renvoyer un membre");
			return false;
		}
		
		/**
		 * @param $pseudo
		 * @return bool
		 * fonction qui permet d'inviter un membre à rejoindre la faction
		 */
		public function setInviterMembre($pseudo) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("INVITER_MEMBRE", $permissions_membre)) {
				$id_identite = Bataille::getPlayerExist($pseudo);
				if ($id_identite== false) {
					FlashMessage::setFlash("Ce joueur n'existe pas");
					return false;
				}
				if (in_array($pseudo, $this->getMembreFaction())) {
					FlashMessage::setFlash("Ce joueur est déjà dans votre faction ou est en attente d'invitation, vous ne pouvez pas l'inviter à nouveau");
					return false;
				}
				if (in_array($pseudo, $this->getInvitationsEnvoyees())) {
					FlashMessage::setFlash("Ce joueur est déjà dans votre faction ou est en attente d'invitation, vous ne pouvez pas l'inviter à nouveau");
					return false;
				}
				if ($this->getNbInvitationPossible() < 1) {
					FlashMessage::setFlash("Plus d'invitations possible, votre leader doit augmenter son ambassade");
					return false;
				}
				
				$infos = [
					"nom_faction" => $this->nom_faction,
					"id_faction" => $this->id_faction
				];
				
				require(MODULEROOT."bataille/app/controller/rapports/invitation-faction.php");
				
				$messagerie = new Messagerie();
				$messagerie->setEnvoyerMessage("Invitation rejoindre faction", $id_identite, $message);
				
				$dbc->insert("ID_faction", $this->id_faction)->insert("ID_identite", $id_identite)
					->into("_bataille_faction_invitation")->set();
				FlashMessage::setFlash("L'invitation a bien été envoyée", "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation d'inviter un membre");
			return false;
		}
		
		/**
		 * @param $id_identite
		 */
		public function setChangerChef($id_identite) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef") {
				$dbc->update("ID_identite", $id_identite)->from("_bataille_faction")
					->where("ID_faction", "=", $this->id_faction)->set();
				
				FlashMessage::setFlash("Le chef de la faction a bien été changé", "success");
				
				return true;
			}
			
			FlashMessage::setFlash("Vous n'êtes pas le chef de la faction vous ne pouvez donc pas le changer");
			return false;
		}
		
		/**
		 * @param $id_identite
		 * @return bool
		 * fonction qui permet de supprimer une invitation a rejoindre la faction
		 */
		public function setSupprimerInvitation($id_identite) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("INVITER_MEMBRE", $permissions_membre)) {
				$dbc->delete()->from("_bataille_faction_invitation")->where("ID_faction", "=", $this->id_faction, "AND")
					->where("ID_identite", "=", $id_identite)->del();
				
				FlashMessage::setFlash("L'invitation a bien été supprimée", "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de supprimer une invitation");
			return false;
		}
		
		/**
		 * @return bool
		 * fonction qui permet à un joueur de quitter sa faction
		 */
		public function setQuitterFaction() {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef") {
				FlashMessage::setFlash("Merci de définir un nouveau chef avant de quitter votre faction");
				return false;
			}
			else {
				$dbc->update("ID_faction", 0)
					->update("rang_faction", "")->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
				
				FlashMessage::setFlash("Vous avez quitter votre faction", "success");
				return true;
			}
		}
		
		/**
		 * @param $id_faction
		 * @return bool
		 * fonction qui permet à un joueur de rejoindre une faction
		 */
		public function setAccepterInvitationPlayer($id_faction) {
			$dbc = App::getDb();
			
			$dbc->update("ID_faction", $id_faction)
				->update("rang_faction", "")->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
			
			$this->setSupprimerInvitationPlayer($id_faction);
			
			FlashMessage::setFlash("Vous avez rejoint une faction", "success");
			return true;
		}
		
		/**
		 * @param $id_faction
		 * @return bool
		 * permet à un joueur de supprimer une invitation qu'il a reçu
		 */
		public function setSupprimerInvitationPlayer($id_faction) {
			$dbc = App::getDb();
			
			$dbc->delete()->from("_bataille_faction_invitation")->where("ID_faction", "=", $id_faction, "AND")
				->where("ID_identite", "=", Bataille::getIdIdentite())->del();
			
			FlashMessage::setFlash("L'invitation a bien été supprimée", "success");
			return true;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}