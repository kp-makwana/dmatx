<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $json = file_get_contents(public_path('storage/OpenAPIScripMaster.json'));

      $data = json_decode($json, true);

      // Insert in chunks to avoid memory overload
      collect($data)->chunk(5000)->each(function ($chunk) {
        $rows = [];

        foreach ($chunk as $item) {
          $rows[] = [
            'token'          => $item['token'] ?? null,
            'symbol'         => $item['symbol'] ?? null,
            'name'           => $item['name'] ?? null,
            'expiry'         => $item['expiry'] ?? null,
            'strike'         => $item['strike'] ?? null,
            'lotsize'        => $item['lotsize'] ?? null,
            'instrumenttype' => $item['instrumenttype'] ?? null,
            'exch_seg'       => $item['exch_seg'],
            'tick_size'      => $item['tick_size'] ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
          ];
        }

        DB::table('instruments')->insert($rows);
      });
    }
}
