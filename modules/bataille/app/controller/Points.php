<?php
	namespace modules\bataille\app\controller;

	use core\App;
	
	
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
				$points = self::getPointsBase($id_base)+self::getPointAjoutBatiment();
			}
			
			$dbc->update("points", $points)
				->from("_bataille_base")
				->where("ID_base", "=", $id_base)
				->set();
			
			self::setAjouterPointsTotaux();
			
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
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}