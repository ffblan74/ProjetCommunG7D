<?php
class UserModel {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function findUserByEmail($email) {
        try {
            // D'abord, chercher dans la table Administrateur
            $query = "SELECT id_admin as user_id, nom, mot_de_passe, 'administrateur' as role FROM Administrateur WHERE email = :email
                     UNION
                     SELECT id_organisateur as user_id, nom_organisateur as nom, mot_de_passe, 'organisateur' as role FROM Organisateur WHERE email = :email
                     UNION
                     SELECT id_participant as user_id, nom_participant as nom, mot_de_passe, 'participant' as role FROM Participants WHERE email = :email";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            
            error_log("Recherche de l'utilisateur pour l'email: " . $email);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Utilisateur trouvé avec le rôle: " . $result['role']);
                return $result;
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
            $query = "SELECT 
                        (SELECT COUNT(*) FROM Participants WHERE email = :email) +
                        (SELECT COUNT(*) FROM Organisateur WHERE email = :email) as total";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification de l'email");
        }
    }

    public function createUser($data) {
        try {
            $table = $data['role'] === 'organisateur' ? 'Organisateur' : 'Participants';
            $nomField = $data['role'] === 'organisateur' ? 'nom_organisateur' : 'nom_participant';
            
            $query = "INSERT INTO $table 
                     ($nomField, nom, prenom, email, mot_de_passe) 
                     VALUES (:nom_complet, :nom, :prenom, :email, :mot_de_passe)";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'nom_complet' => $data['nom'] . ' ' . $data['prenom'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'mot_de_passe' => $data['password']
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage() . "\nRequête: " . $query);
            throw new Exception("Erreur lors de la création du compte: " . $e->getMessage());
        }
    }

    public function getUserData($email) {
        try {
            $query = "
                SELECT prenom, nom, email 
                FROM Participants 
                WHERE email = :email
                UNION
                SELECT prenom, nom, email 
                FROM Organisateur 
                WHERE email = :email
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des données utilisateur: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserData($currentEmail, $prenom, $nom, $newEmail, $ville) {
        try {
            $query = "
                UPDATE Participants 
                SET prenom = :prenom, nom = :nom, email = :newEmail, ville = :ville 
                WHERE email = :currentEmail;

                UPDATE Organisateur 
                SET prenom = :prenom, nom = :nom, email = :newEmail, ville = :ville 
                WHERE email = :currentEmail;
            ";

            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'prenom' => $prenom,
                'nom' => $nom,
                'newEmail' => $newEmail,
                'ville' => $ville,
                'currentEmail' => $currentEmail
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour des données utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($email, $pseudo, $bio, $profilePhoto) {
        try {
            $query = "
                UPDATE Participants 
                SET pseudo = :pseudo, bio = :bio, photo_profil = :profilePhoto 
                WHERE email = :email;
                
                UPDATE Organisateur 
                SET pseudo = :pseudo, bio = :bio, photo_profil = :profilePhoto 
                WHERE email = :email;
            ";

            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'pseudo' => $pseudo,
                'bio' => $bio,
                'profilePhoto' => $profilePhoto,
                'email' => $email
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil: " . $e->getMessage());
            return false;
        }
    }

    public function getUserDataByToken($sessionToken) {
        try {
            $query = "
                SELECT prenom, nom, email 
                FROM Participants 
                WHERE session_token = :sessionToken
                UNION
                SELECT prenom, nom, email 
                FROM Organisateur 
                WHERE session_token = :sessionToken
            ";

            $stmt = $this->pdo->prepare($query);
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
            
            // Déterminer le rôle de l'utilisateur
            $query = "SELECT 'administrateur' as role FROM Administrateur WHERE id_admin = :id
                     UNION
                     SELECT 'organisateur' as role FROM Organisateur WHERE id_organisateur = :id
                     UNION
                     SELECT 'participant' as role FROM Participants WHERE id_participant = :id";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $userData['id']]);
            $roleResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Résultat de la recherche de rôle: " . print_r($roleResult, true));
            
            if (!$roleResult) {
                error_log("Utilisateur non trouvé avec l'ID: " . $userData['id']);
                throw new Exception("Utilisateur non trouvé");
            }

            $role = $roleResult['role'];
            error_log("Rôle trouvé: " . $role);
            
            // Préparer la requête en fonction du rôle
            switch ($role) {
                case 'organisateur':
                    $table = 'Organisateur';
                    $idField = 'id_organisateur';
                    $nomField = 'nom_organisateur';
                    break;
                case 'participant':
                    $table = 'Participants';
                    $idField = 'id_participant';
                    $nomField = 'nom_participant';
                    break;
                case 'administrateur':
                    $table = 'Administrateur';
                    $idField = 'id_admin';
                    $nomField = 'nom';
                    break;
                default:
                    error_log("Rôle non valide: " . $role);
                    throw new Exception("Rôle non valide");
            }

            // Construire la requête de mise à jour
            $query = "UPDATE $table SET 
                        $nomField = :nom,
                        nom = :nom,
                        prenom = :prenom,
                        email = :email";
            
            // Ajouter le téléphone seulement s'il existe dans la table
            $checkColumnQuery = "SHOW COLUMNS FROM $table LIKE 'telephone'";
            $stmt = $this->pdo->prepare($checkColumnQuery);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $query .= ", telephone = :telephone";
            }
            
            // Ajouter la photo de profil à la requête si elle existe
            if (isset($userData['photo_profil'])) {
                $query .= ", photo_profil = :photo_profil";
            }
            
            $query .= " WHERE $idField = :id";
            
            error_log("Requête SQL générée: " . $query);

            $stmt = $this->pdo->prepare($query);
            
            // Préparer les paramètres
            $params = [
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'id' => $userData['id']
            ];

            // Ajouter le téléphone aux paramètres si la colonne existe
            if (isset($userData['telephone']) && !empty($userData['telephone'])) {
                $params['telephone'] = $userData['telephone'];
            }
            
            // Ajouter la photo de profil aux paramètres si elle existe
            if (isset($userData['photo_profil'])) {
                $params['photo_profil'] = $userData['photo_profil'];
            }

            error_log("Paramètres de la requête: " . print_r($params, true));

            $result = $stmt->execute($params);
            error_log("Résultat de l'exécution: " . ($result ? "succès" : "échec"));
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Erreur PDO: " . print_r($errorInfo, true));
                throw new Exception("Erreur SQL: " . $errorInfo[2]);
            }

            return $result;
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

    public function updatePassword($userId, $currentPassword, $newPassword) {
        try {
            // Déterminer le rôle et la table de l'utilisateur
            $query = "SELECT 'administrateur' as role, id_admin as id, mot_de_passe FROM Administrateur WHERE id_admin = :id
                     UNION
                     SELECT 'organisateur' as role, id_organisateur as id, mot_de_passe FROM Organisateur WHERE id_organisateur = :id
                     UNION
                     SELECT 'participant' as role, id_participant as id, mot_de_passe FROM Participants WHERE id_participant = :id";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception("Utilisateur non trouvé");
            }

            // Vérifier l'ancien mot de passe
            if (!password_verify($currentPassword, $user['mot_de_passe'])) {
                throw new Exception("Mot de passe actuel incorrect");
            }

            // Déterminer la table à mettre à jour
            switch ($user['role']) {
                case 'organisateur':
                    $table = 'Organisateur';
                    $idField = 'id_organisateur';
                    break;
                case 'participant':
                    $table = 'Participants';
                    $idField = 'id_participant';
                    break;
                case 'administrateur':
                    $table = 'Administrateur';
                    $idField = 'id_admin';
                    break;
                default:
                    throw new Exception("Rôle non valide");
            }

            // Mettre à jour le mot de passe
            $updateQuery = "UPDATE $table SET mot_de_passe = :password WHERE $idField = :id";
            $stmt = $this->pdo->prepare($updateQuery);
            $result = $stmt->execute([
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $userId
            ]);

            if (!$result) {
                throw new Exception("Erreur lors de la mise à jour du mot de passe");
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour du mot de passe: " . $e->getMessage());
            throw new Exception("Erreur de base de données lors de la mise à jour du mot de passe");
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du mot de passe: " . $e->getMessage());
            throw $e;
        }
    }

    public function findUserByUsername($username) {
        try {
            $query = "SELECT * FROM Utilisateur WHERE nom_utilisateur = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function saveResetToken($userId, $token, $expiry) {
        try {
            $query = "INSERT INTO reset_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)
                      ON DUPLICATE KEY UPDATE token = :token, expiry = :expiry";
            $stmt = $this->pdo->prepare($query);
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
