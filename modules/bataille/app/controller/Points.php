<?php
	namespace modules\bataille\app\controller;

	use core\App;
	use core\HTML\flashmessage\FlashMessage;
	
	
	class Points {
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct($start = null) {
			$dbc = App::getDb();
			
			if ($start === null) $start = 0;
			
			$query = $dbc->select("identite.pseudo")
				->select("_bataille_infos_player.points")
				->select("_bataille_infos_player.ID_identite")
				->from("_bataille_infos_player")
				->from("identite")
				->where("abandon", "!=", 1, "AND")
				->where("_bataille_infos_player.ID_identite", "=", "identite.ID_identite", "", true)
				->orderBy("_bataille_infos_player.points", "DESC")
				->limit($start, 50)
				->get();
			
			if ((is_array($query)) && (count($query) > 0)) {
				$count = 1;
				foreach ($query as $obj) {
					$values[] = [
						"id_identite" => $obj->ID_identite,
						"pseudo" => $obj->pseudo,
						"points" => $obj->points,
						"classement" => $count
					];
					
					$count++;
				}
				
				Bataille::setValues(["classement" => $values]);
			}
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_base
		 * @return int
		 * renvoi les points de la base
		 */
		public static function getPointsBase($id_base) {
			$dbc = App::getDb();
			
			//on récupère les points de la base en cours
			$query = $dbc->select("points")
				->from("_bataille_base")
				->where("ID_base", "=", $id_base, "AND")
				->where("ID_identite", "=", $_SESSION['idlogin'.CLEF_SITE])
				->get();
			
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return $obj->points;
				}
			}
			
			return 0;
		}
		
		/**
		 * @param null $id_identite
		 * @return int
		 * renvoi les points totaux d'un joueur
		 */
		public static function getPointsJoueur($id_identite=null) {
			$dbc = App::getDb();
			
			if ($id_identite === null) {
				$id_identite = Bataille::getIdIdentite();
			}
			
			$query = $dbc->select("points")->from("_bataille_infos_player")->where("ID_identite", "=", $id_identite)->get();
			
			if (count($query) == 1) {
				foreach ($query as $obj) {
					return $obj->points;
				}
			}
			
			return 0;
		}
		
		/**
		 * @return mixed
		 * fonction qui renvoi le nombre de points à ajouter à la base lorsqu'on update un batiment
		 */
		private static function getPointAjoutBatiment() {
			return Bataille::getParam("points_batiment");
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_base
		 * @param null $type
		 * @param null $points
		 * @return int|null
		 * fonction qui ajoute des points à la base en fonction du type
		 * le type peut etre : batiment, attaque, defense, troupe
		 */
		public static function setAjouterPoints($id_base, $type=null, $points=null) {
			$dbc = App::getDb();

			if ($type == "batiment") {
				$points_faction = self::getPointAjoutBatiment();
				$points = self::getPointsBase($id_base)+self::getPointAjoutBatiment();
			}
			
			$dbc->update("points", $points)
				->from("_bataille_base")
				->where("ID_base", "=", $id_base)
				->set();
			
			self::setAjouterPointsTotaux();
			self::setAjouterPointsFaction($points_faction);
			
			return $points;
		}
		
		/**
		 * fonction qui prend les points de toutes les bases et qui les ajoute sur le joueur en lui même
		 */
		private static function setAjouterPointsTotaux() {
			$dbc = App::getDb();
			
			$query = $dbc->select("points")->from("_bataille_base")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
			if ((is_array($query)) && (count($query) > 0)) {
				$points = 0;
				
				foreach ($query as $obj) {
					$points += $obj->points;
				}
				
				$dbc->update("points", $points)->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
			}
		}
		
		/**
		 * fonction qui permet d'ajouter tous les points du joueur aux points de la faction
		 */
		public static function setRejoindreQuitterFaction($del = null) {
			$dbc = App::getDb();
			
			$query = $dbc->select("_bataille_faction.points_faction, _bataille_faction.ID_faction")
				->from("_bataille_faction,_bataille_infos_player")
				->where("_bataille_infos_player.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_faction.ID_faction", "=", "_bataille_infos_player.ID_faction", "", true)
				->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$point_joueur = Points::getPointsJoueur();
					$calc = $obj->points_faction - $point_joueur;
					
					if ($del === null) {
						$calc = $point_joueur+$obj->points_faction;
					}
					
					$dbc->update("points_faction", $calc)
						->from("_bataille_faction")
						->where("ID_faction", "=", $obj->ID_faction)
						->set();
				}
			}
		}
		
		/**
		 * @param $points
		 * permet d'ajouter des points à la faction
		 */
		public static function setAjouterPointsFaction($points) {
			$dbc = App::getDb();
			
			$query = $dbc->select("_bataille_faction.points_faction, _bataille_faction.ID_faction")
				->from("_bataille_faction,_bataille_infos_player")
				->where("_bataille_infos_player.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_faction.ID_faction", "=", "_bataille_infos_player.ID_faction", "", true)
				->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$dbc->update("points_faction", $points+$obj->points_faction)
						->from("_bataille_faction")
						->where("ID_faction", "=", $obj->ID_faction)
						->set();
				}
			}
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}