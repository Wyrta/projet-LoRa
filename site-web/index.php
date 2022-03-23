<?php
require_once('mLib.php');
?>

<!doctype html>
<html>
  <head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../style.css">

  </head>
  
    <body>
      <h1 class="titre">LoRa Maison Connectée</h1>

      <div class="tab">
          <table class="valeur" id="tabValeur">
              <tr>
                <td>Temperature</td>
                <td>Humidité</td>
                <td>CO2</td>
              </tr>
              <tr>
                  <td id="valTemp"><?php echo getTemperature($bdd) . "°C" ?></td>
                  <td id="valHum" ><?php echo getHumidite($bdd) . "%" ?></td>
                  <td id="valCO2" ><?php echo getco2($bdd) . "ppm" ?></td>
              </tr>
          </table>
      </div>

      <div class="courbes" id="courbes">
        <?php
          $humidite = getHumiditeStack($bdd);
          $CO2 = getCO2Stack($bdd);

          //courbe temperature
          affSvgTemperature($bdd);

          //courbe humidite
          affSvgHumidite($bdd);

          //courbe CO2
          affSvgCO2($bdd);
          
        ?>
      </div>
      <div class='botText'>
        <a> bottom text </a>
      </div>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script type="text/javascript" src="./refresh.js"></script>
    </body>

</html>