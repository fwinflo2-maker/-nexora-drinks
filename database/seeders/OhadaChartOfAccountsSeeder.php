<?php

namespace Database\Seeders;

use App\Domain\Accounting\Models\LedgerAccount;
use App\Enums\LedgerAccountType;
use Illuminate\Database\Seeder;

class OhadaChartOfAccountsSeeder extends Seeder
{
    /** @var array<int, array{code: string, name: string, type: LedgerAccountType, sort_order: int}> */
    private array $accounts = [
        ['code' => '31',  'name' => 'Stocks marchandises',         'type' => LedgerAccountType::Asset,     'sort_order' => 10],
        ['code' => '411', 'name' => 'Clients',                      'type' => LedgerAccountType::Asset,     'sort_order' => 20],
        ['code' => '413', 'name' => 'Clients - Effets à recevoir',  'type' => LedgerAccountType::Asset,     'sort_order' => 30],
        ['code' => '471', 'name' => 'Compte liaison/attente',       'type' => LedgerAccountType::Asset,     'sort_order' => 40],
        ['code' => '571', 'name' => 'Caisse',                       'type' => LedgerAccountType::Asset,     'sort_order' => 50],
        ['code' => '572', 'name' => 'Banque',                       'type' => LedgerAccountType::Asset,     'sort_order' => 60],
        ['code' => '573', 'name' => 'Mobile Money',                 'type' => LedgerAccountType::Asset,     'sort_order' => 70],
        ['code' => '101', 'name' => 'Capital',                      'type' => LedgerAccountType::Equity,    'sort_order' => 80],
        ['code' => '401', 'name' => 'Fournisseurs',                 'type' => LedgerAccountType::Liability, 'sort_order' => 90],
        ['code' => '701', 'name' => 'Ventes marchandises',          'type' => LedgerAccountType::Revenue,   'sort_order' => 100],
        ['code' => '706', 'name' => 'Prestations de services',      'type' => LedgerAccountType::Revenue,   'sort_order' => 110],
        ['code' => '601', 'name' => 'Achats marchandises',          'type' => LedgerAccountType::Expense,   'sort_order' => 120],
        ['code' => '6',   'name' => 'Charges opérationnelles',      'type' => LedgerAccountType::Expense,   'sort_order' => 130],
        ['code' => '624', 'name' => 'Transports et carburant',      'type' => LedgerAccountType::Expense,   'sort_order' => 140],
        ['code' => '641', 'name' => 'Salaires et charges sociales', 'type' => LedgerAccountType::Expense,   'sort_order' => 150],
    ];

    public function seedForTeam(int $teamId): void
    {
        foreach ($this->accounts as $account) {
            LedgerAccount::withoutGlobalScopes()->firstOrCreate(
                ['team_id' => $teamId, 'code' => $account['code']],
                [
                    'name' => $account['name'],
                    'account_type' => $account['type'],
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => $account['sort_order'],
                ]
            );
        }
    }

    public function run(): void
    {
        $this->seedForTeam(1);
    }
}
