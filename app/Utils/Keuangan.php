<?php

namespace App\Utils;

use App\Models\Kecamatan;
use App\Models\Rekening;
use DB;
use Session;

class Keuangan
{
    public static function bulatkan($angka)
    {
        $angka = round($angka);

        $kec = Kecamatan::where('id', Session::get('lokasi'))->first();
        $pembulatan    = number_format($kec->pembulatan, 0, '', '');
        $ratusan = substr($angka, -3);
        $nilai_tengah = $pembulatan / 2;

        if ($ratusan < $nilai_tengah) {
            $akhir = $angka - $ratusan;
        } else {
            $akhir = $angka + ($pembulatan - $ratusan);
        }
        return $akhir;
    }

    public static function pembulatan($angka, $pembulatan = null, $dump = false)
    {
        $angka = round($angka);

        if ($pembulatan == null) {
            $kec = Kecamatan::where('id', Session::get('lokasi'))->first();
            $pembulatan    = (string) $kec->pembulatan;
        }

        $sistem = 'auto';
        if (self::startWith($pembulatan, '+')) {
            $sistem = 'keatas';
            $pembulatan = intval($pembulatan);
        }

        if (self::startWith($pembulatan, '-')) {
            $sistem = 'kebawah';
            $pembulatan = intval($pembulatan * -1);
        }

        $ratusan = substr($angka, -strlen($pembulatan / 2));
        $nilai_tengah = $pembulatan / 2;

        $akhir = $angka;
        if ($ratusan > 0) {
            if ($sistem == 'keatas') {
                $akhir = $angka + ($pembulatan - $ratusan);
            }

            if ($sistem == 'kebawah') {
                $akhir = $angka - $ratusan;
            }

            if ($sistem == 'auto') {
                if ($ratusan <= $nilai_tengah) {
                    $akhir = $angka - $ratusan;
                } else {
                    $akhir = $angka + ($pembulatan - $ratusan);
                }
            }
        }

        return $akhir;
    }

    public static function startWith($string, $startString)
    {
        $string = (string) $string;
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return ucwords($hasil);
    }

    public function Saldo($tgl_kondisi, $kode_akun)
    {
        $thn_kondisi = explode('-', $tgl_kondisi)[0];
        $awal_tahun = $thn_kondisi . '-01-01';
        $thn_lalu = $thn_kondisi - 1;

        $rekening = Rekening::select(
            DB::raw("SUM(tb$thn_lalu) as debit"),
            DB::raw("SUM(tbk$thn_lalu) as kredit"),
            DB::raw('(SELECT sum(jumlah) as dbt FROM 
            transaksi_' . Session::get('lokasi') . ' as td WHERE 
            td.rekening_debit=rekening_' . Session::get('lokasi') . '.kode_akun AND 
            td.tgl_transaksi BETWEEN "' . $awal_tahun . '" AND "' . $tgl_kondisi . '"
            ) as saldo_debit'),
            DB::raw('(SELECT sum(jumlah) as dbt FROM 
            transaksi_' . Session::get('lokasi') . ' as td WHERE 
            td.rekening_kredit=rekening_' . Session::get('lokasi') . '.kode_akun AND 
            td.tgl_transaksi BETWEEN "' . $awal_tahun . '" AND "' . $tgl_kondisi . '"
            ) as saldo_kredit'),
            'kode_akun'
        )
            ->groupBy(DB::raw("kode_akun", "jenis_mutasi"))->where('kode_akun', $kode_akun)->first();

        $lev1 = explode('.', $kode_akun)[0];
        $jenis_mutasi = 'kredit';
        if ($lev1 == '1' || $lev1 == '5') $jenis_mutasi = 'debet';

        if (strtolower($jenis_mutasi) == 'debet') {
            $saldo = ($rekening->debit - $rekening->kredit) + $rekening->saldo_debit - $rekening->saldo_kredit;
        } elseif (strtolower($jenis_mutasi) == 'kredit') {
            $saldo = ($rekening->kredit - $rekening->debit) + $rekening->saldo_kredit - $rekening->saldo_debit;
        }

        return $saldo;
    }

    public function romawi(int $angka)
    {
        if ($angka < 1) {
            return '';
        }

        $angka = intval($angka);
        $result = '';

        $lookup = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );

        foreach ($lookup as $roman => $value) {
            $matches = intval($angka / $value);
            $result .= str_repeat($roman, $matches);
            $angka = $angka % $value;
        }

        return $result;
    }
}
