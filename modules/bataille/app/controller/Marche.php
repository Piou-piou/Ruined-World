<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\HTML\flashmessage\FlashMessage;

	class Marche {
		private $id_base_dest;
		private $id_base;
		private $aller;
		private $ressources;
		private $date_arrivee;
		private $nom_base;
		private $id_marche_transport;
		private $duree_restante_trajet;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();

			//récupération des trajets en cours d'envoi
			$query = $dbc->select()->from("_bataille_marche_transport")
				->from("_bataille_base")
				->where("_bataille_marche_transport.ID_base", "=", Bataille::getIdBase(), "AND")
				->where("_bataille_marche_transport.ID_base_dest", "=", "_bataille_base.ID_base", "", true)
				->orderBy("_bataille_marche_transport.aller", "DESC")
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->id_base_dest = $obj->ID_base_dest;
					$this->aller = $obj->aller;
					$this->ressources = $obj->ressources;
					$this->nom_base = $obj->nom_base;
					$this->date_arrivee = $obj->date_arrivee;
					$this->id_marche_transport = $obj->ID_marche_transport;

					$marche[] = $this->getTransportArrive();
				}

				Bataille::setValues(["marche_envoyer" => $marche]);
			}

			//récupération des trajets que l'on va recevoir
			$query = $dbc->select()->from("_bataille_marche_transport")
				->from("_bataille_base")
				->where("aller", "=", 1, "AND")
				->where("_bataille_marche_transport.ID_base_dest", "=", Bataille::getIdBase(), "AND")
				->where("_bataille_marche_transport.ID_base", "=", "_bataille_base.ID_base", "", true)
				->orderBy("_bataille_marche_transport.aller", "DESC")
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->id_base_dest = $obj->ID_base_dest;
					$this->id_base = $obj->ID_base;
					$this->aller = $obj->aller;
					$this->ressources = $obj->ressources;
					$this->nom_base = $obj->nom_base;
					$this->date_arrivee = $obj->date_arrivee;
					$this->id_marche_transport = $obj->ID_marche_transport;

					$marche[] = $this->getTransportArrive();
				}

				Bataille::setValues(["marche_recevoir" => $marche]);
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet de savoir si un transport est arrivé à la base de destination
		 * +si arrivé appel la fonction pour ajouter les ressources et passe le trajet sur le retour
		 * ou en trajet fini suivant le temps de la date d'aujourd'hui et la date à laquelle le
		 * trajet aurait du revenir
		 */
		private function getTransportArrive() {
			$today = Bataille::getToday();

			//on test si déja arrivé à destination
			if (($this->aller == 1) && (($this->date_arrivee-$today) <= 0)) {
				$this->setLivrerRessource();

				//on calcul la date d'arrivée du retour
				if ($this->id_base_dest == Bataille::getIdBase()) {
					$date_retour = Bataille::getDureeTrajet($this->id_base, Bataille::getParam("vitesse_marchand"))+$this->date_arrivee;
				}
				else {
					$date_retour = Bataille::getDureeTrajet($this->id_base_dest, Bataille::getParam("vitesse_marchand"))+$this->date_arrivee;
				}

				//si le retour du trajet est également arrivé on finit le transport sinon on le place sur le retour
				if ($date_retour < $today) {
					$this->setTerminerTransport();
				}
				else {
					$this->setTrajetRetour($date_retour);
					$this->duree_restante_trajet = $date_retour-$today;
					$set_array = true;
				}
			}
			else if (($this->aller == 0) && (($this->date_arrivee-$today) <= 0)) {
				$this->setTerminerTransport();
			}
			else {
				$this->duree_restante_trajet = $this->date_arrivee-$today;
				$set_array = true;
			}

			if ($set_array === true) {
				if ($this->aller == 1) {
					$marche = [
						"id_marche_transport" => $this->id_marche_transport,
						"date_arrivee" => $this->duree_restante_trajet,
						"nom_base_dest" => $this->nom_base,
						"aller" => $this->aller
					];
				}
				else {
					$marche = [
						"id_marche_transport" => $this->id_marche_transport,
						"date_arrivee" => $this->duree_restante_trajet,
						"nom_base_dest" => $this->nom_base,
						"aller" => $this->aller
					];
				}

				return $marche;
			}
		}

		/**
		 * @param $all_ressource
		 * @return bool
		 * fonction qui renvoi true si on a assez de marchand pour ce trajet dans la base
		 * sinon on renvoi false
		 */
		private function getAssezMarchand($all_ressource) {
			$dbc = App::getDb();

			//récupération du nombre max du marchand dispo dans la base
			$nombre_max_marchand = Bataille::getBatiment()->getNiveauBatiment("marche");

			//on récupère tous les marchands qui sont en transport
			$query = $dbc->select("nb_marchand")->from("_bataille_marche_transport")
				->where("ID_base", "=", Bataille::getIdBase(), "OR")
				->where("ID_base_dest", "=", Bataille::getIdBase())
				->get();

			$marchand_transport = 0;
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$marchand_transport += $obj->nb_marchand;
				}
			}

			//on a le nombre de marchand dispo dans la base
			$nombre_marchand_dispo = $nombre_max_marchand-$marchand_transport;

			//on calcul savoir si on en a assez pour transport toutes les ressoures
			//il faut 1 marchand pour 1000 ressource
			$nombre_marchand_trajet = ceil($all_ressource/1000);

			//si on a assez de marchand on revoi true sinon false
			if ($nombre_marchand_dispo >= $nombre_marchand_trajet) {
				return $nombre_marchand_trajet;
			}

			return false;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet d'ajouter les ressources à la base destinatrice du transport
		 */
		private function setLivrerRessource() {
			$ressource = new Ressource($this->id_base_dest);

			$ressource_transport = unserialize($this->ressources);

			$ressource->setUpdateRessource($ressource_transport['eau'], $ressource_transport['electricite'], $ressource_transport['fer'], $ressource_transport['fuel'], $ressource_transport['nourriture'], "+");
		}
		
		/**
		 * @param $date_retour
		 * fonction qui place le trajet en retour
		 */
		private function setTrajetRetour($date_retour) {
			$dbc = App::getDb();

			$dbc->update("ressources", 0)
				->update("aller", 0)
				->update("date_arrivee", $date_retour)
				->from("_bataille_marche_transport")
				->where("ID_marche_transport", "=", $this->id_marche_transport)
				->set();

			$this->aller = 0;
		}

		/**
		 * permet de terminer totallement un transport
		 */
		private function setTerminerTransport() {
			$dbc = App::getDb();

			$dbc->delete()->from("_bataille_marche_transport")->where("ID_marche_transport", "=", $this->id_marche_transport)->del();
		}

		/**
		 * @param $eau
		 * @param $electricite
		 * @param $fer
		 * @param $fuel
		 * @param $nourriture
		 * @param $posx
		 * @param $posy
		 * @return bool
		 * Fonction qui permet d'initialiser un transport de ressources d'une base à une autre
		 */
		public function setCommencerTransport($eau, $electricite, $fer, $fuel, $nourriture, $posx, $posy) {
			$dbc = App::getDb();
			$id_base_dest = Bataille::getBaseExistPosition($posx, $posy);

			if (($id_base_dest != 0) && ($id_base_dest != Bataille::getIdBase())) {
				$ressource["eau"] = Bataille::getTestAssezRessourceBase("eau", $eau);
				$ressource["electricite"] = Bataille::getTestAssezRessourceBase("electricite", $electricite);
				$ressource["fer"] = Bataille::getTestAssezRessourceBase("fer", $fer);
				$ressource["fuel"] = Bataille::getTestAssezRessourceBase("fuel", $fuel);
				$ressource["nourriture"] = Bataille::getTestAssezRessourceBase("nourriture", $nourriture);

				//si pas assez de ressources dispo dans la base pour l'envoi on renvoi erreur
				foreach ($ressource as $tab) {
					if (in_array("rouge", $tab)) {
						FlashMessage::setFlash("Vous n'avez pas autant de ressources disponibles à l'envoi");
						return false;
					};
				}

				//on check si assez marchand dans la base, si pas assez on return false
				$nb_marchand = $this->getAssezMarchand($eau+$electricite+$fer+$fuel+$nourriture);

				if ($nb_marchand === false) {
					FlashMessage::setFlash("Vous n'avez pas assez de marchans disponibles pour effectuer ce trajet");
					return false;
				}

				//sinon initialise le transport
				//on recup la date d'arrivee dans la base de destintation
				$date_arrivee = Bataille::getDureeTrajet($id_base_dest, Bataille::getParam("vitesse_marchand"))+Bataille::getToday();

				$ressource = [
					"eau" => $eau,
					"electricite" => $electricite,
					"fer" => $fer,
					"fuel" => $fuel,
					"nourriture" => $nourriture,
				];

				//on insert le transport dans la table
				$dbc->insert("date_arrivee", $date_arrivee)
					->insert("ressources", serialize($ressource))
					->insert("aller", 1)
					->insert("nb_marchand", $nb_marchand)
					->insert("ID_base_dest", $id_base_dest)
					->insert("ID_base", Bataille::getIdBase())
					->into("_bataille_marche_transport")
					->set();

				//on retire les ressources de la base
				Bataille::getRessource()->setUpdateRessource($eau, $electricite, $fer, $fuel, $nourriture, "-");

				FlashMessage::setFlash("Votre transport vient de partir !", "info");
				return true;
			}

			FlashMessage::setFlash("Aucune base présente aux coordonnées données");
			return false;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}