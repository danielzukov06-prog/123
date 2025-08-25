<?php
session_start();

// Andmebaas
const DB_HOST = '127.0.0.1';
const DB_NAME = 'spordisaal';
const DB_USER = 'root';
const DB_PASS = '';

const APP_NAME = 'Treeningud';
const BASE_URL = '/spordisaal/public';

try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch(PDOException $e){
    die("Andmebaasiga ühendamine ebaõnnestus: ".$e->getMessage());
}
?>
