#include <LoRaWan.h>  //Librairie ajoute les fonctions pour le Seeeduino LoRaWAN
#include "SHT31.h"
#include "SparkFun_SGP30_Arduino_Library.h"
#include <Wire.h>
#include <TinyGPS++.h>

unsigned char data[14];  //Tableau d'octets qui servira à stocker les données des capteurs
unsigned char templsb, tempmsb, humlsb, hummsb, co2lsb, co2msb;
int temp, hum, co2;
uint32_t  latitude, longitude;


SHT31 sht;
SGP30 mySensor; //create an object of the SGP30 class
TinyGPSPlus gps;

void setup(void)
{
  Serial.begin(9600); //Initialise la communication entre le PC et Arduino

  sht.begin(0x45);    //Adresse du capteur I2C
  Wire.begin();
  mySensor.begin();
  mySensor.initAirQuality();

  lora.init();  //Initialisation de la carte seeeduino LoRaWAN

  lora.setKey("2B7E151628AED2A6ABF7158809CF4F3C", "2B7E151628AED2A6ABF7158809CF4F3C", "2B7E151628AED2A6ABF7158809CF4F3C");
  //La carte a besoin de clé pour ce connecter à la passerelle(gateway). Ces clés sont laissées par default
  lora.setDeciveMode(LWABP);  //Defini l'utilisation du protocol LWABP (Lightweight Access Point Protocol) c'est le réseau grande distance utilisé par LoRa

  lora.setDataRate(DR0, EU868);
  lora.setChannel(0, 868.1);
  lora.setChannel(1, 868.3);
  lora.setChannel(2, 868.5);  //868.1 ,.3 et .5 sont 3 canaux utilisé par LoRa
  lora.setReceiceWindowFirst(0, 868.1);
  lora.setReceiceWindowSecond(869.5, DR3);

  //Definition des frequence à utiliser, ici autour de 868 MHZ (frenquence pour l'europe)

  lora.setPower(14);  //Alimentation sur le port 14 (par default)
}

void loop(void)
{
  delay(3000);  //Attente pour ne pas envoyer trop de données au serveur et pour avoir le temps de faire le transfert dans de bonne condition
  sht.read();
  temp = sht.getRawTemperature();
  templsb = (unsigned)temp & 0xff; // mask the lower 8 bits
  tempmsb = (unsigned)temp >> 8;   // shift the higher 8 bits
  hum = sht.getRawHumidity();
  humlsb = (unsigned)hum & 0xff; // mask the lower 8 bits
  hummsb = (unsigned)hum >> 8;   // shift the higher 8 bits

  mySensor.measureAirQuality();
  co2 = mySensor.CO2;
  co2lsb = (unsigned)co2 & 0xff; // mask the lower 8 bits
  co2msb = (unsigned)co2 >> 8;   // shift the higher 8 bits

  latitude  = gps.location.lat();
  longitude = gps.location.lng();


  data [0] = templsb;  //Convertie la valeur du capteur grace au CAN. La valeur est codé sur 8 bits (de 0 à 255) et stocké dans un tableau
  Serial.println(data [0],HEX);   //Affiche sur le port serie la valeur du convertisseur pour comparer avec la valeur sur le serveur
  data [1] = tempmsb;
  Serial.println(data [1],HEX);
  data [2] = humlsb;
  Serial.println(data [2],HEX);
  data [3] = hummsb;
  Serial.println(data [3],HEX);
  data [4] = co2lsb;
  Serial.println(data [4],HEX);
  data [5] = co2msb;
  Serial.println(data [5],HEX);

  data[6] =  latitude & 0xFF;
  data[7] = (latitude >> 8) & 0xFF;
  data[8] = (latitude >> 16) & 0xFF;
  data[9] = (latitude >> 24) & 0xFF;
  data[10] = longitude & 0xFF;
  data[11] = (longitude >> 8) & 0xFF;
  data[12] = (longitude >> 16) & 0xFF;
  data[13] = (longitude >> 24) & 0xFF;
  Serial.println(data [6],HEX);
  Serial.println(data [7],HEX);
  Serial.println(data [8],HEX);
  Serial.println(data [9],HEX);

  Serial.print("Temperature: ");
  Serial.println(sht.getTemperature(), 1);
  Serial.print("Humidity: ");
  Serial.println(sht.getHumidity(), 1);
  Serial.print("CO2: ");
  Serial.println(mySensor.CO2);
  Serial.print("Latitude: ");
  Serial.println(latitude, 6);
  Serial.print("Longitude: ");
  Serial.println(longitude, 6);
  lora.transferPacket(data, 14, 10); //Transfert des données
  //data => les données à transferer, sous la forme d'un tableau unsigned char
  //1 => La taille des données/du tableau
  //10 => Timout c'est à dire le temps maximum de l'envoie, retourne une erreur si ce temps est dépassé
}
