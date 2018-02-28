<?php
namespace App\Connection;

class DatabaseConnection
{
    const CHARSET = 'utf8';

    private static $instance = null;

    private $connection = null;

    private function __clone() {}

    private function __wakeup() {}

    private function __construct()
    {
        $settings = $this->getSettings();
        $dsn = sprintf(
          '%s:host=%s;dbname=%s',
          $settings['db_type'],
          $settings['db_host'],
          $settings['db_name']
        );

        $this->connection = new \PDO($dsn, $settings['db_user'], $settings['db_password']);
        $this->connection->exec(sprintf('SET NAMES %s', self::CHARSET));
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
    
    private function getSettings()
    {
//        TODO: make from file

        $envVars = array(
          'db_type' => 'mysql',
          'db_host' => 'localhost',
          'db_name' => 'react',
          'db_user' => 'root',
          'db_password' => 'root',
        );

        return $envVars;
    }

    public function executeUpdate($query, $params)
    {
        $pdo = $this->connection;
        $pdoStmt = $pdo->prepare($query);
        $lol = $pdoStmt->execute($params);
    }

    public function executeQuery($query, $params)
    {
        $pdo = $this->connection;
        $pdoStmt = $pdo->prepare($query);
        $pdoStmt->execute($params);

        return $pdoStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
