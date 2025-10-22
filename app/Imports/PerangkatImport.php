<?php

namespace App\Imports;

use App\Models\Perangkat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PerangkatImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Lewat setiap baris, biasanya baris pertama adalah header
        foreach ($collection as $index => $row) {
            if ($index === 0) continue; // skip header jika ada

            $lat = null;
            $lng = null;

            // Pastikan kolom ada
            if (!empty($row[3])) { // misal kolom ke-3 adalah latitude_dan_longitude
                $coords = explode(',', $row[3]);
                $lat = trim($coords[0]) ?? null;
                $lng = trim($coords[1]) ?? null;
            }
            
            Perangkat::create([
                'hostname'  => $row[2] ?? null, // kolom hostname
                'ip_address'=> $row[0] ?? null, // kolom ip_address
                'latitude'  => $lat ?? '-0.786528',
                'longitude' => $lng ?? '100.654013',
            ]);

        }
    }
}
