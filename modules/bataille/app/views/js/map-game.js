$(document).ready(function() {
    //var pour le batiment qu'on est en trainde drag
    var taille;
    var batiment;

    /**
     * @param taille
     * @returns {Array}
     * fonction qui renvoi un tableau contenant la taille x et y d'un batiment
     * taille[1] = nom batiment ou case
     * taille[1] = taille en x
     * taille[2] = taille en y
     */
    function getArrayTaille(taille) {
        temp = taille.split("-");

        taille = [parseInt(temp[1]), parseInt(temp[2])];

        return taille;
    }

    /**
     * @param posx
     * @param posy
     * @returns {boolean}
     * fonction qui test si il y a déjà un batiment la ou on vient de poser le nouveau
     */
    function testBatimentPosition(posx, posy) {
        $case = $("#case-"+posx+"-"+posy);

        if ($case.hasClass("batiment")) {
            return true;
        }

        return false;
    }

    /**
     * fonction qui permet de retirer la class batiment sur une case
     * afin de rendre celle-ci disponible pour un futur drop
     */
    function retirerBatiment() {
        $(".case."+batiment).removeClass("batiment");
        $(".case").removeClass(batiment);
    }

    /**
     * fonction qui permet d'afficher ou de masquer la grille de la map
     */
    $(".bataille #afficher-grille").click(function(e) {
        e.preventDefault();

        console.log("dfg");

        $(".bataille .map-game .case").toggleClass("visible");

        if ($(".bataille .map-game .case").hasClass("visible")) {
            $(this).html("Masquer la grille");
        }
        else {
            $(this).html("Afficher la grille");
        }
    });



    //------------------------------------------- DRAG AND DROP D'UN BATIMENT ------------------------------------//
    $(".liste-batiments .un-batiment").draggable({
        grid: [20, 20],
        snap: ".case",
        revert: "invalid",
        cursorAt: { top: -5, left: 0},
        start: function(event, ui) {
            taille = getArrayTaille($(this).find("a").attr("taille"));
            batiment = $(this).find("a").attr("nom_batiment_sql");
            $(".bataille .map-game .case").toggleClass("visible");
        },
        stop: function() {
            $(".bataille .map-game .case").toggleClass("visible");
        }
    });

    $(".map-game").droppable({
        accept: ".un-batiment"
    });

    /**
     * fonction appellée lorsqu'on relache la souris après le drag d'un élément sur la grille de la map
     * elle va appeler testBatimentPosition afin de voir si pas de batiment ou l'on vient de poser le notre
     * renvoi false si il y a un batiment et reset la os de l'élément pour le redrag
     * sinon on lance la construction du batiment
     */
    $(".map-game .case").mouseup(function() {
        pos_depart = getArrayTaille($(this).attr("id"));

        var nom_batiment = $(this).attr("nom_batiment");
        var nom_batiment_sql = $(this).attr("nom_batiment_sql");

        $.ajax({
            type:"POST",
            data: "nom_batiment="+nom_batiment+"&nom_batiment_sql="+nom_batiment_sql,
            url:"{{WEBROOT}}controller/modules/bataille/batiment/placer_batiment",
            success: function(data){

            }, error: function(){

            }
        });

        /*for (i=0 ; i<taille[1] ; i++) {
            for (j=0 ; j<taille[0] ; j++) {
                posx = j+pos_depart[0];
                posy = i+pos_depart[1];

                if (testBatimentPosition(posx, posy) == false) {
                    $("#case-"+posx+"-"+posy).addClass(batiment+" batiment");
                }
                else {
                    alert("batiment deja present");
                    taille = "";
                    $("#"+batiment).css({
                        left: 0,
                        top: 0
                    });
                    retirerBatiment();
                    return false;
                }

            }
        }*/
        taille = "";
    });
})