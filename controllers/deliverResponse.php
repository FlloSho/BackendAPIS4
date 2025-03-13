<?php
/**
 * Fonction qui va envoyer la réponse à l'utilisateur
 */
function deliverResponse($status_code, $status_message, $data = null)
{
    // Paramétrage de l'entête HTTP
    // header("HTTP/1.1 $status_code $status_message");
    $response['status'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    // Envoi de la réponse au format JSON
    $json_response = json_encode($response);
    echo $json_response;
}
