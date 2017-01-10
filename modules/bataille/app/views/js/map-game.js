$(document).ready(function() {
    //vars pour la map
    $map = $(".map-game");
    width = $map.width();
    height = $map.height();
    rows = Math.round(height/20);
    columns = Math.round(width/20);

    //var pour le batiment qu'on est en trainde drag
    var taille;
    var batiment;

    //génération de la grille de la map
    for (i=0 ; i<rows ; i++) {
        for (j=0 ; j<columns ; j++) {
            pleft = j*20;
            ptop = i*20;

            $map.append("<div class='case' id='case-"+pleft+"-"+ptop+"' style='left:"+pleft+"px;top:"+ptop+"px;'></div>");
        }
    }

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

    function retirerBatiment() {
        $(".case ."+batiment).removeClass("batiment");
        $(".case").removeClass(batiment);
    }

    /**
     * fonction appellée lorsqu'on relache la souris après le drag d'un élément sur la grille de la map
     * elle va appeler testBatimentPosition afin de voir si pas de batiment ou l'on vient de poser le notre
     * renvoi false si il y a un batiment et reset la os de l'élément pour le redrag
     * sinon on lance la construction du batiment
     */
    $(".map-game .case").mouseup(function() {
        pos_depart = getArrayTaille($(this).attr("id"));

        for (i=0 ; i<taille[1] ; i++) {
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
        }
        taille = "";
    });



    //------------------------------------------- DRAG AND DROP D'UN BATIMENT ------------------------------------//
    $(".liste-batiments .un-batiment").draggable({
        grid: [20, 20],
        snap: ".case",
        revert: "invalid",
        cursorAt: { top: -5, left: 0},
        start: function(event, ui) {
            taille = getArrayTaille($(this).find("a").attr("taille"));
            batiment = $(this).attr("id");
        }
    });

    $(".map-game").droppable({
        accept: ".un-batiment"
    });
})