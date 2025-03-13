<?php
require "../models/MatchHockey.php";
require "../controllers/deliverResponse.php";

$matchHockey = new MatchHockey();

// Récupération de la méthode HTTP
$method = $_SERVER["REQUEST_METHOD"];

// Switch sur la méthode HTTP
switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            $data = $matchHockey->getMatchParId($_GET['id']);
        } else {
            $data = $matchHockey->tousLesMatchs();
        }
        if (empty($data)) {
            deliverResponse(404, "Erreur 404 : Aucune donnée trouvée");
        } else {
            deliverResponse(200, "200 : Succès", $data);
        }
        break;
    case "POST":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        $dataReponse = $matchHockey->ajouterMatchHockey($data['nomAdversaire'], $data['lieu'], $data['dateHeure']);
        if ($dataReponse == null) {
            deliverResponse(400, "Erreur 400 : Requête invalide. Vérifiez la syntaxe et l'orthographe de votre requête");
        } else {
            deliverResponse(201, "201 : Créé avec succès", $dataReponse);
        }
        break;
    case "PUT":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        $id = $_GET['id'];
        $dataReponse = $matchHockey->modifierMatchHockey($id, $data['nomAdversaire'], $data['lieu'], $data['dateHeure']);
        if ($dataReponse == null) {
            deliverResponse(400, "Erreur 400 : Requête invalide. Vérifiez la syntaxe et l'orthographe de votre requête");
        } elseif ($dataReponse === 'ID non trouvé') {
            deliverResponse(404, "ID non trouvé");
        } else {
            deliverResponse(200, "Données modifiées avec succès");
        }
        break;
    case "DELETE":
        $id = $_GET['id'];
        $dataReponse = $matchHockey->supprimerMatchHockey($id);
        if ($dataReponse == null) {
            deliverResponse(400, "Erreur 400 : Requête invalide. Vérifiez la syntaxe et l'orthographe de votre requête");
        } elseif ($dataReponse === 'ID non trouvé') {
            deliverResponse(404, "ID non trouvé");
        } else {
            deliverResponse(200, "Données supprimées avec succès");
        }
        break;
}
exit();
