<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource Commentaire

//CORS
//a compléter


//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on inclu le modèle
require '../models/Commentaire.php';
require '../controllers/deliverResponse.php';
$Commentaire = new Commentaire();

//on regarde de quel type est a requête
//on regarde de quel type est a requête
switch($http_methode) {
    case 'GET':
        if(isset($_GET['idJoueur'])){ //on regarde si l'utilisateur a demander un id
            $id=htmlspecialchars($_GET['idJoueur']);
            $data = $Commentaire->afficherCommentaire($id); //si l'id est bien définit, on le récup et on le passe à la fonction avec l'id
        }elseif(isset($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $data = $Commentaire->afficherUnCommentaire($id);
        }

        if(empty($data)){ //si la réponse est vide
            deliverResponse(404, 'Aucunes données trouvées'); //on envoie une réponse 404
        }elseif($data === 'ID non trouvé'){
            deliverResponse(404, 'ID non trouvé');
        }else {
            deliverResponse(201, 'Succes', $data); //on envoie la réponse
        }
        break;

    case 'POST':
        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
        $datareponse = $Commentaire->ajouterCommentaire($data);

        if($datareponse == null){
            deliverResponse(404, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la création (vérifier bien l\'orthographe de votre requête)');
        }else{
            deliverResponse(201, 'Créer avec succès', $datareponse);
        }
        break;
    case 'PUT':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        if(isset($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $dataReponse = $Commentaire->modifierCommentaire($data, $id);

            if($dataReponse == null){
                deliverResponse(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la modification (vérifier bien l\'orthographe de votre requête)');
            }elseif($dataReponse === 'ID non trouvé'){
                deliverResponse(404, 'ID non trouvé');
            }else{
                deliverResponse(200, 'Données modifiées avec succès');
            }
        }else{
            deliverResponse(404, 'Veuillez préciser l\'id du commentaire à modifier');
        }
        break;

    case 'DELETE' :
        $id=htmlspecialchars($_GET['id']);
        $dataReponse = $Commentaire->supprimerCommentaire($id);
        if($dataReponse == 'ok'){
            deliverResponse(200, 'Commentaire supprimé avec succès');
        }
        elseif($dataReponse === 'ID non trouvé'){
            deliverResponse(404, 'ID non trouvé' );
        }
        else{
            deliverResponse(500, 'Erreur lors de la suppression');
        }
        break;

}

