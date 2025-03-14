<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource Participes

//CORS
//a compléter


//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on inclu le modèle
require '../models/Participe.php';
require '../controllers/deliverResponse.php';
$Participe = new Participe();

//on regarde de quel type est a requête
switch($http_methode) {
    case 'GET':
       //a faire
        break;

    case 'POST':
        //a faire
        break;

    case 'PUT':
        //a faire
        break;

    case 'DELETE' :
        //a faire
        break;
}
