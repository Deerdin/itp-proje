<?php
declare(strict_types=1);
final class Database
{
    private static ?PDO $connection = null;
    public static function connection(): PDO
    {
        if (self::$connection) return self::$connection;
        $driver = getenv('DB_DRIVER') ?: 'sqlite';
        if ($driver === 'mysql') {
            $dsn = 'mysql:host=' . (getenv('DB_HOST') ?: '127.0.0.1') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . (getenv('DB_NAME') ?: 'kvkk_denetci') . ';charset=utf8mb4';
            if (getenv('DB_SOCKET')) $dsn='mysql:unix_socket='.getenv('DB_SOCKET').';dbname='.(getenv('DB_NAME')?:'kvkk_denetci').';charset=utf8mb4';
            $pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: '');
        } else {
            $path = getenv('KVKK_TEST_DB') ?: ROOT . '/storage/database.sqlite';
            $pdo = new PDO('sqlite:' . $path);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA busy_timeout = 5000');
        }
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if ($driver === 'mysql') $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        self::$connection = $pdo;
        return $pdo;
    }
    public static function migrate(): void
    {
        $pdo = self::connection();
        $mysql = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
        $id = $mysql ? 'INTEGER PRIMARY KEY AUTO_INCREMENT' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
        $long = $mysql ? 'LONGTEXT' : 'TEXT';
        $suffix = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id $id, name VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL DEFAULT 'user', created_at VARCHAR(30) NOT NULL)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
            id $id, user_id INTEGER NOT NULL, title VARCHAR(150) NOT NULL, sector VARCHAR(40) NOT NULL,
            content $long NOT NULL, created_at VARCHAR(30) NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS analyses (
            id $id, document_id INTEGER NOT NULL UNIQUE, score INTEGER NOT NULL, model_version VARCHAR(40) NOT NULL,
            result_json $long NOT NULL, created_at VARCHAR(30) NOT NULL,
            FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
            id $id, analysis_id INTEGER NOT NULL, expert_id INTEGER NOT NULL, criterion VARCHAR(40) NOT NULL,
            verdict VARCHAR(20) NOT NULL, note TEXT NOT NULL, updated_at VARCHAR(30) NOT NULL,
            UNIQUE (analysis_id, expert_id, criterion),
            FOREIGN KEY (analysis_id) REFERENCES analyses(id) ON DELETE CASCADE,
            FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS guest_trials (
            token_hash VARCHAR(64) PRIMARY KEY, used_at VARCHAR(30) NOT NULL)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            email VARCHAR(190) PRIMARY KEY, attempts INTEGER NOT NULL DEFAULT 0, last_attempt INTEGER NOT NULL)".$suffix);
        $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
            rate_key VARCHAR(64) PRIMARY KEY, hits INTEGER NOT NULL, expires_at INTEGER NOT NULL)".$suffix);
    }
}
