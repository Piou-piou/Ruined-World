<?php
	namespace modules\bataille\app\controller;
	use core\App;

	class Ressource {
		private $eau;
		private $electricite;
		private $fuel;
		private $fer;
		private $nourriture;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_base")->where("ID_base", "=", Bataille::getIdBase())->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->eau = $obj->eau;
					$this->electricite = $obj->electricite;
					$this->fuel = $obj->fuel;
					$this->fer = $obj->fer;
					$this->nourriture = $obj->nourriture;

					Bataille::$values = array_merge(Bataille::$values, ["eau" => $obj->eau]);
					Bataille::$values = array_merge(Bataille::$values, ["electricite" => $obj->electricite]);
					Bataille::$values = array_merge(Bataille::$values, ["fuel" => $obj->fuel]);
					Bataille::$values = array_merge(Bataille::$values, ["fer" => $obj->fer]);
					Bataille::$values = array_merge(Bataille::$values, ["nourriture" => $obj->nourriture]);
				}

				$this->setActualiserRessource();

				Bataille::$values = array_merge(Bataille::$values, ["max_eau" => $this->getStockageMax("eau")]);
				Bataille::$values = array_merge(Bataille::$values, ["max_electricite" => $this->getStockageMax("electricite")]);
				Bataille::$values = array_merge(Bataille::$values, ["max_fuel" => $this->getStockageMax("fuel")]);
				Bataille::$values = array_merge(Bataille::$values, ["max_fer" => $this->getStockageMax("fer")]);
				Bataille::$values = array_merge(Bataille::$values, ["max_nourriture" => $this->getStockageMax("nourriture")]);
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getEau() {
			return $this->eau;
		}
		public function getElectricite() {
			return $this->electricite;
		}
		public function getFuel() {
			return $this->fuel;
		}
		public function getFer() {
			return $this->fer;
		}
		public function getNourriture() {
			return $this->nourriture;
		}

		private function getStockageMax($ressource) {
			$stockage_max = Bataille::getBatiment()->getStockageEntrepot();

			if ($this->$ressource == $stockage_max) {
				return "rouge";
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui au chargement de la base regardera la derniere co du joueur
		 * si elle est supérieur à 30sec on recalculera les ressources des bases du joueur
		 */
		public function setActualiserRessource() {
			$last_co = Bataille::getLastConnexion();

			$today = new \DateTime();
			$today = $today->getTimestamp();

			$last_co = new \DateTime($last_co);
			$last_co = $last_co->getTimestamp();

			$diff_temps = $today-$last_co;

			//si la derniere actualisation ou connexion est supérieur à 30 sec
			if ($diff_temps > 180) {
				$this->setAddRessource("eau", $this->eau, $diff_temps);
				$this->setAddRessource("electricite", $this->electricite, $diff_temps);
				$this->setAddRessource("fuel", $this->fuel, $diff_temps);
				$this->setAddRessource("fer", $this->fer, $diff_temps);
			}
		}

		/**
		 * @param $nom_ressource
		 * @param $ressrouce
		 * @param $diff_temps
		 * fonction qui ajoute les ressources qu'on a eu dans la base et qui reinitialise la last co a now
		 */
		private function setAddRessource($nom_ressource, $ressrouce, $diff_temps) {
			$dbc = App::getDb();

			$ressource = $ressrouce+(round((Bataille::getBatiment()->getProduction($nom_ressource)/3600)*$diff_temps));
			$stockage_max = Bataille::getBatiment()->getStockageEntrepot();

			if ($ressource > $stockage_max) {
				$ressource = $stockage_max;
			}

			$dbc->update($nom_ressource, $ressource)
				->from("_bataille_base")
				->where("ID_base", "=", Bataille::getIdBase())
				->set();

			$this->$nom_ressource = $ressource;

			Bataille::setLastConnexion();
		}

		/**
		 * @param $eau
		 * @param $electricite
		 * @param $fer
		 * @param $fuel
		 * @param $nourriture
		 * fonction qui permet de retirer des ressources pour construire des batiment ou creer unités
		 */
		public function setRetirerRessource($eau, $electricite, $fer, $fuel, $nourriture) {
			$dbc = App::getDb();

			$eau = $this->getEau()-$eau;
			$electricite = $this->getElectricite()-$electricite;
			$fer = $this->getFer()-$fer;
			$fuel = $this->getFuel()-$fuel;
			$nourriture = $this->getNourriture()-$nourriture;


			$dbc->update("eau", $eau)
				->update("electricite", $electricite)
				->update("fer", $fer)
				->update("fuel", $fuel)
				->update("nourriture", $nourriture)
				->from("_bataille_base")
				->where("ID_base", "=", Bataille::getIdBase())
				->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}