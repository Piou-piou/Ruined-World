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

    function testBatimentPosition(posx, posy) {
        $case = $("#case-"+posx+"-"+posy);

        if ($case.hasClass("poka")) {
            return true;
        }

        return false;
    }

    $(".map-game .case").mouseup(function() {
        pos_depart = getArrayTaille($(this).attr("id"));

        for (i=0 ; i<taille[1] ; i++) {
            for (j=0 ; j<taille[0] ; j++) {
                posx = j+pos_depart[0];
                posy = i+pos_depart[1];

                if (testBatimentPosition(posx, posy) == false) {
                    $("#case-"+posx+"-"+posy).css("background-color", "red");
                    $("#case-"+posx+"-"+posy).addClass("poka");
                }
                else {
                    alert("batiment deja present");
                    taille = "";
                    console.log($(batiment));
                    $(batiment).draggable();
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
            batiment = "#"+$(this).attr("id");
        }
    });

    $(".map-game").droppable({
        accept: ".un-batiment",
        drop: function(event, ui) {
            $(ui.draggable).draggable({ disabled: true });

           // $("#popup-base").addClass("visible");
        }
    });
})