<?php
require '../config/db.php'; // Inclut la connexion PDO à la base de données $pdo

class Joueur
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
     * Ajoute un joueur à la table 'joueur' et retourne vrai si l'ajout est validé.
     * @param $nom
     * @param $prenom
     * @param $dateNaissance
     * @param $taille
     * @param $poids
     * @param $numero
     * @return bool
     */
    public function ajouterJoueur($nom, $prenom, $dateNaissance, $taille, $poids, $numero)
    {
        $req = $this->bdd->prepare('INSERT INTO Joueur (nom, prenom, numeroLicence, dateNaissance, taille, poids, statut) VALUES (?, ?, ?, ?, ?, ?,?)');
        try{
            $req->execute(array($nom, $prenom, $numero, $dateNaissance, $taille, $poids, 'Actif'));
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
     * Récupère tous les joueurs de la table 'joueur'.
     *
     * @return array Un tableau contenant tous les joueurs.
     */
    public function tousLesJoueurs()
    {
        try {
            $req = $this->bdd->prepare('SELECT * FROM Joueur');
            $req->execute();
        } catch(Exception $e) {
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return null;
        }
        // Récupère tous les résultats et les retourne sous forme de tableau
        return $req->fetchAll();
    }

    /**
     * Récupère un joueur en fonction de son id.
     *
     * @param $id L'id du joueur à récupérer.
     * @return mixed Un tableau contenant les informations du joueur.
     */
    public function getJoueurParId($id)
    {
        try{
            $req = $this->bdd->prepare('SELECT * FROM Joueur WHERE id = ?');
            $req->execute(array($id));
        }catch (Exception $e){
            echo '<script type="text/javascript">
            window.onload = function () {
                alert("Erreur: ' . addslashes($e->getMessage()) . '");
            }
            </script>';
            return false;
        }

        return $req->fetch();
    }

    /**
     * modifie un joueur dans la table 'joueur' et retourne vrai si la modification est validée.
     * @param $id
     * @param $nom
     * @param $prenom
     * @param $dateNaissance
     * @param $taille
     * @param $poids
     * @param $numeroLicence
     * @param $statut
     * @return bool
     */
    public function modifierJoueur($id, $nom, $prenom, $dateNaissance, $taille, $poids, $numeroLicence, $statut)
    {
        try{
            $req = $this->bdd->prepare('UPDATE Joueur SET nom = ?, prenom = ?, dateNaissance = ?, taille = ?, poids = ?, numeroLicence = ?, statut = ? WHERE id = ?');
            $req->execute(array($nom, $prenom, $dateNaissance, $taille, $poids, $numeroLicence, $statut, $id));
        }catch (Exception $e){
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
     * Supprime un joueur de la table 'joueur' et retourne vrai si la suppression est validée.
     * @param $id
     * @return bool
     */
    public function supprimerJoueur($id)
    {
        try {
            // commence une transaction
            $this->bdd->beginTransaction();

            // suprime les enregistrements associés dans la table Participe
            $req = $this->bdd->prepare('DELETE FROM Participe WHERE id = ?');
            $req->execute(array($id));

            //supprime les enregistrement associés dans la table Commentaire
            $req = $this->bdd->prepare('DELETE FROM Commentaire WHERE id_1 = ?');
            $req->execute(array($id));

            // supprime le joueur de la table Joueur
            $req = $this->bdd->prepare('DELETE FROM Joueur WHERE id = ?');
            $req->execute(array($id));

            // valide la transaction
            $this->bdd->commit();
        } catch (Exception $e) {
            // annule la transaction en cas d'erreur
            $this->bdd->rollBack();
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
