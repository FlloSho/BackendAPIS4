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
     * Ajoute un joueur titulaire à la table 'participe'.
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function ajouterTitulaire($idJoueur, $idMatch, $poste) {
        try {
            $req = $this->bdd->prepare('INSERT INTO Participe (id, id_1, poste, titulaire) VALUES (?, ?, ?, 1)');
            return $req->execute(array($idJoueur, $idMatch, $poste));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Ajoute un joueur remplaçant à la table 'participe'.
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function ajouterRemplacant($idJoueur, $idMatch, $poste) {
        try {
            $req = $this->bdd->prepare('INSERT INTO Participe (id, id_1, poste, titulaire) VALUES (?, ?, ?, 0)');
            return $req->execute(array($idJoueur, $idMatch, $poste));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère les joueurs titulaires d'un match.
     * @param $idMatch
     * @return array|false
     */
    public function getTitulaires($idMatch) {
        try {
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
        return $req->fetchAll();
    }

    /**
     * Récupère les joueurs remplaçants d'un match.
     * @param $idMatch
     * @return array|false
     */
    public function getRemplacants($idMatch) {
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
        return $req->fetchAll();
    }

    /**
     * retire une participation d'un joueur à un match donné.
     * @param $idJoueur
     * @param $idMatch
     * @return bool
     */
    public function retirerParticipation($idJoueur, $idMatch){
        try {
            $req = $this->bdd->prepare('DELETE FROM Participe WHERE id = ? AND id_1 = ?');
            return $req->execute(array($idJoueur, $idMatch));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Modifie le poste d'un joueur dans un match.
     * @param $idJoueur
     * @param $idMatch
     * @param $poste
     * @return bool
     */
    public function modifierPoste($idJoueur, $idMatch, $poste) {
        try {
            $req = $this->bdd->prepare('
            UPDATE Participe
            SET poste = ?
            WHERE id = ? AND id_1 = ?
        ');
            $req->execute(array($poste, $idJoueur, $idMatch));
        } catch (PDOException $e) {
            echo 'Erreur SQL : ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Récupère les matchs d'un joueur.
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
     * Récupère le poste le plus joué d'un joueur.
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
     * Récupère la note moyenne d'un joueur.
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
     * Récupère le nombre de matchs gagnés par un joueur.
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
     * Récupère le nombre de matchs perdus par un joueur.
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