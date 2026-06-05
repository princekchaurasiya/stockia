<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
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

    public function down(): void
    {
        User::where('role', 'admin')->update(['account_id' => null]);
    }
};
