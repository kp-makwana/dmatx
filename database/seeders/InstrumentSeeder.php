<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
  {
    ini_set('memory_limit', '1024M');
    set_time_limit(0);

    $url = 'https://margincalculator.angelbroking.com/OpenAPI_File/files/OpenAPIScripMaster.json';
    $response = Http::timeout(300)->get($url);

    if (!$response->successful()) {
      $this->command->error('Failed to fetch JSON file');
      return;
    }

    $data = $response->json();

    DB::disableQueryLog();

    collect($data)->chunk(5000)->each(function ($chunk, $index) {

      $symbols = collect($chunk)
        ->pluck('symbol')
        ->filter()
        ->unique()
        ->values();

      $existingSymbols = DB::table('instruments')
        ->whereIn('symbol', $symbols)
        ->pluck('symbol')
        ->flip();

      $rows = [];

      foreach ($chunk as $item) {
        if (! in_array($item['exch_seg'], ['NSE', 'BSE'])) {
          continue;
        }

        if (isset($existingSymbols[$item['symbol']])) {
          continue;
        }

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

      if (! empty($rows)) {
        DB::table('instruments')->insert($rows);
      }
    });
  }
}
