
<?php
require_once 'classes/Tenda.php';
require_once 'classes/SleepingBag.php';
require_once 'classes/Pelanggan.php';
require_once 'classes/Penyewaan.php';

session_start();

// Data alat camping
if (!isset($_SESSION['versi']) || $_SESSION['versi'] != 2) {
    $_SESSION['alat'] = [
        new Tenda("Tenda Dome", 50000, 5, 4),
        new Tenda("Tenda Family", 85000, 3, 6),
        new SleepingBag("Sleeping Bag Polar", 20000, 10, "Polar")
    ];

    $_SESSION['versi'] = 2;
}

$pesan = "";
$struk = null;

// Tambah alat
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $jenis = $_POST['jenis'];
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $detail = trim($_POST['detail']);

    if ($nama == "" || $harga <= 0 || $stok < 0 || $detail == "") {
        $pesan = "Data tidak boleh kosong!";
    } elseif ($jenis == "Tenda") {
        $_SESSION['alat'][] = new Tenda(
            $nama, $harga, $stok, (int)$detail
        );

        $pesan = "Tenda berhasil ditambahkan!";
    } else {
        $_SESSION['alat'][] = new SleepingBag(
            $nama, $harga, $stok, $detail
        );

        $pesan = "Sleeping bag berhasil ditambahkan!";
    }
}

// Hapus alat
if (isset($_POST['hapus'])) {
    $index = (int) $_POST['index'];

    if (isset($_SESSION['alat'][$index])) {
        unset($_SESSION['alat'][$index]);
        $_SESSION['alat'] = array_values($_SESSION['alat']);

        $pesan = "Alat berhasil dihapus!";
    }
}

// Edit alat
if (isset($_POST['edit'])) {
    $index = (int) $_POST['index'];

    if (isset($_SESSION['alat'][$index])) {
        $alat = $_SESSION['alat'][$index];

        $nama = trim($_POST['nama']);
        $harga = (int) $_POST['harga'];
        $stok = (int) $_POST['stok'];

        if ($nama == "" || $harga <= 0 || $stok < 0) {
            $pesan = "Data edit tidak valid!";
        } else {
            if ($alat instanceof Tenda) {
                preg_match('/\d+/', $alat->getJenis(), $hasil);
                $kapasitas = (int) ($hasil[0] ?? 1);

                $_SESSION['alat'][$index] = new Tenda(
                    $nama, $harga, $stok, $kapasitas
                );
            } else {
                $bahan = str_replace(
                    "Sleeping bag berbahan ",
                    "",
                    $alat->getJenis()
                );

                $_SESSION['alat'][$index] = new SleepingBag(
                    $nama, $harga, $stok, $bahan
                );
            }

            $pesan = "Data berhasil diubah!";
        }
    }
}

// Transaksi penyewaan
if (isset($_POST['sewa'])) {
    $nama = trim($_POST['nama']);
    $telepon = trim($_POST['telepon']);
    $index = (int) $_POST['alat'];
    $hari = (int) $_POST['hari'];
    $jumlah = (int) $_POST['jumlah'];

    if (
        $nama == "" || $telepon == "" ||
        $hari <= 0 || $jumlah <= 0 ||
        !isset($_SESSION['alat'][$index])
    ) {
        $pesan = "Isi data penyewaan dengan benar!";
    } else {
        $alat = $_SESSION['alat'][$index];

        if ($alat->kurangiStok($jumlah)) {
            $pelanggan = new Pelanggan($nama, $telepon);

            $subtotal = $alat->getHargaPerHari() * $hari * $jumlah;

            $total = Penyewaan::hitungTotal(
                $alat->getHargaPerHari(),
                $hari,
                $jumlah
            );

            $struk = [
                'nama' => $pelanggan->getNama(),
                'telepon' => $pelanggan->getNomorTelepon(),
                'alat' => $alat->getNama(),
                'jumlah' => $jumlah,
                'hari' => $hari,
                'subtotal' => $subtotal,
                'total' => $total
            ];
        } else {
            $pesan = "Stok alat tidak mencukupi!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Penyewaan Alat Camping</title>

    <style>
        body {
            font-family: Arial;
            background: white;
            color: black;
            margin: 20px;
        }

        header {
            background: black;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .card {
            border: 1px solid black;
            padding: 15px;
            margin: 10px 0;
        }

        form {
            border: 1px solid black;
            padding: 15px;
            margin-top: 20px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
            box-sizing: border-box;
        }

        button {
            background: black;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        .hapus {
            background: white;
            color: black;
            border: 1px solid black;
        }

        .struk {
            border: 1px solid black;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<header>
    <h1>Camping Rental</h1>
    <p>Sistem Penyewaan Alat Camping</p>
</header>

<div class="container">

    <h2>Daftar Alat Camping</h2>

    <?php foreach ($_SESSION['alat'] as $i => $alat) { ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($alat->getNama()); ?></h3>

            <p>Jenis: <?php echo htmlspecialchars($alat->getJenis()); ?></p>

            <p>Harga: Rp<?php echo number_format($alat->getHargaPerHari(), 0, ',', '.'); ?></p>

            <p>Stok: <?php echo $alat->getStok(); ?></p>

            <form method="POST">
                <h3>Edit Alat</h3>

                <input type="hidden" name="index" value="<?php echo $i; ?>">

                <label>Nama Alat</label>
                <input type="text" name="nama"
                    value="<?php echo htmlspecialchars($alat->getNama()); ?>" required>

                <label>Harga</label>
                <input type="number" name="harga"
                    value="<?php echo $alat->getHargaPerHari(); ?>" min="1" required>

                <label>Stok</label>
                <input type="number" name="stok"
                    value="<?php echo $alat->getStok(); ?>" min="0" required>

                <button type="submit" name="edit">Simpan Perubahan</button>
            </form>

            <form method="POST">
                <input type="hidden" name="index" value="<?php echo $i; ?>">

                <button type="submit" name="hapus" class="hapus"
                    onclick="return confirm('Yakin ingin menghapus alat ini?')">
                    Hapus Alat
                </button>
            </form>
        </div>
    <?php } ?>

    <form method="POST">
        <h2>Tambah Alat Camping</h2>

        <label>Nama Alat</label>
        <input type="text" name="nama" required>

        <label>Jenis Alat</label>
        <select name="jenis">
            <option value="Tenda">Tenda</option>
            <option value="SleepingBag">Sleeping Bag</option>
        </select>

        <label>Harga per Hari</label>
        <input type="number" name="harga" min="1" required>

        <label>Stok</label>
        <input type="number" name="stok" min="0" required>

        <label>Kapasitas Tenda / Bahan Sleeping Bag</label>
        <input type="text" name="detail" required>

        <button type="submit" name="tambah">Tambah Alat</button>
    </form>

    <form method="POST">
        <h2>Form Penyewaan</h2>

        <label>Nama Pelanggan</label>
        <input type="text" name="nama" required>

        <label>Nomor Telepon</label>
        <input type="text" name="telepon" required>

        <label>Pilih Alat</label>
        <select name="alat" required>
            <?php foreach ($_SESSION['alat'] as $i => $alat) { ?>
                <option value="<?php echo $i; ?>"
                    <?php echo $alat->getStok() <= 0 ? 'disabled' : ''; ?>>
                    <?php echo htmlspecialchars($alat->getNama()); ?>
                </option>
            <?php } ?>
        </select>

        <label>Lama Sewa (Hari)</label>
        <input type="number" name="hari" min="1" required>

        <label>Jumlah Unit</label>
        <input type="number" name="jumlah" min="1" required>

        <button type="submit" name="sewa">Sewa Sekarang</button>
    </form>

    <?php if ($pesan != "") { ?>
        <p><?php echo htmlspecialchars($pesan); ?></p>
    <?php } ?>

    <?php if ($struk != null) { ?>
        <div class="struk">
            <h2>Struk Penyewaan</h2>

            <p>Nama: <?php echo htmlspecialchars($struk['nama']); ?></p>
            <p>Telepon: <?php echo htmlspecialchars($struk['telepon']); ?></p>
            <p>Alat: <?php echo htmlspecialchars($struk['alat']); ?></p>
            <p>Jumlah: <?php echo $struk['jumlah']; ?> unit</p>
            <p>Lama Sewa: <?php echo $struk['hari']; ?> hari</p>

            <p>Subtotal: Rp<?php echo number_format($struk['subtotal'], 0, ',', '.'); ?></p>
            <p>Biaya Admin: Rp5.000</p>

            <h3>Total: Rp<?php echo number_format($struk['total'], 0, ',', '.'); ?></h3>
        </div>
    <?php } ?>

</div>

</body>
</html>
