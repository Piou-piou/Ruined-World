<?php require_once("nav.php");?>

<h1>Aide sur les <?=\modules\bataille\app\controller\Aide::$batiment?> de type <?=\modules\bataille\app\controller\Aide::$parametre_router?></h1>

<?php for ($i=0 ; $i<$count ; $i++):?>
	<div>
		<h3><?=$aide->getNomBatiment()[$i]?></h3>
		
		<h4>Ressources nécéssaires pour le premier niveau</h4>
		<ul>
			<li>Eau : <?=$aide->getRessourceConstruire()[$i][2]?></li>
			<li>Electricité : <?=$aide->getRessourceConstruire()[$i][3]?></li>
			<li>Fer : <?=$aide->getRessourceConstruire()[$i][0]?></li>
			<li>Fuel : <?=$aide->getRessourceConstruire()[$i][1]?></li>
		</ul>

		<?php if (count($aide->getNomBatimentConstruire()[$i]) > 0) :?>
			<h4>Batiments nécéssaires pour le premier niveau</h4>
		<?php endif;?>

		<?php if (count($aide->getNomBatimentConstruire()[$i]) > 1) :?>
			<?php for($x=0 ; $x<count($aide->getNomBatimentConstruire()[$i]) ; $x++): ?>
				<ul>
					<li>Nom du batiment : <?=$aide->getNomBatimentConstruire()[$i][$x][0]?></li>
					<li>Niveau du batiment : <?=$aide->getNiveauBatimentConstruire()[$i][$x][0]?></li>
				</ul>
			<?php endfor;?>
		<?php else: ?>
			<?php for ($j=0 ; $j<count($aide->getNomBatimentConstruire()[$i]) ; $j++): ?>
				<ul>
					<li>Nom du batiment : <?=$aide->getNomBatimentConstruire()[$j]?></li>
					<li>Niveau du batiment : <?=$aide->getNiveauBatimentConstruire()[$j]?></li>
				</ul>
			<?php endfor;?>
		<?php endif;?>
	</div>
<?php endfor;?>
