<?php
	use \modules\bataille\app\controller\Bataille;
	Bataille::getRessource()->setActualiserRessource();
?>

<div class="ressource">
	<p class="<?=Bataille::getRessource()->getMaxEau()?>">Eau : <?=Bataille::getRessource()->getEau()?> (+<?=Bataille::getBatiment()->getProduction("eau")?>)</p>
	<p class="<?=Bataille::getRessource()->getMaxElectricite()?>">Elétricité : <?=Bataille::getRessource()->getElectricite()?> (+<?=Bataille::getBatiment()->getProduction("electricite")?>)</p>
	<p class="<?=Bataille::getRessource()->getMaxFer()?>">Fer : <?=Bataille::getRessource()->getFer()?> (+<?=Bataille::getBatiment()->getProduction("fer")?>)</p>
	<p class="<?=Bataille::getRessource()->getMaxFuel()?>">Fuel : <?=Bataille::getRessource()->getFuel()?> (+<?=Bataille::getBatiment()->getProduction("fuel")?>)</p>
</div>