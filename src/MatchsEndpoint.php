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
            $data = $matchHockey->getMatchHockeyById($_GET['id']);
        } else {
            $data = $matchHockey->getAllMatchHockey();
        }
        if (empty($data)) {
            deliverResponse(404, "Aucune donnée trouvée");
        } else {
            deliverResponse(200, "Succès", $data);
        }
        break;
    case "POST":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        $dataReponse = $matchHockey->addMatchHockey($data);
        if ($dataReponse == null) {
            deliverResponse(500, "Erreur de syntaxe dans votre requête ou erreur serveur lors de la création (vérifiez bien l'orthographe de votre requête)");
        } else {
            deliverResponse(201, "Créé avec succès", $dataReponse);
        }
        break;
    case "PUT":
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        $id = $_GET['id'];
        $dataReponse = $matchHockey->updateMatchHockey($id, $data);
        if ($dataReponse == null) {
            deliverResponse(500, "Erreur de syntaxe dans votre requête ou erreur serveur lors de la modification (vérifiez bien l'orthographe de votre requête)");
        } elseif ($dataReponse === 'ID non trouvé') {
            deliverResponse(404, "ID non trouvé");
        } else {
            deliverResponse(200, "Données modifiées avec succès");
        }
        break;
    case "DELETE":
        $id = $_GET['id'];
        $dataReponse = $matchHockey->deleteMatchHockey($id);
        if ($dataReponse == null) {
            deliverResponse(500, "Erreur de syntaxe dans votre requête ou erreur serveur lors de la suppression (vérifiez bien l'orthographe de votre requête)");
        } elseif ($dataReponse === 'ID non trouvé') {
            deliverResponse(404, "ID non trouvé");
        } else {
            deliverResponse(200, "Données supprimées avec succès");
        }
        break;
}
exit();
