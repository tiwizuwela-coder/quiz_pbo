
<?php

require_once __DIR__ . '/Pelanggan.php';
require_once __DIR__ . '/AlatCamping.php';

class Penyewaan
{
    private Pelanggan $pelanggan;
    private AlatCamping $alat;
    private int $lamaHari;
    private int $jumlahUnit;

    private static int $biayaAdmin = 5000;

    public function __construct(
        Pelanggan $pelanggan,
        AlatCamping $alat,
        $lamaHari,
        $jumlahUnit
    ) {
        $this->pelanggan = $pelanggan;
        $this->alat = $alat;
        $this->lamaHari = $lamaHari;
        $this->jumlahUnit = $jumlahUnit;
    }

    public static function hitungTotal(
        $harga,
        $lamaHari,
        $jumlahUnit
    ) {
        return ($harga * $lamaHari * $jumlahUnit)
            + self::$biayaAdmin;
    }

    public function tampilkanStruk()
    {
        $subtotal = $this->alat->getHargaPerHari()
            * $this->lamaHari
            * $this->jumlahUnit;

        $total = self::hitungTotal(
            $this->alat->getHargaPerHari(),
            $this->lamaHari,
            $this->jumlahUnit
        );

        echo PHP_EOL;
        echo "========== STRUK PENYEWAAN ==========" . PHP_EOL;

        echo "Nama pelanggan : "
            . $this->pelanggan->getNama() . PHP_EOL;

        echo "No. telepon    : "
            . $this->pelanggan->getNomorTelepon() . PHP_EOL;

        echo "Alat           : "
            . $this->alat->getNama() . PHP_EOL;

        echo "Jumlah         : "
            . $this->jumlahUnit . " unit" . PHP_EOL;

        echo "Lama sewa      : "
            . $this->lamaHari . " hari" . PHP_EOL;

        echo "Subtotal       : Rp"
            . number_format($subtotal, 0, ',', '.') . PHP_EOL;

        echo "Biaya admin    : Rp"
            . number_format(self::$biayaAdmin, 0, ',', '.') . PHP_EOL;

        echo "TOTAL          : Rp"
            . number_format($total, 0, ',', '.') . PHP_EOL;

        echo "=====================================" . PHP_EOL;
    }
}
