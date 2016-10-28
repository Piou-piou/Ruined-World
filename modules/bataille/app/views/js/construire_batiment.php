<script>
	//bout utilisé dans la popup pour construire un batment
	$(document).ready(function() {
		$(".popup a.construire").click(function(e) {
			e.preventDefault();

			if (!$(this).hasClass("rouge")) {
				var nom_batiment = $(this).attr("nom_batiment");
				var nom_batiment_sql = $(this).attr("nom_batiment_sql");
				var emplacement = $(this).attr("emplacement");

				$.ajax({
					type:"POST",
					data: "nom_batiment="+nom_batiment+"&nom_batiment_sql="+nom_batiment_sql+"&emplacement="+emplacement,
					url:"<?=WEBROOT?>controller/modules/bataille/batiment/construire",
					success: function(data){
						location.reload();
					}, error: function(){

					}
				});
			}
		});
	});
</script>

<?php
	//parti qui s'active des lors qu'un batiment est en construction
	use \modules\bataille\app\controller\Bataille;
	if (is_int(Bataille::getBatiment()->getDateFinConstruction()) && Bataille::getBatiment()->getDateFinConstruction() > 0):
?>
	<script>
		$(function () {
			$('#defaultCountdown').countdown({
				until: <?=Bataille::getBatiment()->getDateFinConstruction()?>,
				onExpiry: setTerminerConstruction
			});
		});

		function setTerminerConstruction() {
			$.ajax({
				url:"<?=WEBROOT?>controller/modules/bataille/batiment/terminer_construction",
				success: function(data){
					location.reload();
				}, error: function(){

				}
			});
		}
	</script>
<?php endif;?>