<?php
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniuement pour la ressource joueurs

//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on inclu le modèle
require '../models/Joueur.php';
require '../controllers/deliverResponse.php';
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
            deliverResponse(404, 'Aucunes données trouvées'); //on envoie une réponse 404
        }else{
            deliverResponse(201, 'Succes', $data); //on envoie la réponse
        }
        break;

    case 'POST':
        // Récupération des données dans le corps
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
        $datareponse = $joueur->ajouterJoueur($data);

        if($datareponse == null){
            deliverResponse(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la création (vérifier bien l\'orthographe de votre requête)');
        }else{
            deliverResponse(201, 'Créer avec succès', $datareponse);
        }
        break;
    case 'PUT':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData,true);
        $id=htmlspecialchars($_GET['id']); //Si l'on veut récupérer l'id de l'url on doit passer par $_GET mm si on est en PUT ou autres requêtes

        $dataReponse = $joueur->modifierJoueur($id, $data);
        if($dataReponse == null){
            deliverResponse(500, 'Erreur de synstaxe sans votre requête ou erreur serveur lors de la modification (vérifier bien l\'orthographe de votre requête)');
        }elseif($dataReponse === 'ID non trouvé'){
            deliverResponse(404, 'ID non trouvé');
        }else{
            deliverResponse(200, 'Données modifiées avec succès');
        }
        break;
}
exit();
