<?php

namespace App\Console\Commands;

use Database\Seeders\InstrumentSeeder;
use Illuminate\Console\Command;

class InstrumentUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instrument:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instrument update';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $this->info('Seeding started...');

      $this->call('db:seed', [
        '--class' => InstrumentSeeder::class,
      ]);

      $this->info('Seeding completed successfully!');
    }
}
