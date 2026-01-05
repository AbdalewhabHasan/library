<?php
$dotenv = file_get_contents(__DIR__ . '/../.env');
$lines = explode("\n", $dotenv);
$config = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
        [$k,$v] = explode('=', $line, 2);
        $k=trim($k); $v=trim($v);
        $config[$k]=$v;
    }
}
$host = $config['DB_HOST'] ?? '127.0.0.1';
$port = $config['DB_PORT'] ?? '3306';
${'dbname'} = $config['DB_DATABASE'] ?? 'library';
$user = $config['DB_USERNAME'] ?? 'root';
$pass = $config['DB_PASSWORD'] ?? '';
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $cols = $pdo->query("SHOW COLUMNS FROM audio_books")->fetchAll(PDO::FETCH_ASSOC);
    $count = $pdo->query("SELECT COUNT(*) as c FROM audio_books")->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT id,title,cover_image_path,cover_image,file_path,pdf_path,status,created_at FROM audio_books ORDER BY id DESC LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['columns'=>$cols,'count'=>$count['c'],'rows'=>$rows], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo "ERROR: ".$e->getMessage();
}
