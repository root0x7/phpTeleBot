<?php

namespace Root0x7\Database;

use PDO;
use PDOException;
use Exception;

class Connection
{
    private static ?PDO $instance = null;
    private static array $config = [];

    public static function configure(array $config): void
    {
        self::$config = $config;
        self::$instance = null; // Reset connection
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }
        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        $config = self::$config;
        
        if (empty($config)) {
            throw new Exception("Database configuration not set");
        }

        $driver = $config['driver'] ?? 'sqlite';
        
        try {
            switch ($driver) {
                case 'sqlite':
                    $dsn = "sqlite:" . ($config['database'] ?? 'bot.db');
                    return new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

                case 'mysql':
                    $dsn = sprintf(
                        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                        $config['host'] ?? 'localhost',
                        $config['port'] ?? 3306,
                        $config['database']
                    );
                    return new PDO($dsn, $config['username'], $config['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    ]);

                case 'pgsql':
                    $dsn = sprintf(
                        "pgsql:host=%s;port=%s;dbname=%s",
                        $config['host'] ?? 'localhost',
                        $config['port'] ?? 5432,
                        $config['database']
                    );
                    return new PDO($dsn, $config['username'], $config['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

                default:
                    throw new Exception("Unsupported database driver: {$driver}");
            }
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    public static function rollback(): bool
    {
        return self::getInstance()->rollBack();
    }
}