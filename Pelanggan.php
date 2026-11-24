
<?php

class Pelanggan
{
    private string $nama;
    private string $nomorTelepon;

    public function __construct($nama, $nomorTelepon)
    {
        $this->nama = $nama;
        $this->nomorTelepon = $nomorTelepon;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getNomorTelepon()
    {
        return $this->nomorTelepon;
    }
}
