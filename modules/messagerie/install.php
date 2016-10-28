<?php

	$requete = "
		DROP TABLE IF EXISTS _messagerie_boite_reception ;

		CREATE TABLE _messagerie_boite_reception (
			ID_boite_reception INT  AUTO_INCREMENT NOT NULL,
		 	ID_identite INT,
		 	ID_message INT NOT NULL,
		 	supprimer INT,
		 	PRIMARY KEY (ID_boite_reception) ) ENGINE=InnoDB;

		DROP TABLE IF EXISTS _messagerie_message ;

		CREATE TABLE _messagerie_message (
			ID_message INT  AUTO_INCREMENT NOT NULL,
			ID_expediteur INT,
			date DATETIME,
		 	message TEXT,
		 	objet VARCHAR(100),
		 	url VARCHAR(255),
		 	PRIMARY KEY (ID_message) ) ENGINE=InnoDB;
	";