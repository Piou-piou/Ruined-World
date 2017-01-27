<?php
	namespace modules\bataille\app\controller;
	use core\App;

	class Ressource {
		private $eau;
		private $electricite;
		private $fuel;
		private $fer;
		private $nourriture;

		private $id_base;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct($id_base = null) {
			$dbc = App::getDb();

			if ($id_base === null) {
				$this->id_base = Bataille::getIdBase();
			}
			else {
				$this->id_base = $id_base;
			}

			$query = $dbc->select()->from("_bataille_base")->where("ID_base", "=", $this->id_base)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->eau = $obj->eau;
					$this->electricite = $obj->electricite;
					$this->fuel = $obj->fuel;
					$this->fer = $obj->fer;
					$this->nourriture = $obj->nourriture;
				}

				$this->setActualiserRessource();

				Bataille::setValues([
					"max_eau" => $this->getStockageMax("eau"),
					"max_electricite" => $this->getStockageMax("electricite"),
					"max_fer" => $this->getStockageMax("fer"),
					"max_fuel" => $this->getStockageMax("fuel"),
					"max_nourriture" => $this->getStockageMax("nourriture")
				]);

				Bataille::setValues([
					"eau" => $this->eau,
					"electricite" => $this->electricite,
					"fer" => $this->fer,
					"fuel" => $this->fuel,
					"nourriture" => $this->nourriture
				]);
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
		
		/**
		 * @param $ressource
		 * @return string
		 * fonction qui sert à tester si on a atteint le stockage maximum pour une ressource en particulier
		 */
		private function getStockageMax($ressource) {
			if ($ressource == "nourriture") {
				$stockage_max = Bataille::getBatiment()->getStockage("grenier");
			}
			else {
				$stockage_max = Bataille::getBatiment()->getStockage();
			}

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
			$last_co = Bataille::getLastConnexion($this->id_base);

			$today = Bataille::getToday();

			$last_co = new \DateTime($last_co);
			$last_co = $last_co->getTimestamp();

			$diff_temps = $today-$last_co;

			//si la derniere actualisation ou connexion est supérieur à 30 sec
			$this->setAddRessource("eau", $this->eau, $diff_temps);
			$this->setAddRessource("electricite", $this->electricite, $diff_temps);
			$this->setAddRessource("fuel", $this->fuel, $diff_temps);
			$this->setAddRessource("fer", $this->fer, $diff_temps);
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
			
			$stockage_max = Bataille::getBatiment()->getStockage();
			if ($nom_ressource == "nourriture") {
				$stockage_max = Bataille::getBatiment()->getStockage("grenier");
			}

			if ($ressource > $stockage_max) {
				$ressource = $stockage_max;
			}

			$dbc->update($nom_ressource, $ressource)
				->from("_bataille_base")
				->where("ID_base", "=", $this->id_base)
				->set();

			$this->$nom_ressource = $ressource;

			Bataille::setLastConnexion($this->id_base);
		}

		/**
		 * @param $eau
		 * @param $electricite
		 * @param $fer
		 * @param $fuel
		 * @param $nourriture
		 * @param $signe -> contient + ou -
		 * fonction qui permet de retirer des ressources pour construire des batiment ou creer unités
		 */
		public function setUpdateRessource($eau, $electricite, $fer, $fuel, $nourriture, $signe) {
			$dbc = App::getDb();

			//soit on enelve ou on ajoute
			if ($signe == "-") {
				$eau = $this->getEau()-$eau;
				$electricite = $this->getElectricite()-$electricite;
				$fer = $this->getFer()-$fer;
				$fuel = $this->getFuel()-$fuel;
				$nourriture = $this->getNourriture()-$nourriture;
				
				if ($eau < 0) $eau = 0;
				if ($electricite < 0) $electricite = 0;
				if ($fer < 0) $fer = 0;
				if ($fuel < 0) $fuel = 0;
				if ($nourriture < 0) $nourriture = 0;
			}
			else {
				$eau = $this->getEau()+$eau;
				$electricite = $this->getElectricite()+$electricite;
				$fer = $this->getFer()+$fer;
				$fuel = $this->getFuel()+$fuel;
				$nourriture = $this->getNourriture()+$nourriture;

				$stockage_max = Bataille::getBatiment()->getStockage();
				$stockage_max_grenier = Bataille::getBatiment()->getStockage("grenier");

				if ($eau > $stockage_max) $eau = $stockage_max;
				if ($electricite > $stockage_max) $electricite = $stockage_max;
				if ($fer > $stockage_max) $fer = $stockage_max;
				if ($fuel > $stockage_max) $fuel = $stockage_max;
				if ($nourriture > $stockage_max_grenier) $nourriture = $stockage_max_grenier;
			}
			
			Bataille::setValues([
				"eau" => $eau,
				"electricite" => $electricite,
				"fer" => $fer,
				"fuel" => $fuel,
				"nourriture" => $nourriture
			]);


			$dbc->update("eau", $eau)
				->update("electricite", $electricite)
				->update("fer", $fer)
				->update("fuel", $fuel)
				->update("nourriture", $nourriture)
				->from("_bataille_base")
				->where("ID_base", "=", $this->id_base)
				->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}