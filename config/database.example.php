<?php
// Database Configuration Example for Hearts2Hearts
// Copy this file to config/database.php or use environment variables
global $host, $username, $password, $database;
$host     = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'db_hearts2hearts';

function getDbConnection() {
    global $host, $username, $password, $database;
    static $db = null;
    if ($db === null) {
        try {
            $db = @new mysqli($host, $username, $password, $database);
            if ($db->connect_error) {
                return null;
            }
            $db->set_charset('utf8mb4');
        } catch (Throwable $e) {
            return null;
        }
    }
    return $db;
}

$conn = getDbConnection();
