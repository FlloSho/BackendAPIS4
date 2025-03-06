<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource joueurs

//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on inclu le modèle
include '../models/Joueur.php';
$joueur = new Joueur();

//on regarde de quel type est a requête
switch($http_methode) {
    case 'GET':
        if(isset($_GET['id'])){ //on regarde si l'utilisateur a demander un id
            $id=htmlspecialchars($_GET['id']);
            $data = $joueur->getJoueurParId($id); //si l'id est bien définit, on le récu et on le passe à la fonction avec l'id
        }else{
            $data = $joueur->tousLesJoueurs(); //sinon on appelle la fonction sans id
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

/**
 * Fonction qui va envoyer la réponse à l'utilisateur
 */
function envoyer_response($status_code, $status_message, $data=null){
    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); //Utilise un message standardisé en fonction du code HTTP
    //header("HTTP/1.1 $status_code $status_message"); //Permet de personnaliser le message associé au code HTTP
    header("Content-Type:application/json; charset=utf-8");//Indique au client le format de la réponse

    /// Construction de la réponse
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if($json_response===false)
        die('json encode ERROR : '.json_last_error_msg());

    /// Affichage de la réponse (Retourné au client)
    echo $json_response;
}


