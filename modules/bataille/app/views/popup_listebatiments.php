<h2>Liste des btiments à construire</h2>
<!--<p>Si vous supprimez ce module, tous les contenus, images qui y sont associés seront supprimés<br/></p>
<p class="attention">Cette action est irréverssible !</p>-->

<?php for ($i=0 ; $i<$all_batiment ; $i++):?>
	<div>
		<p><?=$batiments[$i][1]?></p>
		<ul>
			<li class="<?=$batiments[$i][2][0][1]?>">fer : <?=$batiments[$i][2][0][0]?></li>
			<li class="<?=$batiments[$i][2][1][1]?>">fuel : <?=$batiments[$i][2][1][0]?></li>
			<li class="<?=$batiments[$i][2][2][1]?>">eau : <?=$batiments[$i][2][2][0]?></li>
			<li class="<?=$batiments[$i][2][3][1]?>">électricité : <?=$batiments[$i][2][3][0]?></li>
			<li>Temps de construction : <?=$batiments[$i][3]?></li>
		</ul>
		<a nom_batiment="<?=$batiments[$i][1]?>" emplacement="<?=$_POST['emplacement']?>" nom_batiment_sql="<?=$batiments[$i][0]?>" class="construire <?=$batiments[$i][2][0][1]?> <?=$batiments[$i][2][1][1]?> <?=$batiments[$i][2][2][1]?> <?=$batiments[$i][2][3][1]?>" href="">Construire le batiment</a>
		<hr>
	</div>
<?php endfor;?>


<?php require_once(MODULEROOT."bataille/app/views/js/construire_batiment.php");?>