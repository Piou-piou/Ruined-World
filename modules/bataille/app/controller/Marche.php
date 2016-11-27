<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;

	class Marche {
		private $id_base_dest;
		private $aller;
		private $ressources;
		private $date_arrivee;
		private $nom_base;
		private $id_marche_transport;
		private $duree_restante_trajet;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();

			//récupération des trajets en cours
			$query = $dbc->select()->from("_bataille_marche_transport")
				->from("_bataille_base")
				->where("_bataille_marche_transport.ID_base", "=", Bataille::getIdBase(), "AND")
				->where("_bataille_marche_transport.ID_base_dest", "=", "_bataille_base.ID_base", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->id_base_dest = $obj->ID_base_dest;
					$this->aller = $obj->aller;
					$this->ressources = $obj->ressources;
					$this->nom_base = $obj->nom_base;
					$this->date_arrivee = $obj->date_arrivee;
					$this->id_marche_transport = $obj->ID_marche_transport;

					$marche = $this->getTransportArrive();
				}

				Bataille::setValues(["marche" => $marche]);
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
			$today = new \DateTime();
			$today = $today->getTimestamp();

			echo "today : ".$today." ++ date arrivee : ";
			echo $this->date_arrivee."<br>";

			//on test si déja arrivé à destination
			if (($this->aller == 1) && (($this->date_arrivee-$today) <= 0)) {
				$this->setLivrerRessource();

				//on calcul la date d'arrivée du retour
				$date_retour = Bataille::getDureeTrajet($this->id_base_dest)+$this->date_arrivee;

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
					$marche["aller"][] = [
						"id_marche_transport" => $this->id_marche_transport,
						"date_arrivee" => $this->duree_restante_trajet,
						"nom_base_dest" => $this->nom_base
					];
				}
				else {
					$marche["retour"][] = [
						"id_marche_transport" => $this->id_marche_transport,
						"date_arrivee" => $this->duree_restante_trajet,
						"nom_base_dest" => $this->nom_base
					];
				}

				return $marche;
			}
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
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}