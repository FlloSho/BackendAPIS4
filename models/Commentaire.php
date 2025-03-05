<?php
require '../config/db.php'; // Inclut la connexion PDO à la base de données $pdo

class Commentaire
{
    private $bdd;

    public function __construct()
    {
        global $pdo; // Récupère la connexion PDO globale
        if (!isset($pdo)) {
            throw new Exception("La connexion à la base de données n'a pas été initialisée.");
        }
        $this->bdd = $pdo;
    }

    /**
     * Affiche tous les commentaire d'un joueur
     * @param $idJoueur
     * @return array
     */
    public function afficherCommentaire($idJoueur)
    {
        $req = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id_1 = ?');
        $req->execute(array($idJoueur));
        return $req->fetchAll();
    }

    /**
     * Affiche un commentaire en fonction de son id
     * @param $idCommentaire
     * @return mixed
     */
    public function afficherUnCommentaire($idCommentaire)
    {
        $req = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id = ?');
        $req->execute(array($idCommentaire));
        return $req->fetch();
    }

    /**
     * Ajoute un commentaire à un joueur
     * @param $commentaire
     * @param $idJoueur
     * @return bool
     */
    public function ajouterCommentaire($commentaire, $idJoueur)
    {
        $req = $this->bdd->prepare('INSERT INTO Commentaire (commentaire, id_1) VALUES (?, ?)');
        try{
            $req->execute(array($commentaire, $idJoueur));
        }catch(Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return false;
        }
        return true;
    }

    /**
     * Modifie un commentaire
     * @param $commentaire
     * @param $idCommentaire
     * @return bool
     */
    public function modifierCommentaire($commentaire, $idCommentaire)
    {
        $req = $this->bdd->prepare('UPDATE Commentaire SET commentaire = ? WHERE id = ?');
        try{
            $req->execute(array($commentaire, $idCommentaire));
        }catch(Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return false;
        }
        return true;
    }

    /**
     * Supprime un commentaire
     * @param $idCommentaire
     * @return bool
     */
    public function supprimerCommentaire($idCommentaire)
    {
        $req = $this->bdd->prepare('DELETE FROM Commentaire WHERE id = ?');
        try{
            $req->execute(array($idCommentaire));
        }catch(Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return false;
        }
        return true;
    }
}