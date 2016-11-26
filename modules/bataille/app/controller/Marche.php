<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;

	class Marche {
		private $id_base_dest;
		private $aller;
		private $date_arrivee;
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
					$this->date_arrivee = $obj->date_arrivee;
					$this->id_marche_transport = $obj->ID_marche_transport;

					$this->getTransportArrive();

					//si c'est sur l'allé
					if ($obj->aller == 1) {
						$marche["aller"][] = [
							"id_marche_transport" => $obj->ID_marche_transport,
							"date_arrivee" => $this->duree_restante_trajet,
							"nom_base_dest" => $obj->nom_base
						];
					}
					else {
						$marche["retour"][] = [
							"id_marche_transport" => $obj->ID_marche_transport,
							"date_arrivee" => $this->duree_restante_trajet,
							"nom_base_dest" => $obj->nom_base
						];
					}
				}

				Bataille::setValues(["marche" => $marche]);
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		private function getTransportArrive() {
			$today = new \DateTime();
			$today = $today->getTimestamp();

			echo "today : ".$today." ++ date arrivee : ";
			echo $this->date_arrivee."<br>";

			//on test si déja arrivé à destination
			if (($this->aller == 1) && (($this->date_arrivee-$today) <= 0)) {echo("dgd");
				$this->setLivrerRessource();

				//on calcul la date d'arrivée du retour
				$date_retour = Bataille::getDureeTrajet($this->id_base_dest)+$this->date_arrivee;

				echo "date retour = ".$date_retour;

				//si le retour du trajet est également arrivé on finit le transport
				if ($date_retour < $today) {
					$this->setTerminerTransport();
				}
				else {

				}


			}
			else if (($this->aller == 0) && (($this->date_arrivee-$today) <= 0)) {

			}
			else {
				$this->duree_restante_trajet = $this->date_arrivee-$today;
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet d'ajouter les ressources à la base destinatrice du transport
		 */
		private function setLivrerRessource() {

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