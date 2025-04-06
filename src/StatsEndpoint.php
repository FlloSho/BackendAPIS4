<?php
require_once __DIR__ . '/../config/cors.php';
//ce fichier va réceptionner les requêtes de l'utilisateur et les rediriger vers les bonnes fonctions
//uniquement pour la ressource stats (ressources participe mais dicisé en deux parties)

// Définir les en-têtes CORS au début
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");


//regarde si l'utilisateur à envoyé une requête HTTP
$http_methode = $_SERVER['REQUEST_METHOD'];

//on inclu le modèle
require '../models/Participe.php';
require '../controllers/deliverResponse.php';
$Participe = new Participe();

//on regarde de quel type est a requête
switch ($http_methode) {
    case 'GET':
        if (isset($_GET['idM'])) {
            $id = htmlspecialchars($_GET['idM']);
            $data = $Participe->getNoteMoyenne($id);
        } elseif(isset($_GET['idV'])){
            $id = htmlspecialchars($_GET['idV']);
            $data = $Participe->getPourcentageVictoire($id);
        }elseif(isset($_GET['idP'])) {
            $id = htmlspecialchars($_GET['idP']);
            $data = $Participe->getPourcentageDefaite($id);
        }else{
            $data = null;
        }
        if (empty($data)) { //si la réponse est vide
            deliverResponse(404, 'Aucunes données trouvées'); //on envoie une réponse 404
        } else {
            deliverResponse(201, 'Succes', $data); //on envoie la réponse
        }
        break;
}
exit();

