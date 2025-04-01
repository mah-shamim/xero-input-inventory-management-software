<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected array $accounts = [
        'Asset',
        'Liability',
        'Equity',
        'Income',
        'Expense',
    ];
    public function run(): void
    {
        $user1 = User::where('email', 'test@test.com')->first();
        $user2 = User::where('email', 'test_dusk@test.com')->first();

        $this->create_accounts([$user1, $user2]);
    }

    /**
     * @param $users
     * @return void
     */
    public function create_accounts(array $users): void
    {
        foreach ($this->accounts as $account) {
            foreach ($users as $user) {
                $account_main = Account::create([
                    'name' => $account,
                    'type' => 'group',
                    'parent_id' => 0,
                    'company_id' => $user->company_id,
                ]);
                Account::create([
                    'name' => $account . '_default',
                    'type' => 'ledger',
                    'parent_id' => $account_main->id,
                    'company_id' => $user->company_id,
                ]);
            }
        }
    }
}
