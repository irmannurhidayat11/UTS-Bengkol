<?php
// Memulai sesi dan memasukkan koneksi database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Ambil data pasien
$pasien_query = "SELECT * FROM pasien";
$pasien_result = $koneksi->query($pasien_query);

// Ambil data dokter
$dokter_query = "SELECT * FROM dokter";
$dokter_result = $koneksi->query($dokter_query);

// Jika tombol 'Edit' diklik, ambil data pemeriksaan yang akan diedit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = "SELECT * FROM periksa WHERE id=?";
    $stmt = $koneksi->prepare($edit_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

// Tambah atau perbarui data pemeriksaan saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pasien = $_POST['id_pasien'];
    $id_dokter = $_POST['id_dokter'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $obat = $_POST['obat'];

    if (isset($_POST['tambah'])) {
        // Tambah data pemeriksaan baru
        $query = "INSERT INTO periksa (id_pasien, id_dokter, tgl_periksa, obat) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iiss", $id_pasien, $id_dokter, $tgl_periksa, $obat);
        $stmt->execute();
    } elseif (isset($_POST['update']) && isset($_POST['id'])) {
        // Perbarui data pemeriksaan yang ada
        $id = $_POST['id'];
        $query = "UPDATE periksa SET id_pasien=?, id_dokter=?, tgl_periksa=?, obat=? WHERE id=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iissi", $id_pasien, $id_dokter, $tgl_periksa, $obat, $id);
        $stmt->execute();
    }
    header("Location: index.php?page=periksa.php");
    exit;
}

// Menghapus data pemeriksaan jika opsi 'hapus' dipilih
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM periksa WHERE id=?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?page=periksa.php");
    exit;
}

// Ambil semua data pemeriksaan untuk ditampilkan di tabel
$result = $koneksi->query("SELECT periksa.*, pasien.nama AS nama_pasien, dokter.nama AS nama_dokter 
                           FROM periksa 
                           JOIN pasien ON periksa.id_pasien = pasien.id 
                           JOIN dokter ON periksa.id_dokter = dokter.id");
?>

<!-- Form Tambah/Edit Pemeriksaan -->
<form method="POST">
    <?php if (isset($edit_data)): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
    <?php endif; ?>
    
    <div class="mb-3">
        <label class="form-label">Pasien</label>
        <select name="id_pasien" class="form-control" required>
            <option value="">Pilih Pasien</option>
            <?php while ($pasien = $pasien_result->fetch_assoc()): ?>
                <option value="<?= $pasien['id'] ?>" <?= (isset($edit_data) && $edit_data['id_pasien'] == $pasien['id']) ? 'selected' : '' ?>>
                    <?= $pasien['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Dokter</label>
        <select name="id_dokter" class="form-control" required>
            <option value="">Pilih Dokter</option>
            <?php while ($dokter = $dokter_result->fetch_assoc()): ?>
                <option value="<?= $dokter['id'] ?>" <?= (isset($edit_data) && $edit_data['id_dokter'] == $dokter['id']) ? 'selected' : '' ?>>
                    <?= $dokter['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Periksa</label>
        <input type="date" name="tgl_periksa" class="form-control" value="<?= isset($edit_data) ? $edit_data['tgl_periksa'] : '' ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Obat</label>
        <textarea name="obat" class="form-control" required><?= isset($edit_data) ? $edit_data['obat'] : '' ?></textarea>
    </div>

    <button type="submit" name="<?= isset($edit_data) ? 'update' : 'tambah' ?>" class="btn btn-primary">
        <?= isset($edit_data) ? 'Update' : 'Tambah' ?>
    </button>
</form>

<!-- Tabel Data Pemeriksaan -->
<div class="card">
    <div class="card-header">
        <h5>Data Pemeriksaan</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Tanggal Periksa</th>
                    <th>Obat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_pasien'] ?></td>
                    <td><?= $row['nama_dokter'] ?></td>
                    <td><?= $row['tgl_periksa'] ?></td>
                    <td><?= $row['obat'] ?></td>
                    <td>
                        <a href="?page=periksa.php&edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?page=periksa.php&hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
