<?php

function hashMotDePasse($mdp){
    return password_hash($mdp, PASSWORD_BCRYPT);
}

function verifierMotDePasse($mdp, $hash){
    return password_verify($mdp, $hash);
}
