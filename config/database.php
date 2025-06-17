<?php

class Database {
    private static $instance = null;
    private $connection = null;
    private $maxRetries = 3;
    private $retryDelay = 1; // secondes
    
    private function __construct() {
        $this->connect();
    }
    
      

    private function connect() {
        $attempts = 0;
        while ($attempts < $this->maxRetries) {
            try {
                // Fermer la connexion existante si elle existe
                if ($this->connection !== null) {
                    $this->connection = null;
                }
                
                $host = "romantcham.fr";
                $dbname = "Domotic_db";
                $username = "G7D";
                $password = "rgnefb";
                
                $this->connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_TIMEOUT => 5, // Timeout de 5 secondes
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    ]
                );
                
                // Vérifier si la connexion est active
                $this->connection->query('SELECT 1');
                return;
                
            } catch (PDOException $e) {
                $attempts++;
                if ($attempts >= $this->maxRetries) {
                    throw new Exception("Erreur de connexion à la base de données après {$this->maxRetries} tentatives: " . $e->getMessage());
                }
                error_log("Tentative de connexion {$attempts}/{$this->maxRetries} échouée. Nouvelle tentative dans {$this->retryDelay} secondes.");
                sleep($this->retryDelay);
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        try {
            // Vérifier si la connexion est toujours active
            if ($this->connection !== null) {
                $this->connection->query('SELECT 1');
            } else {
                $this->connect();
            }
        } catch (PDOException $e) {
            // Si la connexion est perdue, tenter de se reconnecter
            $this->connect();
        }
        return $this->connection;
    }
    
    public function closeConnection() {
        if ($this->connection !== null) {
            $this->connection = null;
        }
        self::$instance = null;
    }
    
    // Empêcher le clonage de l'instance
    private function __clone() {}
    
    // Empêcher la désérialisation de l'instance
    private function __wakeup() {}
    
    // Fermer la connexion automatiquement à la fin du script
    public function __destruct() {
        $this->closeConnection();
    }
} 