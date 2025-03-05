<?php
require '../controllers/GestionMDPController.php';
require '../config/db.php'; // Inclut la connexion PDO à la base de données $pdo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Utilisateur
{
    private PDO $bdd;

    public function __construct()
    {
        global $pdo; // Récupère la connexion PDO globale
        if (!isset($pdo)) {
            throw new Exception("La connexion à la base de données n'a pas été initialisée.");
        }
        $this->bdd = $pdo;
    }

    /**
     * Récupère un utilisateur en fonction de son nom d'utilisateur et de son mot de passe.
     * @param $login
     * @param $mdp
     * @return bool
     */
    public function getUtilisateur($login, $mdp)
    {
        $req = $this->bdd->prepare('SELECT count(*) FROM Utilisateur WHERE username = ?');
        $req->execute(array($login));
        $resultat = $req->fetchColumn();
        if ($resultat == 1) {
            $req = $this->bdd->prepare('SELECT password FROM Utilisateur WHERE username = ?');
            $req->execute(array($login));
            $password = $req->fetchColumn();
            return verifierMotDePasse($mdp, $password);
        } else {
            echo "Utilisateur non trouvé.";
            return false;
        }
    }
}