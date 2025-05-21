<?php
require_once("../db/database.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $stmt = $pdo->prepare("INSERT INTO segnalazioni (data, ora, motivo, note, latitudine, longitudine) VALUES (?, ?, ?, ?, ?, ?)");
    $ok = $stmt->execute([
        $data['data'],
        $data['ora'],
        $data['motivo'],
        $data['note'],
        $data['latitudine'],
        $data['longitudine']
    ]);
    echo json_encode(['success' => $ok]);
} else {
    echo json_encode(['success' => false]);
}
?>