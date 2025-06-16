<?php
class UserModel {
    private $bdd; // Utilisation de la variable en français 'bdd' pour la connexion à la base de données

    public function __construct() {
        try {
            $this->bdd = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function findUserByEmail($email) {
        try {
            // D'abord, chercher dans la table utilisateur
            $requete = "SELECT id, nom, mdp AS mot_de_passe, mail AS email, admin AS est_admin, token FROM utilisateur WHERE mail = :email";
            
            $stmt = $this->bdd->prepare($requete);
            $stmt->execute(['email' => $email]);
            
            error_log("Recherche de l'utilisateur pour l'email: " . $email);
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultat) {
                error_log("Utilisateur trouvé avec l'email: " . $resultat['email']);
                return $resultat;
            }
            
            error_log("Aucun utilisateur trouvé pour l'email: " . $email);
            return false;
        } catch (PDOException $e) {
            error_log("Erreur dans findUserByEmail: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email) {
        try {
            $requete = "SELECT COUNT(*) FROM utilisateur WHERE mail = :email";

            $stmt = $this->bdd->prepare($requete);
            $stmt->execute(['email' => $email]);
            
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification de l'email");
        }
    }

    public function createUser($data) {
        try {
            $requete = "INSERT INTO utilisateur 
                        (nom, mdp, mail, admin, token) 
                        VALUES (:nom, :mdp, :mail, :admin, :token)";
            
            $stmt = $this->bdd->prepare($requete);
            $stmt->execute([
                'nom' => $data['nom'],
                'mdp' => password_hash($data['password'], PASSWORD_DEFAULT), // Utilisation de 'password' comme dans votre original
                'mail' => $data['email'],
                'admin' => $data['admin'] ?? 0, // Par défaut, non admin si non spécifié
                'token' => $data['token'] ?? null // Token optionnel
            ]);

            return $this->bdd->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage() . "\nRequête: " . $requete);
            throw new Exception("Erreur lors de la création du compte: " . $e->getMessage());
        }
    }

    public function getUserData($email) {
        try {
            $requete = "
                SELECT id, nom, mail AS email, admin AS est_admin, token 
                FROM utilisateur 
                WHERE mail = :email
            ";

            $stmt = $this->bdd->prepare($requete);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            return $utilisateur ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des données utilisateur: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserData($currentEmail, $prenom, $nom, $newEmail, $ville) {
        // La table 'utilisateur' n'a pas les champs 'prenom', 'ville'.
        // Seuls 'nom' et 'mail' sont disponibles pour la mise à jour des données principales.
        // Si ces champs sont nécessaires, ils doivent être ajoutés à la table 'utilisateur'.
        try {
            $requete = "
                UPDATE utilisateur 
                SET nom = :nom, mail = :newEmail 
                WHERE mail = :currentEmail;
            ";

            $stmt = $this->bdd->prepare($requete);
            return $stmt->execute([
                // 'prenom' => $prenom, // Ce champ n'existe pas dans la table utilisateur
                'nom' => $nom,
                'newEmail' => $newEmail,
                // 'ville' => $ville, // Ce champ n'existe pas dans la table utilisateur
                'currentEmail' => $currentEmail
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour des données utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($email, $pseudo, $bio, $profilePhoto) {
        // La table 'utilisateur' n'a pas les champs 'pseudo', 'bio', 'photo_profil'.
        // Si ces champs sont nécessaires, ils doivent être ajoutés à la table 'utilisateur'.
        // Pour l'instant, cette fonction ne peut mettre à jour que le 'token' si désiré.
        try {
            $requete = "
                UPDATE utilisateur 
                SET token = :token 
                WHERE mail = :email;
            ";

            $stmt = $this->bdd->prepare($requete);
            return $stmt->execute([
                // 'pseudo' => $pseudo, // Ce champ n'existe pas dans la table utilisateur
                // 'bio' => $bio,       // Ce champ n'existe pas dans la table utilisateur
                // 'profilePhoto' => $profilePhoto, // Ce champ n'existe pas dans la table utilisateur
                'token' => null, // Vous pouvez passer un token spécifique si vous en avez un pour le profil
                'email' => $email
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil: " . $e->getMessage());
            return false;
        }
    }

    public function getUserDataByToken($sessionToken) {
        try {
            $requete = "
                SELECT id, nom, mail AS email, admin AS est_admin, token 
                FROM utilisateur 
                WHERE token = :sessionToken
            ";

            $stmt = $this->bdd->prepare($requete);
            $stmt->bindParam(':sessionToken', $sessionToken);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des données par token: " . $e->getMessage());
            return null;
        }
    }

    public function updateUser($userData) {
        try {
            error_log("Début de updateUser avec ID: " . $userData['id']);
            error_log("Données reçues: " . print_r($userData, true));
            
            // La table 'utilisateur' est unique, pas besoin de déterminer le rôle entre plusieurs tables.
            $requete = "UPDATE utilisateur SET 
                        nom = :nom,
                        mail = :email,
                        admin = :admin"; // 'admin' est le nom de la colonne
            
            // Ajouter le token seulement s'il est présent dans les données
            if (isset($userData['token'])) {
                $requete .= ", token = :token";
            }
            
            $requete .= " WHERE id = :id";
            
            error_log("Requête SQL générée: " . $requete);

            $stmt = $this->bdd->prepare($requete);
            
            // Préparer les paramètres
            $parametres = [
                'nom' => $userData['nom'],
                'email' => $userData['email'],
                'admin' => $userData['admin'] ?? 0, // Utilisation de 'admin' pour la colonne
                'id' => $userData['id']
            ];

            // Ajouter le token aux paramètres si il existe
            if (isset($userData['token'])) {
                $parametres['token'] = $userData['token'];
            }

            error_log("Paramètres de la requête: " . print_r($parametres, true));

            $resultat = $stmt->execute($parametres);
            error_log("Résultat de l'exécution: " . ($resultat ? "succès" : "échec"));
            
            if (!$resultat) {
                $infoErreur = $stmt->errorInfo();
                error_log("Erreur PDO: " . print_r($infoErreur, true));
                throw new Exception("Erreur SQL: " . $infoErreur[2]);
            }

            return $resultat;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Erreur de base de données: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Erreur générale lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updatePasswordById($userId, $newPassword) {
        try {
            // 1. Hasher le nouveau mot de passe pour le stocker de manière sécurisée.
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // 2. Préparer la requête de mise à jour.
            $requete = "UPDATE utilisateur SET mdp = :mdp WHERE id = :id";
            $stmt = $this->bdd->prepare($requete);
            
            // 3. Exécuter la requête avec les nouvelles données.
            // La fonction execute() renvoie directement true en cas de succès ou false en cas d'échec.
            return $stmt->execute([
                'mdp' => $hashedPassword,
                'id' => $userId
            ]);

        } catch (PDOException $e) {
            // En cas d'erreur de base de données, on l'enregistre dans les logs
            // et on renvoie false pour que le contrôleur sache que ça a échoué.
            error_log("Erreur PDO dans updatePasswordById: " . $e->getMessage());
            return false;
        }
    }

    public function findUserByMail($mail) {
        try {
            $requete = "SELECT id, nom, mdp AS mot_de_passe, mail AS email, admin AS est_admin, token FROM utilisateur WHERE email = :mail";
            $stmt = $this->bdd->prepare($requete);
            $stmt->execute(['email' => $mail]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function saveResetToken($userId, $token, $expiry) {
        try {
            $requete = "INSERT INTO reset_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)
                            ON DUPLICATE KEY UPDATE token = :token, expiry = :expiry";
            $stmt = $this->bdd->prepare($requete);
            return $stmt->execute([
                'user_id' => $userId,
                'token' => $token,
                'expiry' => $expiry
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde du token : " . $e->getMessage());
            return false;
        }
    }
}