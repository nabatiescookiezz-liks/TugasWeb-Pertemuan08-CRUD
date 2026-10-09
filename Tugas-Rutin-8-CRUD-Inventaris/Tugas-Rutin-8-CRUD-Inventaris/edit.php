<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

$pdo = Database::getInstance()->getConnection();

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    setFlash('danger', 'ID produk tidak valid.');
    redirect('index.php');
}

$stmt = $pdo->prepare(
    "SELECT id, kode_produk, nama_produk, kategori_id, supplier_id, stok, harga
     FROM produk
     WHERE id = :id"
);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('danger', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$categories = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$suppliers = $pdo->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier")->fetchAll();
$errors = [];

if (isPost()) {
    verifyCsrf();

    $kode = trim($_POST['kode_produk'] ?? '');
    $nama = trim($_POST['nama_produk'] ?? '');
    $kategoriId = filter_var($_POST['kategori_id'] ?? null, FILTER_VALIDATE_INT);
    $supplierId = filter_var($_POST['supplier_id'] ?? null, FILTER_VALIDATE_INT);
    $stok = filter_var($_POST['stok'] ?? null, FILTER_VALIDATE_INT);
    $harga = filter_var($_POST['harga'] ?? null, FILTER_VALIDATE_FLOAT);

    $product['kode_produk'] = $kode;
    $product['nama_produk'] = $nama;
    $product['kategori_id'] = $kategoriId;
    $product['supplier_id'] = $supplierId;
    $product['stok'] = $stok;
    $product['harga'] = $harga;

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
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM produk
             WHERE kode_produk = :kode AND id <> :id"
        );
        $check->execute([
            'kode' => $kode,
            'id' => $id,
        ]);

        if ((int) $check->fetchColumn() > 0) {
            $errors[] = 'Kode produk sudah digunakan oleh produk lain.';
        } else {
            $update = $pdo->prepare(
                "UPDATE produk
                 SET kode_produk = :kode,
                     nama_produk = :nama,
                     kategori_id = :kategori_id,
                     supplier_id = :supplier_id,
                     stok = :stok,
                     harga = :harga
                 WHERE id = :id"
            );

            $update->execute([
                'kode' => $kode,
                'nama' => $nama,
                'kategori_id' => $kategoriId,
                'supplier_id' => $supplierId,
                'stok' => $stok,
                'harga' => $harga,
                'id' => $id,
            ]);

            setFlash('success', 'Produk berhasil diperbarui.');
            redirect('index.php');
        }
    }
}

$pageTitle = 'Edit Produk';
require __DIR__ . '/partials/header.php';
?>

<section class="page-heading">
    <div>
        <div class="eyebrow">Form Update</div>
        <h1>Edit Produk</h1>
        <p>Perbarui informasi produk yang dipilih. Data lama otomatis terisi pada form.</p>
    </div>
</section>

<section class="card form-card">
    <div class="card-header">
        <h2>Perbarui Informasi Produk</h2>
        <p>Periksa kembali data sebelum menyimpan perubahan.</p>
    </div>

    <div class="form-body">
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <div>
                    <strong>Data belum dapat diperbarui:</strong>
                    <ul style="margin: 6px 0 0 18px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="edit.php?id=<?= (int) $id ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="kode_produk">Kode Produk</label>
                    <input class="input" id="kode_produk" name="kode_produk" type="text" maxlength="30" required value="<?= e((string) $product['kode_produk']) ?>">
                </div>

                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input class="input" id="nama_produk" name="nama_produk" type="text" maxlength="100" required value="<?= e((string) $product['nama_produk']) ?>">
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select class="select" id="kategori_id" name="kategori_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= ((int) $product['kategori_id'] === (int) $category['id']) ? 'selected' : '' ?>>
                                <?= e($category['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supplier_id">Supplier</label>
                    <select class="select" id="supplier_id" name="supplier_id" required>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= (int) $supplier['id'] ?>" <?= ((int) $product['supplier_id'] === (int) $supplier['id']) ? 'selected' : '' ?>>
                                <?= e($supplier['nama_supplier']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input class="input" id="stok" name="stok" type="number" min="0" required value="<?= e((string) $product['stok']) ?>">
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input class="input" id="harga" name="harga" type="number" min="0" step="0.01" required value="<?= e((string) $product['harga']) ?>">
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
