<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource joueurs

//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on regarde de quel type est a requête
switch($http_methode) {
    case 'GET':
        if(isset($_GET['id'])){ //on regarde si l'utilisateur a demander un id
            $id=htmlspecialchars($_GET['id']);
            $data = LireChunkFact($id); //si l'id est bien définit, on le récu et on le passe à la fonction avec l'id
        }else{
            $data = LireChunkFact(); //sinon on appelle la fonction sans id
        }
        if(empty($data)){ //si la réponse est vide
            envoyer_response(404, 'Aucunes données trouvées'); //on envoie une réponse 404
        }else{
            envoyer_response(201, 'Succes', $data); //on envoie la réponse
        }
        break;

    case 'POST':
        //a faire
        break;
}


