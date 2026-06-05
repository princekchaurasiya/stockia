<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed admin and superadmin users for development/testing.
     * Run: php artisan db:seed --class=UserSeeder
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->ensureAdminAccounts();
    }

    private function ensureAdminAccounts(): void
    {
        $admins = User::where('role', 'admin')->whereNull('account_id')->get();
        foreach ($admins as $admin) {
            $account = Account::create([
                'name' => $admin->name . ' Account',
                'owner_id' => $admin->id,
            ]);
            $admin->update(['account_id' => $account->id]);
        }
    }
}
