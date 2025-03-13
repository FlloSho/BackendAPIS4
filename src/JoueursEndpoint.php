<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource joueurs

//CORS


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
        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
        $datareponse = $joueur->ajouterJoueur($data);

        if($datareponse == null){
            envoyer_response(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la création (vérifier bien l\'orthographe de votre requête)');
        }else{
            envoyer_response(201, 'Créer avec succès', $datareponse);
        }
        break;
    case 'PUT':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        $id=htmlspecialchars($_GET['id']); //Si l'on veut récupérer l'id de l'url on doit passer par $_GET mm si on est en PUT ou autres requêtes

        $dataReponse = $joueur->modifierJoueur($id, $data);
        if($dataReponse == null){
            envoyer_response(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la modification (vérifier bien l\'orthographe de votre requête)');
        }elseif($dataReponse === 'ID non trouvé'){
            envoyer_response(404, 'ID non trouvé');
        }else{
            envoyer_response(200, 'Données modifiées avec succès');
        }
        break;
    case 'DELETE' :
        $id=htmlspecialchars($_GET['id']);
        $dataReponse = $joueur->supprimerJoueur($id);
        if($dataReponse == 'ok'){
            envoyer_response(200, 'Joeur supprimé avec succès');
        }
        elseif($dataReponse === 'ID non trouvé'){
            envoyer_response(404, 'ID non trouvé' );
        }
        else{
            envoyer_response(500, 'Erreur lors de la suppression');
        }
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


