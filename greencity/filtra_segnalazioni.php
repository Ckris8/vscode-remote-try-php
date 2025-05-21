
<?php
require_once("../db/database.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$motivo = $_GET['motivo'] ?? '';
$dataDa = $_GET['dataDa'] ?? '';

$query = "SELECT data, ora, motivo, note, latitudine, longitudine FROM segnalazioni WHERE 1=1";
$params = [];

if ($motivo && $motivo !== 'tutte') {
    $query .= " AND motivo = ?";
    $params[] = $motivo;
}
if ($dataDa) {
    $query .= " AND data >= ?";
    $params[] = $dataDa;
}

$query .= " ORDER BY data DESC, ora DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>