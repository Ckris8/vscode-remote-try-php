
<?php
require_once("../db/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'] ?? '';
    $ora = $_POST['ora'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $note = $_POST['note'] ?? '';
    $latitudine = $_POST['latitudine'] ?? null;
    $longitudine = $_POST['longitudine'] ?? null;
    $foto = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO segnalazioni (data, ora, motivo, note, latitudine, longitudine, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data, $ora, $motivo, $note, $latitudine, $longitudine, $foto]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>