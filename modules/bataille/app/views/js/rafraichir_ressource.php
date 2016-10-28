<script>
	//bout utilisé dans la popup pour construire un batment
	jQuery(function ($) {
		function setRafraichirRessource() {
			$.ajax({
				type:"POST",
				url:"<?=WEBROOT?>controller/modules/bataille/ressource/rafraichir",
				success: function(data){
					$(".ressource").html(data);
				}, error: function(){

				}
			});
		}

		setInterval(setRafraichirRessource, 20000);
	});
</script>