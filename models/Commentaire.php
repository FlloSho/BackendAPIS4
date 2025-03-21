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
        // On regarde si l'id existe
        $requete = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id_1 = ?;');
        $requete->execute(array($idJoueur));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        $req = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id_1 = ?');
        $req->execute(array($idJoueur));
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Affiche un commentaire en fonction de son id
     * @param $idCommentaire
     * @return mixed
     */
    public function afficherUnCommentaire($idCommentaire)
    {
        // On regarde si l'id existe
        $requete = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id = ?;');
        $requete->execute(array($idCommentaire));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        $req = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id = ?');
        $req->execute(array($idCommentaire));
        return $req->fetchAll((PDO::FETCH_ASSOC));
    }

    /**
     * Ajoute un commentaire à un joueur
     * @param $data
     * @return bool
     */
    public function ajouterCommentaire($data)
        /// $commentaire, $idJoueur
    {
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id_1 = ?;');
        $requete->execute(array($data['idJoueur']));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        $req = $this->bdd->prepare('INSERT INTO Commentaire (commentaire, id_1) VALUES (?, ?)');
        try{
            $req->execute(array($data['commentaire'], $data['idJoueur']));
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
    public function modifierCommentaire($data, $idCommentaire)
    {
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id = ?;');
        $requete->execute(array($idCommentaire));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        $req = $this->bdd->prepare('UPDATE Commentaire SET commentaire = ? WHERE id = ?');
        try{
            $req->execute(array($data['commentaire'], $idCommentaire));
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
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Commentaire WHERE id = ?;');
        $requete->execute(array($idCommentaire));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        $req = $this->bdd->prepare('DELETE FROM Commentaire WHERE id = ?');
        try{
            $req->execute(array($idCommentaire));
        }catch(Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return 'non';
        }
        return 'ok';
    }
}