<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Support\PrivateStorage;

/**
 * Data demo FIKTIF. Sumber data: database/seeders/demo-data.json
 * (file yang sama dipakai untuk membuat kp_monitor.sql).
 * Password semua akun demo: "password" -> WAJIB diganti jika dipakai sungguhan.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/demo-data.json')), true, 512, JSON_THROW_ON_ERROR);
        $password = Hash::make('password');

        $disk = PrivateStorage::disk();
        if (! $disk->exists('seed/laporan-demo.pdf')) {
            $disk->put('seed/laporan-demo.pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF");
        }

        DB::transaction(function () use ($data, $password) {
            foreach ($data as $table => $rows) {
                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table($table)->insert(array_map(function (array $row) use ($table, $password) {
                        if ($table === 'users') {
                            $row['password'] = $password;
                        }
                        foreach ($row as $k => $v) {
                            if (is_array($v)) {
                                $row[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
                            }
                        }

                        return $row;
                    }, $chunk));
                }
            }
        });
    }
}
