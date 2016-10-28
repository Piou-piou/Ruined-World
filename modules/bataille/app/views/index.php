<?php use \modules\bataille\app\controller\Bataille; ?>
<?php require_once("nav.php");?>

<div class="row bataille">
	<div class="small-12 medium-3 large-3 columns side-bar">
		<h3>Construction en cours</h3>
		<?=Bataille::getBatiment()->getNomBatimentConstruction()?><br>

		<div id="defaultCountdown"></div>
	</div>
	<div class="small-12 medium-6 large-6 columns">
		<h2>Batiments</h2>
		<?php $count = count(Bataille::getBase()->getBatiments()); for ($i=0 ; $i<$count ; $i++):?>
			<a href="#" class="open-popup" popup="popup-base" emplacement="<?=$i+1?>" nom_batiment="<?=Bataille::getBase()->getBatiments()[$i][0]?>">
				<?=Bataille::getBase()->getBatiments()[$i][0]?> <?=Bataille::getBase()->getBatiments()[$i][2]?>
			</a><br>
		<?php endfor;?>
	</div>
	<div class="small-12 medium-3 large-3 columns side-bar">
		<h3>Unités</h3>
	</div>
</div>







<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="<?=MODULEWEBROOT?>bataille/app/views/js/jquery.plugin.min.js"></script>
<script src="<?=MODULEWEBROOT?>bataille/app/views/js/jquery.countdown.js"></script>
<?php require_once(MODULEROOT."bataille/app/views/js/construire_batiment.php");?>
<?php require_once(MODULEROOT."bataille/app/views/js/rafraichir_ressource.php");?>