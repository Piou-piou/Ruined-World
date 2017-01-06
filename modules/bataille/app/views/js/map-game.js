$(document).ready(function() {
    $map = $(".map-game");
    width = $map.width();
    height = $map.height();
    rows = Math.round(height/20);
    columns = Math.round(width/20);

    for (i=0 ; i<rows ; i++) {
        for (j=0 ; j<columns ; j++) {
            pleft = j*20;
            ptop = i*20;

            $map.append("<div class='case' id='case-"+pleft+"-"+ptop+"' style='left:"+pleft+"px;top:"+ptop+"px;'></div>");
        }
    }


    /*$(".map-game .case").on("click", function() {
        $(this).css("background-color", "red");
    });*/

    $( ".liste-batiments .un-batiment" ).draggable({
        grid: [20, 20],
        snap: ".case",
        revert: "invalid"
    });

    $(".map-game").droppable({
        accept: ".un-batiment",
    });
})