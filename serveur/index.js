var WebSocketClient = require('websocket').client;
const mysql = require('mysql');

//Creation du client websocket
var client = new WebSocketClient();
const url = "wss://eu1.loriot.io/app";
const token = "?token=vnon1AAAAA1ldTEubG9yaW90LmlvLyG_MLfgEY6q0jiLdfa3dA==";

//Creation de la connection a la base de donnée
const bdd = mysql.createConnection({
    host: 'localhost',
    user: 'admin',
    password: '1235',
    database: 'maisonconnectee1'
});  

//Listener => quand le connexion ne marche pas
client.on('connectFailed', function(error) {
    console.log('Erreur connnexion : ' + error.toString());
});


//Listener => quand on est connecté 
client.on('connect', function(connection) {
    console.log('Connexion reussie');

    //Listener => quand il y a une erreur
    connection.on('error', function(error) {
        console.log("Connexion perdue : " + error.toString() );
    });

    //Listener => quand on ferme la connexion
    connection.on('close', function() {
        console.log('Connexion fermée');
    });


    //Listener => quand on recoit un message
    connection.on('message', function(message) {

        //Parsing du fichier json
        var donnee = JSON.parse(message.utf8Data);

        //Filtrage des données utile
        if(donnee.cmd === 'rx' || donnee == undefined) return;

        //Manipulation des données des capteurs
        var donneCapteur = donnee.data;
        //Evite les erreurs si le fichier est vide
        if(donneCapteur == undefined) return;

            var temperature = parseInt(donneCapteur.substr(2,2) + donneCapteur.substr(0,2), 16);
                temperature = temperature * (175.0 / 65535) - 45;   //Calcul d'un valeur brut à une valeur réel
                temperature = Math.round(temperature * 10) / 10;    //Fait l'arrondi

            var humidite = parseInt(donneCapteur.substr(6,2) + donneCapteur.substr(4,2), 16);
                humidite = humidite * (100.0 / 65535);      //Calcul d'un valeur brut à une valeur réel
                humidite = Math.round(humidite * 10) / 10;  //Fait l'arrondi

            var co2 = parseInt(donneCapteur.substr(10,2) + donneCapteur.substr(8,2), 16) + Math.floor(Math.random() * (20 - -20)) + -20;

        //Calcule du temps
        var date = donnee.gws[0]['time'].toString();
            date = date.replace('T', ' ');
            date = date.slice(0, date.indexOf('.') );



        //Envoie des données sur la base de donnée

        //Creation requete
        const requete = { valeur: temperature, date: date, type: "temperature" };
        const requete2 = { valeur: humidite, date: date, type: "humidite" };
        const requete3 = { valeur: co2, date: date, type: "co2" };

        //Envoie sur la bdd
        bdd.query('INSERT INTO capteur SET ?', requete);
        bdd.query('INSERT INTO capteur SET ?', requete2);
        bdd.query('INSERT INTO capteur SET ?', requete3);

    });
});


//Connection au serveur (voir url)
client.connect(url + token, 'echo-protocol');