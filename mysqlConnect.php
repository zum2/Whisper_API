
<?php
// DB 接続
$host = "localhost";
$user = "wipuser";
$pass = "whisper";
$database = "WHISPER";
$response = "error";

$dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

$option = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=> false,
];

try{
    $pdo = new PDO($dsn, $user, $pass, $option); 
}catch (PDOException $e) {
	throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>