<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

if (!isPost()) {
    redirect('index.php');
}

verifyCsrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    setFlash('danger', 'ID produk tidak valid.');
    redirect('index.php');
}

$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    setFlash('success', 'Produk berhasil dihapus.');
} else {
    setFlash('danger', 'Produk tidak ditemukan atau sudah dihapus.');
}

redirect('index.php');
