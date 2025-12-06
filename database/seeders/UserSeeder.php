<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $adminExists = User::role('admin')->exists();

      if (empty($adminExists)) {

        $admin = new User();
        $admin->name = 'Super Admin';
        $admin->email = 'admin@gmail.com';
        $admin->password = Hash::make('123456');
        $admin->email_verified_at = now();
        $admin->save();

        $admin->assignRole('admin');

        $this->command->info('Admin user created: admin@gmail.com / password');
      } else {
        $this->command->warn('Admin user already exists — skipping admin creation.');
      }


      // -------------------------------------------------
      // 2. IF ENVIRONMENT IS NOT PRODUCTION — ASK INPUT
      // -------------------------------------------------
      if (!app()->environment('production')) {

        $count = (int) $this->command->ask(
          'How many test users do you want to create?',
          0 // default value
        );

        if ($count > 0) {
          for ($i = 1; $i <= $count; $i++) {

            $user = new User();
            $user->name = "Test User {$i}";
            $user->email = "user{$i}@example.com";
            $user->password = Hash::make('123456');
            $user->email_verified_at = now();
            $user->save();

            $user->assignRole('user');
          }

          $this->command->info("{$count} test users created successfully!");
        }
      } else {
        $this->command->warn("Production environment detected — skipping test user creation.");
      }
    }
}
