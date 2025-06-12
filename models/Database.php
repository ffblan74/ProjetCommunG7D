<?php
if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $pdo;

        private function __construct() {
            $host = 'romantcham.fr';
            $dbname = 'Domotic_db';
            $user = 'admin';
            $password_db = 'AdMiN';

            try {
                $this->pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $user,
                    $password_db,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT => false // Disable persistent connections
                    )
                );
            } catch (PDOException $e) {
                throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getConnection() {
            return $this->pdo;
        }

        public function __destruct() {
            // Close the connection when the object is destroyed
            $this->pdo = null;
        }
    }
}
?>
