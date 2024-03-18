<?php

namespace App\Imports;

use App\Models\Checkinout;
use Maatwebsite\Excel\Concerns\ToModel;

class CheckInOutImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $checkInOutRecords = [];

        // Create "Tap in" record
        $checkInOutRecords[] = new Checkinout([
            'date' => $row['Tanggal'],
            'user_id' => $row['Payroll'],
            'time' => $row['Tap in'],
        ]);

        // Create "Tap out" record
        $checkInOutRecords[] = new Checkinout([
            'date' => $row['Tanggal'],
            'user_id' => $row['Payroll'],
            'time' => $row['Tap out'],
        ]);

        return $checkInOutRecords;
    }
}
