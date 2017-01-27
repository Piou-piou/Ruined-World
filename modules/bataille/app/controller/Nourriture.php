<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class Nourriture {
		private $date_last_check;
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$last_check = $this->getLastCheckNourriture();
			$nb_unite = Bataille::getUnite()->getNombreUniteHumaine();
			
			if ($last_check >= 3600) {
				$this->setNourritureConsomee(round($last_check/3600), $nb_unite);
				$this->setUpdateLastCheckNourriture();
			}
			else if (($last_check == 0) && ($nb_unite > 0)) {
				$this->setUpdateLastCheckNourriture();
			}
			
			if (Bataille::getRessource()->getNourriture() == 0) {
				Bataille::setValues(["nourriture_mort_heure" => $this->getNombreUniteMortHeure()]);
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @return int
		 * fonction qui renvoi la durée écoulée depuis le dernier check de la nourriture
		 */
		private function getLastCheckNourriture() {
			$dbc= App::getDb();
			
			$query = $dbc->select("last_check_nourriture")->from("_bataille_last_connexion")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
			if ((is_array($query)) && (count($query) == 1)) {
				$today = Bataille::getToday();
				
				foreach ($query as $obj) {
					$last_check = $obj->last_check_nourriture;
				}
				
				$this->date_last_check = $last_check;
				
				$last_co = new \DateTime($last_check);
				$last_co = $last_co->getTimestamp();
				
				return $today-$last_co;
			}
			
			return 0;
		}
		
		/**
		 * @return mixed
		 * renvoi la consommation de nourriture d'une unité par heure
		 */
		private function getConsommationNourritureUnite() {
			return  Bataille::getParam("unite_nourriture_heure");
		}
		
		private function getNombreUniteMortHeure() {
			$nb_unite = Bataille::getUnite()->getNombreUniteHumaine();
			
			return abs(round((0-($nb_unite*$this->getConsommationNourritureUnite()))/100));
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * remet la date de last_check_nourriture a Y-m-d H:i:s
		 */
		private function setUpdateLastCheckNourriture() {
			$dbc = App::getDb();
			
			$dbc->update("last_check_nourriture", date("Y-m-d H:i:s"))->from("_bataille_last_connexion")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		}
		
		/**
		 * @param $nb_heure
		 * @param $nb_unite
		 * fonction qui calcule la nourriture consomee en 1h
		 * si elle est inférieur à 0 on tue un nombre d'unite defini en fonction de combien le nombre de
		 * nourriture est en dessous de 0 (valeur divisée par 100)
		 * puis on retire les ressources du grenier
		 */
		private function setNourritureConsomee($nb_heure, $nb_unite) {
			$nourriture_base = Bataille::getRessource()->getNourriture();
			$nourriture_consommee = ($nb_unite*$this->getConsommationNourritureUnite())*$nb_heure;
			
			$nourriture_retirer = $nourriture_base - $nourriture_consommee;
			
			if ($nourriture_retirer < 0) {
				$unite_tuer = abs(round($nourriture_retirer/100));
				
				Bataille::getUnite()->setTuerUnites($unite_tuer);
			}
			
			Bataille::getRessource()->setUpdateRessource(0, 0, 0, 0, $nourriture_consommee, "-");
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}