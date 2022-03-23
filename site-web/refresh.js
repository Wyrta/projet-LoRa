//Permet de declencher la fonction toute les x milisec
setInterval(majValeurs, 10000);

function majValeurs() {
    //Change la valeurs des elements
    document.getElementById("valTemp").innerHTML = '<?php getTemperature($bdd)?>';
    document.getElementById("valHum").innerHTML  = '<?php getHumidite($bdd)?>';
    document.getElementById("valCO2").innerHTML  = '<?php getco2($bdd)?>';

    //Met à jours le tableau sur la page
    $( ".valeur" ).load(window.location.href + " .valeur" );
    $( ".courbes" ).load(window.location.href + " .courbes" );

}

