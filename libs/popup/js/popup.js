function OpenSupprimerPopup(lien, id_popup) {
    $("#"+id_popup).addClass("visible");

    $("#"+id_popup).find("a.valider").attr("href", lien);
}

$(document).ready(function() {
    $(".popup .fermer").click(function() {
        $(".popup").removeClass("visible");
        $("body").css("overflow-y", "auto");
    });
    $(".popup .annuler").click(function() {
        $(".popup").removeClass("visible");
        $(".popup").find("a.valider").attr("href", "");
        $("body").css("overflow-y", "auto");
    });

    //popup qui s'ouvre pour valider suppression article bloc
    $(".open-popup").click(function(e) {
        e.preventDefault();
        
        $("body").css("overflow-y", "hidden");

        OpenSupprimerPopup($(this).attr("href"), $(this).attr("popup"));
    });
});