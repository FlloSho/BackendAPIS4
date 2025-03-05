<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../config/db.php'; // Inclut la connexion PDO à la base de données $pdo

class MatchHockey
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
     * Ajoute un match à la table 'matchhockey'
     * @param $nomAdversaire
     * @param $lieu
     * @param $dateHeure
     * @return bool
     */
    public function ajouterMatchHockey($nomAdversaire, $lieu, $dateHeure) {
        try {
            $req = $this->bdd->prepare('INSERT INTO MatchHockey (nomAdversaire, lieu, dateHeure, resultat) VALUES (?, ?, ?, ?)');
            return $req->execute(array($nomAdversaire, $lieu, $dateHeure, 'En attente'));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère tous les matchs de la table 'matchhockey'.
     *
     * @return array Un tableau contenant tous les joueurs.
     */
    public function tousLesMatchs()
    {
        try{
            $req = $this->bdd->prepare('SELECT * FROM MatchHockey');
            $req->execute();

            //gestion de des exeptions PDO
        }catch(Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return false;
        }

        // Récupère tous les résultats et les retourne sous forme de tableau
        return $req->fetchAll();
    }

    /**
     * Récupère un match en fonction de son id
     * @param $id
     * @return mixed
     */
    public function getMatchParId($id)
    {
        try {
            $req = $this->bdd->prepare('SELECT * FROM MatchHockey WHERE id = ?');
            $req->execute(array($id));
            return $req->fetch();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un match de la table 'matchhockey' en fonction d'un id
     * @param $id
     * @return bool
     */
    public function supprimerMatchHockey($id) {
        try {
            $req = $this->bdd->prepare("DELETE FROM MatchHockey WHERE id = ?");
            return $req->execute(array($id));

        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Modifie un match dans la table 'matchhockey'
     * @param $id
     * @param $nomAdversaire
     * @param $lieu
     * @param $dateHeure
     * @return bool
     */
    public function modifierMatchHockey($id, $nomAdversaire, $lieu, $dateHeure) {
        try {
            $req = $this->bdd->prepare("UPDATE MatchHockey SET nomAdversaire = ?, lieu = ?, dateHeure = ? WHERE id = ?");
            return $req->execute(array($nomAdversaire, $lieu, $dateHeure, $id));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Ajoute un résultat à un match selon un id
     * @param $id
     * @param $resultat
     * @return bool
     */
    public function ajouterResultat($id, $resultat) {
        try {
            $req = $this->bdd->prepare("UPDATE MatchHockey SET resultat = ? WHERE id = ?");
            $req->execute(array($resultat, $id));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Récupère les matchs à venir
     * @return array
     */
    public function getMatchsAVenir()
    {
        try {
            $req = $this->bdd->prepare('SELECT * FROM MatchHockey WHERE dateHeure > NOW()');
            $req->execute();
            return $req->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }
}
?>
