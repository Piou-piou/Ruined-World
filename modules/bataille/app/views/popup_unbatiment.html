<?php use \modules\bataille\app\controller\Bataille; ?>
<h2><?=$_POST['nom_batiment']?></h2>
<h3>Niveau : <?=Bataille::getBatiment()->getNiveau()?></h3>

<hr>
<?php if ($max_level == false){?>
<h2>Cout pour le prochain niveau</h2>
<h3>Niveau suivant : <?=Bataille::getBatiment()->getNiveau()+1?></h3>
<ul>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[0][1]?>">fer : <?=Bataille::getBatiment()->getRessourceConstruire()[0][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[1][1]?>">fuel :<?=Bataille::getBatiment()->getRessourceConstruire()[1][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[2][1]?>">eau : <?=Bataille::getBatiment()->getRessourceConstruire()[2][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[3][1]?>">électricité : <?=Bataille::getBatiment()->getRessourceConstruire()[3][0]?></li>
	<li>Temps de construction : <?=Bataille::getBatiment()->getTempsConstruction()?></li>
</ul>
<h4><?=Bataille::getBatiment()->getInfoBatiment()?></h4>

<a nom_batiment="<?=$_POST['nom_batiment']?>"
   emplacement="<?=$_POST['emplacement']?>"
   nom_batiment_sql="<?=Bataille::getBatiment()->getNomBatimentSql()?>"
   class="construire <?=Bataille::getBatiment()->getRessourceConstruire()[0][1]?>
   <?=Bataille::getBatiment()->getRessourceConstruire()[1][1]?>
   <?=Bataille::getBatiment()->getRessourceConstruire()[2][1]?>
   <?=Bataille::getBatiment()->getRessourceConstruire()[3][1]?>" href="">Construire le batiment
</a>

<?php
} else if (in_array($_POST['nom_batiment'], Bataille::getBatiment()->getTestBatimentProductionRessource())) {
?>
<?php Bataille::getBatiment()->getUnBatiment($_POST['nom_batiment']." addon", 0)?>
<!-- Partie pour la construction d'addon -->
<h3>Construire l'addon</h3>
<p>L'addon permet d'améliorer la production de ce bâtiment !</p>

<h2>Cout pour le prochain niveau</h2>
<h3>Niveau suivant : <?=Bataille::getBatiment()->getNiveau()+1?></h3>
<ul>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[0][1]?>">fer : <?=Bataille::getBatiment()->getRessourceConstruire()[0][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[1][1]?>">fuel :<?=Bataille::getBatiment()->getRessourceConstruire()[1][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[2][1]?>">eau : <?=Bataille::getBatiment()->getRessourceConstruire()[2][0]?></li>
	<li class="<?=Bataille::getBatiment()->getRessourceConstruire()[3][1]?>">électricité : <?=Bataille::getBatiment()->getRessourceConstruire()[3][0]?></li>
	<li>Temps de construction : <?=Bataille::getBatiment()->getTempsConstruction()?></li>
</ul>

<h4><?=Bataille::getBatiment()->getInfoBatiment()?></h4>

<a nom_batiment="<?=$_POST['nom_batiment']. " addon"?>"
   emplacement="<?=$_POST['emplacement']?>"
   nom_batiment_sql="<?=Bataille::getBatiment()->getNomBatimentSql()?>"
   class="construire <?=Bataille::getBatiment()->getRessourceConstruire()[0][1]?>
	<?=Bataille::getBatiment()->getRessourceConstruire()[1][1]?>
	<?=Bataille::getBatiment()->getRessourceConstruire()[2][1]?>
	<?=Bataille::getBatiment()->getRessourceConstruire()[3][1]?>" href="">Construire le batiment
</a>

<?php } else {?>
<!-- partie quand le batiment est au lvl max -->
<p>Le batiment a atteint son niveau maxiumum</p>
<?php }?>
<?php require_once(MODULEROOT."bataille/app/views/js/construire_batiment.php");?>