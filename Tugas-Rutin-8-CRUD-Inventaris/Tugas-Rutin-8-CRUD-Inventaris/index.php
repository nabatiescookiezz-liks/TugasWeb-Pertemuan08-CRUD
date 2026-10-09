<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

$pdo = Database::getInstance()->getConnection();

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];

if ($search !== '') {
    $where = "WHERE p.nama_produk LIKE :nama
              OR p.kode_produk LIKE :kode
              OR c.nama_kategori LIKE :kategori
              OR s.nama_supplier LIKE :supplier";
    $keyword = "%{$search}%";
$params = [
    'nama' => $keyword,
    'kode' => $keyword,
    'kategori' => $keyword,
    'supplier' => $keyword,

];

}

$countStmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM produk p
     INNER JOIN kategori c ON c.id = p.kategori_id
     INNER JOIN supplier s ON s.id = p.supplier_id
     {$where}"
);
$countStmt->execute($params);
$totalRows = (int) $countStmt->fetchColumn();

$totalPages = max(1, (int) ceil($totalRows / $perPage));

$listStmt = $pdo->prepare(
    "SELECT
        p.id,
        p.kode_produk,
        p.nama_produk,
        p.stok,
        p.harga,
        p.created_at,
        c.nama_kategori,
        s.nama_supplier
     FROM produk p
     INNER JOIN kategori c ON c.id = p.kategori_id
     INNER JOIN supplier s ON s.id = p.supplier_id
     {$where}
     ORDER BY p.id DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $value) {
    $listStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$products = $listStmt->fetchAll();

$summaryStmt = $pdo->query(
    "SELECT
        COUNT(*) AS total_produk,
        COALESCE(SUM(stok), 0) AS total_stok,
        COALESCE(SUM(stok * harga), 0) AS nilai_inventaris
     FROM produk"
);
$summary = $summaryStmt->fetch();

$pageTitle = 'Daftar Produk';
require __DIR__ . '/partials/header.php';
?>

<section class="page-heading">
    <div>
        <div class="eyebrow">Tugas Rutin 8</div>
        <h1>CRUD Inventaris Barang</h1>
        <p>Daftar produk ditampilkan dengan relasi kategori dan supplier menggunakan JOIN.</p>
    </div>
    <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
</section>

<section class="stats">
    <div class="stat-card">
        <span>Total Produk</span>
        <strong><?= e((string) $summary['total_produk']) ?></strong>
    </div>
    <div class="stat-card">
        <span>Total Stok</span>
        <strong><?= e((string) $summary['total_stok']) ?></strong>
    </div>
    <div class="stat-card">
        <span>Nilai Inventaris</span>
        <strong>Rp <?= number_format((float) $summary['nilai_inventaris'], 0, ',', '.') ?></strong>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <h2>Data Produk</h2>
        <p><?= $search !== '' ? 'Hasil pencarian untuk: ' . e($search) : 'Seluruh data produk inventaris.' ?></p>
    </div>

    <form class="filter-bar" method="get" action="index.php">
        <input
            class="input search-input"
            type="search"
            name="search"
            value="<?= e($search) ?>"
            placeholder="Cari nama, kode, kategori, supplier..."
        >
        <button class="btn btn-secondary" type="submit">Cari</button>
        <?php if ($search !== ''): ?>
            <a class="btn btn-secondary" href="index.php">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (!$products): ?>
        <div class="empty-state">
            <strong>Data tidak ditemukan.</strong>
            Coba gunakan kata kunci lain atau tambahkan produk baru.
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="product-name"><?= e($product['nama_produk']) ?></div>
                            <div class="product-code"><?= e($product['kode_produk']) ?></div>
                        </td>
                        <td><span class="badge"><?= e($product['nama_kategori']) ?></span></td>
                        <td><?= e($product['nama_supplier']) ?></td>
                        <td><?= e((string) $product['stok']) ?> unit</td>
                        <td>Rp <?= number_format((float) $product['harga'], 0, ',', '.') ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="edit.php?id=<?= (int) $product['id'] ?>">Edit</a>
                                <form method="post" action="delete.php" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">‹</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">›</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
