
<?php

abstract class AlatCamping
{
    protected string $nama;

    private int $hargaPerHari;
    private int $stok;

    public function __construct($nama, $hargaPerHari, $stok)
    {
        $this->nama = $nama;
        $this->hargaPerHari = $hargaPerHari;
        $this->stok = $stok;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getHargaPerHari()
    {
        return $this->hargaPerHari;
    }

    public function getStok()
    {
        return $this->stok;
    }

    public function kurangiStok($jumlah)
    {
        if ($jumlah <= 0 || $jumlah > $this->stok) {
            return false;
        }

        $this->stok -= $jumlah;
        return true;
    }

    public function tampilkanInfo()
    {
        echo $this->nama . " | Rp" .
            number_format($this->hargaPerHari, 0, ',', '.') .
            " / hari | Stok: " . $this->stok . " | " .
            $this->getJenis();
    }

    abstract public function getJenis();
}
