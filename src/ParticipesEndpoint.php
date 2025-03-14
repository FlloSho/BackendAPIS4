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
        if(isset($_GET['ListeT'])){ //on regarde si l'utilisateur a demander un id
            $id=htmlspecialchars($_GET['ListeT']);
            $data = $Participe->getTitulaires($id); //si l'id est bien définit, on le récu et on le passe à la fonction avec l'id
        }elseif(isset($_GET['ListeR'])){
            $id=htmlspecialchars($_GET['ListeR']);
            $data = $Participe->getRemplacants($id);
        }else{
            $data = null;
        }

        if(empty($data)){ //si la réponse est vide
            deliverResponse(404, 'Aucunes données trouvées'); //on envoie une réponse 404
        }elseif($data === 'ID non trouvé'){
            deliverResponse(404, 'ID non trouvé');
        }else{
            deliverResponse(201, 'Succes', $data); //on envoie la réponse
        }
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
