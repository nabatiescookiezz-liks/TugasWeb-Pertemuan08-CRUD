<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$pageTitle = $pageTitle ?? 'InventarisKu';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | InventarisKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <a class="brand" href="index.php">
            <span class="brand-mark">I</span>
            <span>
                <strong>InventarisKu</strong>
                <small>CRUD Inventaris Barang</small>
            </span>
        </a>

        <nav class="nav">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Daftar Produk</a>
            <a href="create.php" class="nav-button">+ Tambah Produk</a>
        </nav>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
                <div><?= e($flash['message']) ?></div>
            </div>
        <?php endif; ?>
