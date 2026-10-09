<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

$pdo = Database::getInstance()->getConnection();
$errors = [];

$categories = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$suppliers = $pdo->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier")->fetchAll();

if (isPost()) {
    verifyCsrf();

    $kode = trim($_POST['kode_produk'] ?? '');
    $nama = trim($_POST['nama_produk'] ?? '');
    $kategoriId = filter_var($_POST['kategori_id'] ?? null, FILTER_VALIDATE_INT);
    $supplierId = filter_var($_POST['supplier_id'] ?? null, FILTER_VALIDATE_INT);
    $stok = filter_var($_POST['stok'] ?? null, FILTER_VALIDATE_INT);
    $harga = filter_var($_POST['harga'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($kode === '' || strlen($kode) > 30) {
        $errors[] = 'Kode produk wajib diisi dan maksimal 30 karakter.';
    }

    if ($nama === '' || strlen($nama) > 100) {
        $errors[] = 'Nama produk wajib diisi dan maksimal 100 karakter.';
    }

    if ($kategoriId === false || $kategoriId === null) {
        $errors[] = 'Kategori wajib dipilih.';
    }

    if ($supplierId === false || $supplierId === null) {
        $errors[] = 'Supplier wajib dipilih.';
    }

    if ($stok === false || $stok < 0) {
        $errors[] = 'Stok harus berupa angka 0 atau lebih.';
    }

    if ($harga === false || $harga < 0) {
        $errors[] = 'Harga harus berupa angka 0 atau lebih.';
    }

    if (!$errors) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE kode_produk = :kode");
        $check->execute(['kode' => $kode]);

        if ((int) $check->fetchColumn() > 0) {
            $errors[] = 'Kode produk sudah digunakan.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, stok, harga)
                 VALUES (:kode, :nama, :kategori_id, :supplier_id, :stok, :harga)"
            );

            $stmt->execute([
                'kode' => $kode,
                'nama' => $nama,
                'kategori_id' => $kategoriId,
                'supplier_id' => $supplierId,
                'stok' => $stok,
                'harga' => $harga,
            ]);

            setFlash('success', 'Produk berhasil ditambahkan.');
            redirect('index.php');
        }
    }
}

$pageTitle = 'Tambah Produk';
require __DIR__ . '/partials/header.php';
?>

<section class="page-heading">
    <div>
        <div class="eyebrow">Form Create</div>
        <h1>Tambah Produk</h1>
        <p>Masukkan data barang baru ke dalam inventaris.</p>
    </div>
</section>

<section class="card form-card">
    <div class="card-header">
        <h2>Informasi Produk</h2>
        <p>Semua field wajib diisi.</p>
    </div>

    <div class="form-body">
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <div>
                    <strong>Data belum dapat disimpan:</strong>
                    <ul style="margin: 6px 0 0 18px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="create.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="kode_produk">Kode Produk</label>
                    <input class="input" id="kode_produk" name="kode_produk" type="text" maxlength="30" required value="<?= old('kode_produk') ?>" placeholder="Contoh: EL-001">
                    <small>Kode harus unik.</small>
                </div>

                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input class="input" id="nama_produk" name="nama_produk" type="text" maxlength="100" required value="<?= old('nama_produk') ?>" placeholder="Contoh: Keyboard Mechanical">
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select class="select" id="kategori_id" name="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= (($_POST['kategori_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                <?= e($category['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supplier_id">Supplier</label>
                    <select class="select" id="supplier_id" name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= (int) $supplier['id'] ?>" <?= (($_POST['supplier_id'] ?? '') == $supplier['id']) ? 'selected' : '' ?>>
                                <?= e($supplier['nama_supplier']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input class="input" id="stok" name="stok" type="number" min="0" required value="<?= old('stok') ?>" placeholder="0">
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input class="input" id="harga" name="harga" type="number" min="0" step="0.01" required value="<?= old('harga') ?>" placeholder="0">
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Produk</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
