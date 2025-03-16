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
        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.

        if(isset($_GET['Titulaire']) and $_GET['Titulaire'] == 1){
            $datareponse = $Participe->ajouterTitulaire($data);
        }elseif (isset($_GET['Titulaire']) and $_GET['Titulaire'] == 0){
            $datareponse = $Participe->ajouterRemplacant($data);
        }else{
            $datareponse=null;
        }

        if($datareponse == null or $datareponse == false){
            deliverResponse(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la création (vérifier bien l\'orthographe de votre requête)');
        }elseif ($datareponse === 'ID non trouvé'){
            deliverResponse(404, 'ID non trouvé');
        }elseif($datareponse === 'duplicate'){
            deliverResponse(404, 'La ligne existe déjà');
        }
        else{
            deliverResponse(201, 'Créer avec succès', $datareponse);
        }
        break;

    case 'PUT':
        //a faire
        break;

    case 'DELETE' :
        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.

        if($data === null){
            deliverResponse(404, 'Veuillez indiquer dans le corps de la requête l\'id du joueuret l\' id du match de la participation à supprimer');
        }else{
            $dataReponse = $Participe->retirerParticipation($data);
            if($dataReponse === 'duplicate'){
                deliverResponse(404, 'Cette participation n\'existe pas');
            }
            elseif($dataReponse === 'ID joueur non trouvé'){
                deliverResponse(404, 'ID joueur non trouvé' );
            }
            elseif($dataReponse === 'ID match non trouvé'){
                deliverResponse(404, 'ID match non trouvé' );
            }
            elseif($dataReponse ){
                deliverResponse(200, 'Participation supprimée avec succès');
            }
            else{
                deliverResponse(500, 'Erreur lors de la suppression');
            }
        }
        break;
}
