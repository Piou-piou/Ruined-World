<?php
	namespace modules\messagerie\app\controller;

	use core\App;

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
			$dbc = App::getDb();

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

				//on check les messages supprimes d'il y a plus de 15 jours et on les delete de la bdd
				//a changer fonction qui se lancera une fois par jour et qui degagera tous les messages
				//supprimes de plus vieux que 15 jours
				/*$today = new \DateTime();
				$date_del =  $today->sub(new \DateInterval("P15D"))->format("Y-m-d H:i:s");

				$query = $dbc->select("_messagerie_message.ID_message")
					->from("_messagerie_boite_reception")
					->from("_messagerie_message")
					->where("_messagerie_message.date", "<", $date_del, "AND")
					->where("(_messagerie_boite_reception.ID_identite = 1 OR _messagerie_message.ID_expediteur = 1)", "", "", "AND", true)
					->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "", true)
					->get();

				if ((is_array($query)) && (count($query) > 0)) {
					foreach ($query as $obj) {
						$this->setDeleteMessageBySystem($obj->ID_message);
					}
				}*/
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
					$id_message[] = $obj->ID_message;
					$objet[] = $obj->objet;
					$date_message[] = $obj->date;
					$id_expediteur[] = $obj->ID_expediteur;
					$pseudo_expediteur[] = $obj->pseudo;
					$url[] = $obj->url;
				}

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
						"date_message" => $obj->date,
						"id_expediteur" => $obj->ID_expediteur,
						"pseudo_expediteur" => $obj->pseudo,
						"url" => $obj->url
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

		public function setEnvoyerMessage($objet, $destinataires, $message) {

		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}