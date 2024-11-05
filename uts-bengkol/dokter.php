<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Create
if(isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    
    $query = "INSERT INTO dokter (nama, alamat, no_hp) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sss", $nama, $alamat, $no_hp);
    
    if($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='index.php?page=dokter.php';</script>";
    }
}

// Update
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    
    $query = "UPDATE dokter SET nama=?, alamat=?, no_hp=? WHERE id=?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssi", $nama, $alamat, $no_hp, $id);
    
    if($stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='index.php?page=dokter.php';</script>";
    }
}

// Delete
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $query = "DELETE FROM dokter WHERE id=?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='index.php?page=dokter.php';</script>";
    }
}

// Read
$query = "SELECT * FROM dokter";
$result = mysqli_query($koneksi, $query);
?>

<h2>Data Dokter</h2>

<!-- Form Tambah/Edit -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Form Dokter</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php
            if(isset($_GET['edit'])) {
                $id = $_GET['edit'];
                $edit_query = "SELECT * FROM dokter WHERE id=?";
                $stmt = $koneksi->prepare($edit_query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $edit_data = $stmt->get_result()->fetch_assoc();
            ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php } ?>
            
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" value="<?= isset($edit_data) ? $edit_data['nama'] : '' ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" required><?= isset($edit_data) ? $edit_data['alamat'] : '' ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">No HP</label>
                <input type="text" class="form-control" name="no_hp" value="<?= isset($edit_data) ? $edit_data['no_hp'] : '' ?>" required>
            </div>
            
            <?php if(isset($_GET['edit'])): ?>
                <button type="submit" name="update" class="btn btn-warning">Update</button>
            <?php else: ?>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header">
        <h5>Daftar Dokter</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".$row['nama']."</td>";
                    echo "<td>".$row['alamat']."</td>";
                    echo "<td>".$row['no_hp']."</td>";
                    echo "<td>
                            <a href='index.php?page=dokter.php&edit=".$row['id']."' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='index.php?page=dokter.php&hapus=".$row['id']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>