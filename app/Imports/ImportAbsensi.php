<?php

namespace App\Imports;

use App\Imports\Absen\Kelompok1;
use App\Imports\Absen\Kelompok2;
use App\Imports\Absen\Kelompok3;
use App\Imports\Absen\Kelompok4;
use App\Imports\Absen\Kelompok5;
use App\Imports\Absen\Kelompok6;
use App\Imports\Absen\Kelompok7;
use App\Imports\Absen\Kelompok8;
use App\Imports\Absen\Kelompok9;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class ImportAbsensi implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            0 => new Kelompok1(),
            1 => new Kelompok2(),
            2 => new Kelompok3(),
            3 => new Kelompok4(),
            4 => new Kelompok5(),
            5 => new Kelompok6(),
            6 => new Kelompok7(),
            7 => new Kelompok8(),
            8 => new Kelompok9(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }
}
