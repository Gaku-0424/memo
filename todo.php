<?php

$host = '127.0.0.1';
$port = 3306;
$dbname = 'todo';
$username = 'root';
$password = 'root';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $rows = $pdo->query('SELECT * FROM task ORDER BY id DESC')->fetchAll();




    foreach ($rows as $row) {
        $safeTitle = htmlspecialchars($row['title']);
        $class = $row['is_done'] ? 'done' : '';
    }
} catch (PDOException $e) {
    echo 'データベース接続失敗: ' . $e->getMessage();
    exit;
}

