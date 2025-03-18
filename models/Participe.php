<?php
require '../config/db.php'; // Inclut la connexion PDO à la base de données $pdo

class Participe {
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
     * Ajoute un joueur titulaire à la table 'participe'. POST
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function ajouterTitulaire($data) {
        //$idJoueur, $idMatch, $poste

        //On regarde que la ligne ne soit pas déjà dans la table
        $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id = ? AND id_1 = ?');
        $requete->execute(array($data['idJoueur'], $data['idMatch']));
        if ($requete->fetch() !== false) {
            return 'duplicate';
        }
        // On regarde si l'id du match existe
        $requete = $this->bdd->prepare('SELECT * FROM MatchHockey WHERE id = ?;');
        $requete->execute(array($data['idMatch']));
        if ($requete->fetch() === false) {
            return 'ID non trouvé';
        }
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Joueur WHERE id = ?;');
        $requete->execute(array($data['idJoueur']));
        if ($requete->fetch() === false) {
            return 'ID non trouvé';
        }

        try {
            $req = $this->bdd->prepare('INSERT INTO Participe (id, id_1, poste, titulaire) VALUES (?, ?, ?, 1)');
            return $req->execute(array($data['idJoueur'], $data['idMatch'], $data['poste']));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Ajoute un joueur remplaçant à la table 'participe'.Joeur POST
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function ajouterRemplacant($data) {
        //$idJoueur, $idMatch, $poste

        //On regarde que la ligne ne soit pas déjà dans la table
        $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id = ? AND id_1 = ?');
        $requete->execute(array($data['idJoueur'], $data['idMatch']));
        if ($requete->fetch() !== false) {
            return 'duplicate';
        }
        // On regarde si l'id du match existe
        $requete = $this->bdd->prepare('SELECT * FROM MatchHockey WHERE id = ?;');
        $requete->execute(array($data['idMatch']));
        if ($requete->fetch() === false) {
            return 'ID non trouvé';
        }
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Joueur WHERE id = ?;');
        $requete->execute(array($data['idJoueur']));
        if ($requete->fetch() === false) {
            return 'ID non trouvé';
        }

        try {
            $req = $this->bdd->prepare('INSERT INTO Participe (id, id_1, poste, titulaire) VALUES (?, ?, ?, 0)');
            return $req->execute(array($data['idJoueur'], $data['idMatch'], $data['poste']));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère les joueurs titulaires d'un match. GET
     * @param $idMatch
     * @return array|false
     */
    public function getTitulaires($idMatch) {
        try {
            // On regarde si l'id du match existe
            $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id_1 = ?;');
            $requete->execute(array($idMatch));
            if ($requete->fetch() === false) {
                return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
            }

            $req = $this->bdd->prepare('SELECT Participe.id, nom, prenom, Participe.poste, statut 
                                        FROM Participe 
                                        JOIN Joueur ON Participe.id = Joueur.id 
                                        WHERE Participe.id_1 = ? AND Participe.titulaire = 1
                                        ORDER BY nom');
            $req->execute(array($idMatch));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les joueurs remplaçants d'un match. GET
     * @param $idMatch
     * @return array|false
     */
    public function getRemplacants($idMatch) {
        // On regarde si l'id du match existe
        $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id_1 = ?;');
        $requete->execute(array($idMatch));
        if ($requete->fetch() === false) {
            return 'ID non trouvé'; //soit il n'y a pas de commentaires associé a ce joueur ou soit l'id est incorrect
        }

        try {
            $req = $this->bdd->prepare('SELECT Participe.id, nom , prenom, Participe.poste, statut 
                                        FROM Participe 
                                        JOIN Joueur ON Participe.id = Joueur.id
                                        WHERE id_1 = ? AND titulaire = 0
                                        ORDER BY nom');
            $req->execute(array($idMatch));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * retire une participation d'un joueur à un match donné. DELETE
     * @param $idJoueur
     * @param $idMatch
     * @return bool
     */
    public function retirerParticipation($data){
        //$idJoueur, $idMatch

        // On regarde si l'id du match existe
        $requete = $this->bdd->prepare('SELECT * FROM MatchHockey WHERE id = ?;');
        $requete->execute(array($data['idMatch']));
        if ($requete->fetch() === false) {
            return 'ID match non trouvé';
        }
        // On regarde si l'id du joueur existe
        $requete = $this->bdd->prepare('SELECT * FROM Joueur WHERE id = ?;');
        $requete->execute(array($data['idJoueur']));
        if ($requete->fetch() === false) {
            return 'ID joueur non trouvé';
        }
        //Enfin on regarde si la participation existe
        $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id = ? and id_1 = ?;');
        $requete->execute(array($data['idJoueur'], $data['idMatch']));
        if ($requete->fetch() === false) {
            return 'duplicate';
        }

        try {
            $req = $this->bdd->prepare('DELETE FROM Participe WHERE id = ? AND id_1 = ?');
            return $req->execute(array($data['idJoueur'], $data['idMatch']));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Modifie le poste d'un joueur dans un match. PUT
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function modifierPoste($data) {
        //$idJoueur, $idMatch, $poste
        if(!isset($data['poste']) || !isset($data['idMatch']) || !isset($data['idJoueur'])){
            return false;
        }
        //On regarde si la participation existe
        $requete = $this->bdd->prepare('SELECT * FROM Participe WHERE id = ? and id_1 = ?;');
        $requete->execute(array($data['idJoueur'], $data['idMatch']));
        if ($requete->fetch() === false) {
            return 'existe pas';
        }

        try {
            $req = $this->bdd->prepare('
            UPDATE Participe
            SET poste = ?
            WHERE id = ? AND id_1 = ?
        ');
            $req->execute(array($data['poste'], $data['idJoueur'], $data['idMatch']));
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Récupère les matchs d'un joueur. /!\ on peut le mettre dans l'api matchs
     * @param $idJoueur
     * @return array|false
     */
    public function getMatchsParJoueur($idJoueur) {
        try {
            $req = $this->bdd->prepare('
            SELECT p.note, p.poste, p.titulaire, m.* FROM MatchHockey m
            JOIN Participe p ON m.id = p.id_1
            WHERE p.id = ?
            ORDER BY m.dateHeure DESC
        ');
            $req->execute(array($idJoueur));
            return $req->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Met à jour la note d'un joueur pour un match donné.
     * @param $idJoueur
     * @param $idMatch
     * @param $note
     * @return bool
     */
    public function mettreAJourNoteJoueur($idJoueur, $idMatch, $note) {
        try {
            $req = $this->bdd->prepare('
            UPDATE Participe
            SET note = ?
            WHERE id = ? AND id_1 = ?
        ');
            $req->execute(array($note, $idJoueur, $idMatch));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Récupère le poste le plus joué d'un joueur. /!\ on peut le mettre dans l'api stats
     * @param $idJoueur
     * @return array|false
     */
    public function getPosteLePlusJoue($idJoueur) {
        try {
            $req = $this->bdd->prepare('
            SELECT poste, COUNT(*) as count
            FROM Participe
            WHERE id = ?
            GROUP BY poste
            ORDER BY count DESC
            LIMIT 1
        ');
            $req->execute(array($idJoueur));
            return $req->fetch();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère la note moyenne d'un joueur. /!\ on peut le mettre dans l'api stats
     * @param $idJoueur
     * @return array|false
     */
    public function getNoteMoyenne($idJoueur) {
        try {
            $req = $this->bdd->prepare('
            SELECT AVG(note) as moyenne
            FROM Participe
            WHERE id = ?
        ');
            $req->execute(array($idJoueur));
            return $req->fetch();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère le nombre de matchs gagnés par un joueur. /!\ on peut le mettre dans l'api stats
     * @param $idJoueur
     * @return array|false
     */
    public function getPourcentageVictoire($idJoueur) {
        try {
            $req = $this->bdd->prepare('
            SELECT COUNT(*) as total, 
                   SUM(CASE WHEN resultat = "Victoire" THEN 1 ELSE 0 END) as gagne
            FROM Participe
            JOIN MatchHockey ON Participe.id_1 = MatchHockey.id
            WHERE Participe.id = ?
        ');
            $req->execute(array($idJoueur));
            return $req->fetch();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère le nombre de matchs perdus par un joueur. /!\ on peut le mettre dans l'api stats
     * @param $idJoueur
     * @return array|false
     */
    public function getPourcentageDefaite($idJoueur) {
        try {
            $req = $this->bdd->prepare('
            SELECT COUNT(*) as total, 
                   SUM(CASE WHEN resultat = "Défaite" THEN 1 ELSE 0 END) as perdu
            FROM Participe
            JOIN MatchHockey ON Participe.id_1 = MatchHockey.id
            WHERE Participe.id = ?
        ');
            $req->execute(array($idJoueur));
            return $req->fetch();
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

}
?>