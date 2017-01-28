<?php
	namespace modules\messagerie\app\controller;

	use core\App;
	use core\functions\ChaineCaractere;

	class Messagerie {
		public static $url_message;

		private $id_message;
		private $objet;
		private $message;
		private $date_message;
		private $url;

		private $id_expediteur;
		private $pseudo_expediteur;

		private $pseudo_receveur;
		private $id_receveur;

		private $values = [];
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		/**
		 * @param null $type_boite
		 * initialisation de la récupération des messages des différents boites
		 */
		public function __construct($type_boite = null) {
			if ($type_boite !== null) {
				if ($type_boite == "boite réception") {
					$this->getBoiteReception();
				}
				else if ($type_boite == "messages envoyés") {
					$this->getMessagesEnvoyes();
				}
				else if ($type_boite == "messages supprimés") {
					$this->getMessageSupprimes();
				}
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getIdMessage() {
			return $this->id_message;
		}
		public function getObjet() {
			return $this->objet;
		}
		public function getMessage() {
			return $this->message;
		}
		public function getDateMessage(){
		    return $this->date_message;
		}
		public function getUrl(){
		    return $this->url;
		}
		public function getIdExpediteur() {
			return $this->id_expediteur;
		}
		public function getPseudoExpediteur() {
			return $this->pseudo_expediteur;
		}
		public function getIdReceveur() {
			return $this->id_receveur;
		}
		public function getPseudoReceveur() {
			return $this->pseudo_receveur;
		}
		public function getValues(){
		    return ["messagerie" => $this->values];
		}

		/**
		 * fonction qui permet de récupérer tous les messages dans la boite de récéption
		 */
		private function getBoiteReception() {
			$dbc = App::getDb();

			$query = $dbc->select()
				->from("_messagerie_boite_reception")
				->from("_messagerie_message")
				->from("identite")
				->where("_messagerie_boite_reception.ID_identite", "=", 1, "AND")
				->where("_messagerie_boite_reception.supprimer", " IS ", "NULL", "AND", true)
				->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "AND", true)
				->where("_messagerie_message.ID_expediteur", "=", "identite.ID_identite", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$arr = [
						"id_message" => $obj->ID_message,
						"objet" => $obj->objet,
						"date_message" => $obj->date,
						"id_expediteur" => $obj->ID_expediteur,
						"pseudo_expediteur" => $obj->pseudo,
						"url" => $obj->url
					];

					$this->values[] = $arr;
				}
			}
		}

		/**
		 * fonction qui permet de récupérer tous les messages dans la boite des messages envoyes
		 */
		private function getMessagesEnvoyes() {
			$dbc = App::getDb();

			$query = $dbc->select()
				->from("_messagerie_boite_reception")
				->from("_messagerie_message")
				->from("identite")
				->where("_messagerie_message.ID_expediteur", "=", 1, "AND")
				->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "AND", true)
				->where("_messagerie_boite_reception.ID_identite", "=", "identite.ID_identite", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$arr = [
						"id_message" => $obj->ID_message,
						"objet" => $obj->objet,
						"date_message" => $obj->date,
						"id_expediteur" => $obj->ID_expediteur,
						"pseudo_receveur" => $obj->pseudo,
						"url" => $obj->url
					];

					$this->values[] = $arr;
				}
			}
		}

		/**
		 * fonction qui récupère tous les messages supprimés
		 */
		private function getMessageSupprimes() {
			$dbc = App::getDb();

			$query = $dbc->select()
				->from("_messagerie_boite_reception")
				->from("_messagerie_message")
				->from("identite")
				->where("_messagerie_boite_reception.ID_identite", "=", 1, "AND")
				->where("_messagerie_boite_reception.supprimer", "=", 1, "AND")
				->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "AND", true)
				->where("_messagerie_message.ID_expediteur", "=", "identite.ID_identite", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$arr = [
						"id_message" => $obj->ID_message,
						"objet" => $obj->objet,
						"date_message" => $obj->date,
						"id_expediteur" => $obj->ID_expediteur,
						"pseudo_expediteur" => $obj->pseudo,
						"url" => $obj->url
					];

					$this->values[] = $arr;
				}
			}
		}

		/*
		 * fonction qui permetlors de l'envoit d'un message d'être sur que le membre existe
		 */
		private function getIdIdentiteExist($pseudo) {
			$dbc = App::getDb();

			$pseudo = trim($pseudo);

			$query = $dbc->select("ID_identite")->from("identite")->where("pseudo", "=", $pseudo)->get();

			if ((count($query) == 1) && (is_array($query))) {
				foreach ($query as $obj) {
					return $obj->ID_identite;
				}
			}

			return false;
		}

		/**
		 * @param $url_message
		 * fonction qui récupère un message suivant une url
		 */
		public function getUnMessage($url_message) {
			$dbc = App::getDb();

			$query = $dbc->select()
				->from("_messagerie_message")
				->from("_messagerie_boite_reception")
				->from("identite")
				->where("_messagerie_message.url", "=", $url_message, "AND")
				->where("_messagerie_message.ID_expediteur", "=", "identite.ID_identite", "AND", true)
				->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->values = [
						"id_message" => $obj->ID_message,
						"objet" => $obj->objet,
						"message" => $obj->message,
						"date_message" => $obj->date,
						"id_expediteur" => $obj->ID_expediteur,
						"pseudo_expediteur" => $obj->pseudo,
						"url" => $obj->url,
						"supprimer" => $obj->supprimer
					];
				}
			}
			else {
				return false;
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_message
		 * pour passer le message en supprimé, il sera alors consultable dans la table messages supprimés
		 */
		public function setArchiverMessage($id_message) {
			$dbc = App::getDb();

			$dbc->update("supprimer", 1)->from("_messagerie_boite_reception")
				->where("ID_message", "=", $id_message, "AND")
				->where("ID_identite", "=", 1)
				->set();
		}

		/**
		 * @param $id_message
		 */
		public function setSupprimerMessage($id_message) {
			$dbc = App::getDb();

			$dbc->delete()->from("_messagerie_boite_reception")
				->where("ID_message", "=", $id_message, "AND")
				->where("ID_identite", "=", $_SESSION['idlongin'])
				->del();
		}

		/**
		 * @param $objet
		 * @param $destinataire
		 * @param $message
		 * @return bool
		 *
		 * fonction qui sert à envoyer un message à un ou plusieurs destinataires
		 */
		public function setEnvoyerMessage($objet, $destinataire, $message) {
			$dbc = App::getDb();

			//on test si un ou plusieurs destinataires ++ si ils existent
			if (ChaineCaractere::FindInString($destinataire, ",")) {
				$destinataires = explode(",", $destinataire);
				$c = count($destinataires);

				for ($i=0 ; $i<$c ; $i++) {
					if ($this->getIdIdentiteExist($destinataires[$i]) !== false) {
						$destinataires[] = $this->getIdIdentiteExist($destinataires[$i]);
						$expediteur = $_SESSION['idlongin'.CLEF_SITE];
					}
					else {
						return false;
					}
				}
			}
			else {
				if ($this->getIdIdentiteExist($destinataire) !== false) {
					$destinataires[] = $this->getIdIdentiteExist($destinataire);
					$expediteur = $_SESSION['idlongin'.CLEF_SITE];
				}
				else if (is_numeric($destinataire)) {
					$destinataires[] = $destinataire;
					$expediteur = 1;
				}
				else {
					return false;
				}
			}

			//cela veut dire qu'on a au moin 1 membre à qui envoyer le message
			if (count($destinataires) > 0) {
				$dbc->insert("message", $message)
					->insert("objet", $objet)
					->insert("url", ChaineCaractere::setUrl($objet))
					->insert("date", date("Y-m-d H:i:s"))
					->insert("ID_expediteur", $expediteur)
					->into("_messagerie_message")
					->set();

				$id_message = $dbc->lastInsertId();

				foreach ($destinataires as $destinataire) {
					$dbc->insert("ID_identite", $destinataire)
						->insert("ID_message", $id_message)
						->into("_messagerie_boite_reception")
						->set();
				}

				return true;
			}

			return false;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}