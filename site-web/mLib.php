<?php 

$bdd = new mysqli("localhost", "IUT", "GEII@ngers221", "maisonconnectee1");

function getTemperature($bdd) {
    $requeteTemp = $bdd->query("SELECT `valeur` FROM `capteur` WHERE `id`=(SELECT max(`id`) FROM `capteur` WHERE `type`='temperature')");
    $temp = $requeteTemp->fetch_array()[0] ?? '';
    return $temp;
}

function getHumidite($bdd) {
    $requeteHum = $bdd->query("SELECT `valeur` FROM `capteur` WHERE `id`=(SELECT max(`id`) FROM `capteur` WHERE `type`='humidite')");
    $hum = $requeteHum->fetch_array()[0] ?? '';
    
    return $hum;
}

function getco2($bdd) {
    $requeteco2 = $bdd->query("SELECT `valeur` FROM `capteur` WHERE `id`=(SELECT max(`id`) FROM `capteur` WHERE `type`='co2')");
    $co2 = $requeteco2->fetch_array()[0] ?? '';
    return $co2;
}

/**
 * @param bdd client pour la requete SQL 
 * @return resultat un tableau des dernieres valeurs de la bdd
 */
function getTemperatureStack($bdd) {
    $NbDonnee = 38;

    //Creation de la requete
    $requeteTemp = $bdd->query("SELECT * FROM (SELECT * FROM capteur WHERE type='temperature' ORDER BY id DESC LIMIT $NbDonnee) sub ORDER BY id ASC");

    //Cree le tableau 
    $resultat[0] = '';
    $i = 0;

    //Boule tant qu'il y a encore des données à traiter
    while ($row = mysqli_fetch_row($requeteTemp)) {
        $resultat[$i] =  $row[1];   //row[1] est la case du tableau avec les valeurs utilisable
        $i++;   //Incrementation de l'index du tableau 
    }

    return $resultat;
}

function getHumiditeStack($bdd) {
    $NbDonnee = 38;
    $requeteHum = $bdd->query("SELECT * FROM (SELECT * FROM capteur WHERE type='humidite' ORDER BY id DESC LIMIT $NbDonnee) sub ORDER BY id ASC");
    
    $resultat[0] = '';
    $i = 0;
    while ($row = mysqli_fetch_row($requeteHum)) {
        $resultat[$i] =  $row[1];
        $i++;
    }

    return $resultat;
}

function getCO2Stack($bdd) {
    $NbDonnee = 38;
    $requeteCO2 = $bdd->query("SELECT * FROM (SELECT * FROM capteur WHERE type='co2' ORDER BY id DESC LIMIT $NbDonnee) sub ORDER BY id ASC");
    
    $resultat[0] = '';
    $i = 0;
    while ($row = mysqli_fetch_row($requeteCO2)) {
        $resultat[$i] =  $row[1];
        $i++;
    }

    return $resultat;
}



function affSvgTemperature($bdd) {
    $temperature = getTemperatureStack($bdd);

    echo "<svg class='courbeTemp' width='1500' height='400' style='padding-top: 50px'> ";
    echo "<g transform='translate(0 300)'> <g transform='scale(1,-1)' >";

    //courbe temperature
    $vwMaw = 1500;
    $axeX = 0;
    echo "<polyline 
            fill='none' 
            stroke-width='6' stroke='red' 
            points=' ";
    for ($i=0; $i < count($temperature); $i++) {
        $temperature[$i] = intval($temperature[$i]) * 5;
        $axeX += count($temperature);
        if($axeX >= $vwMaw) break;

        echo $axeX . "," . $temperature[$i] . " ";
    }
    echo " '/>";
    echo " </g> </g>";


    echo "<line x1='33' y1='300' x2='1900' y2='300' stroke-width='2' stroke='black' />";
    for ($i=(1500/18); $i < 1500; $i += (750/18) ) {
        echo "<line x1='$i' y1='297' x2='$i' y2='303' stroke-width='2' stroke='black' />";
    }
    echo "<text x='1345' y='290' font-size='20'> Temps (10s/div) </text> ";



    echo "<line x1='33' y1='0' x2='33' y2='300' stroke-width='2' stroke='black' />";
    for ($i=-5; $i <= 300; $i += (300/6) ) {
        echo "<line x1='31' y1='$i' x2='35' y2='$i' stroke-width='2' stroke='black' />";
    }
    echo "<text x='40' y='20' font-size='20'> Température (°C) </text> ";

    //Temperature
    echo "<text x='0' y='25'  font-size='20'> 60 </text> ";
    echo "<text x='0' y='100' font-size='20'> 40 </text> ";
    echo "<text x='0' y='200' font-size='20'> 20 </text> ";
    echo "<text x='0' y='300' font-size='20'> 0  </text> ";


    echo "</svg>";
}

function affSvgHumidite($bdd) {
    $humidite = getHumiditeStack($bdd);

    echo "<svg class='courbeHum' width='1500' height='325' style='padding-top: 50px'> ";
    echo "<g transform='translate(0 300)'> <g transform='scale(1,-1)' >";

    //courbe temperature
    $vwMaw = 1500;
    $axeX = 0;
    echo "<polyline 
            fill='none' 
            stroke-width='6' stroke='blue' 
            points=' ";
            for ($i=0; $i < count($humidite); $i++) {
                $humidite[$i] = intval($humidite[$i]) * 3;
                $axeX += count($humidite);
                if($axeX >= $vwMaw) break;
            
                echo $axeX . "," . $humidite[$i] . " ";
            }
    echo " '/>";
    echo " </g> </g>";


    echo "<line x1='33' y1='300' x2='1900' y2='300' stroke-width='2' stroke='black' />";
    for ($i=(1500/18); $i < 1500; $i += (750/18) ) {
        echo "<line x1='$i' y1='297' x2='$i' y2='303' stroke-width='2' stroke='black' />";
    }
    echo "<text x='1345' y='290' font-size='20'> Temps (10s/div) </text> ";



    echo "<line x1='33' y1='0' x2='33' y2='300' stroke-width='2' stroke='black' />";
    for ($i=-5; $i <= 300; $i += (300/4) ) {
        echo "<line x1='31' y1='$i' x2='35' y2='$i' stroke-width='2' stroke='black' />";
    }
    echo "<text x='40' y='20' font-size='20'> Humidité (%) </text> ";

    //% humidité
    echo "<text x='0' y='20'  font-size='20'> 100 </text> ";
    echo "<text x='0' y='75' font-size='20'> 75 </text> ";
    echo "<text x='0' y='150' font-size='20'> 50 </text> ";
    echo "<text x='0' y='225' font-size='20'> 25 </text> ";
    echo "<text x='0' y='300' font-size='20'> 0  </text> ";


    echo "</svg>";
}

function affSvgCO2($bdd) {
    $CO2 = getCO2Stack($bdd);

    echo "<svg class='courbeCO2' width='1500' height='325' style='padding-top: 50px'> ";
    echo "<g transform='translate(0 300)'> <g transform='scale(1,-1)' >";

    //courbe temperature
    $vwMaw = 1500;
    $axeX = 0;
    echo "<polyline fill='none' stroke-width='6' stroke='white' points=' ";
    for ($i=0; $i < count($CO2); $i++) {
        $CO2[$i] = intval($CO2[$i]) / 2 - 100;
        $axeX += count($CO2);
        if($axeX >= $vwMaw) break;

        echo $axeX . "," . $CO2[$i] . " ";
    }
    echo " '/>";
    echo " </g> </g>";


    echo "<line x1='33' y1='300' x2='1900' y2='300' stroke-width='2' stroke='black' />";
    for ($i=(1500/18); $i < 1500; $i += (750/18) ) {
        echo "<line x1='$i' y1='297' x2='$i' y2='303' stroke-width='2' stroke='black' />";
    }
    echo "<text x='1345' y='290' font-size='20'> Temps (10s/div) </text> ";



    echo "<line x1='33' y1='0' x2='33' y2='300' stroke-width='2' stroke='black' />";
    for ($i=-5; $i <= 300; $i += (300/3) ) {
        echo "<line x1='31' y1='$i' x2='35' y2='$i' stroke-width='2' stroke='black' />";
    }
    echo "<text x='40' y='20' font-size='20'> Tx CO2 (ppm) </text> ";

    //Temperature
    echo "<text x='-1' y='20'  font-size='20'> 800 </text> ";
    echo "<text x='0' y='100' font-size='20'> 600 </text> ";
    echo "<text x='0' y='200' font-size='20'> 400 </text> ";
    echo "<text x='0' y='300' font-size='20'> 200 </text> ";


    echo "</svg>";
}
?>