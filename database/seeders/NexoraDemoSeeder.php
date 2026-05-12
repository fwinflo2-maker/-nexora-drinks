<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NexoraDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populate the database with realistic data for a beverage distributor in Africa.
     */
    public function run(): void
    {
        $this->command->info('Début du seeding de démonstration NEXORA...');

        // 1. Assurer la présence du Super Admin et de son équipe
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@nexora.app'],
            [
                'name' => 'Super Admin NEXORA',
                'password' => Hash::make('nexora_admin2026'),
                'nexora_role' => 'super_admin',
            ]
        );
        $superAdmin->update(['current_team_id' => null]);

        // 2. Créer le Tenant (L'entreprise cliente de démo)
        $team = Team::firstOrCreate(
            ['slug' => 'brasseries-demo'],
            [
                'name' => 'SABD - Société Africaine de Boissons',
                'is_personal' => false,
                'plan' => 'pro',
                'is_active' => true,
                'domain' => 'sabd.nexora.app',
            ]
        );

        // 3. Créer les employés (Users pour ce tenant)
        $users = [
            ['name' => 'Directeur SABD', 'email' => 'admin@sabd.cm', 'role' => 'admin'],
            ['name' => 'Manager Ops', 'email' => 'manager@sabd.cm', 'role' => 'manager'],
            ['name' => 'Chef Magasinier', 'email' => 'magasinier@sabd.cm', 'role' => 'magasinier'],
            ['name' => 'Commercial Terrain 1', 'email' => 'commercial1@sabd.cm', 'role' => 'commercial'],
            ['name' => 'Livreur Principal', 'email' => 'livreur@sabd.cm', 'role' => 'livreur'],
            ['name' => 'Caissière Centrale', 'email' => 'caisse@sabd.cm', 'role' => 'caissier'],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $user = clone $superAdmin; // just to get a User model instance quickly
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('nexora_admin2026'),
                    'current_team_id' => $team->id,
                ]
            );
            DB::table('team_members')->updateOrInsert(
                ['team_id' => $team->id, 'user_id' => $user->id],
                ['role' => $u['role'], 'created_at' => now(), 'updated_at' => now()]
            );
            $createdUsers[$u['role']] = $user->id;
        }

        // 4. Créer les types de consignes (Emballages)
        $packagings = [
            ['name' => 'Casier 65cl (Vide)', 'value' => 2000],
            ['name' => 'Casier 33cl (Vide)', 'value' => 2500],
            ['name' => 'Bouteille Verre 65cl', 'value' => 100],
            ['name' => 'Bouteille Verre 33cl', 'value' => 50],
        ];
        $pkgIds = [];
        foreach ($packagings as $pkg) {
            $pkgIds[$pkg['name']] = DB::table('packaging_types')->insertGetId([
                'team_id' => $team->id,
                'name' => $pkg['name'],
                'unit_value_xaf' => $pkg['value'],
                'created_at' => now(),
            ]);
        }

        // 5. Entrepôts
        $warehouseId = DB::table('warehouses')->insertGetId([
            'team_id' => $team->id,
            'name' => 'Dépôt Central Akwa',
            'address' => 'Zone Industrielle Bassa',
            'type' => 'main',
            'is_active' => true,
            'created_at' => now(),
        ]);

        // 6. Catégories & Produits (Boissons)
        $categories = ['Bières', 'Sodas', 'Eaux', 'Vins & Spiritueux'];
        $catIds = [];
        foreach ($categories as $cat) {
            $catIds[$cat] = DB::table('categories')->insertGetId([
                'team_id' => $team->id,
                'name' => $cat,
                'is_active' => true,
                'created_at' => now(),
            ]);
        }

        $products = [
            ['cat' => 'Bières', 'name' => 'Castel Beer 65cl', 'sku' => 'CST-65', 'is_consignable' => true, 'price' => 600, 'case' => 12],
            ['cat' => 'Bières', 'name' => '33 Export 65cl', 'sku' => '33-65', 'is_consignable' => true, 'price' => 500, 'case' => 12],
            ['cat' => 'Bières', 'name' => 'Beaufort Lager 50cl', 'sku' => 'BF-50', 'is_consignable' => true, 'price' => 650, 'case' => 20],
            ['cat' => 'Sodas', 'name' => 'Top Pamplemousse 60cl', 'sku' => 'TOP-P-60', 'is_consignable' => true, 'price' => 450, 'case' => 12],
            ['cat' => 'Sodas', 'name' => 'Coca-Cola 1.5L PET', 'sku' => 'CC-15-PET', 'is_consignable' => false, 'price' => 800, 'case' => 6],
            ['cat' => 'Eaux', 'name' => 'Supermont 1.5L', 'sku' => 'SM-15', 'is_consignable' => false, 'price' => 350, 'case' => 6],
            ['cat' => 'Vins & Spiritueux', 'name' => 'Vin Rouge Château 75cl', 'sku' => 'VIN-R-75', 'is_consignable' => false, 'price' => 3500, 'case' => 6],
        ];

        $productIds = [];
        foreach ($products as $p) {
            $pid = DB::table('products')->insertGetId([
                'team_id' => $team->id,
                'category_id' => $catIds[$p['cat']],
                'name' => $p['name'],
                'sku' => $p['sku'],
                'base_unit' => 'bouteille',
                'units_per_case' => $p['case'],
                'purchase_price' => $p['price'] * 0.7, // Marge 30%
                'sale_price' => $p['price'],
                'is_consignable' => $p['is_consignable'],
                'created_at' => now(),
            ]);
            $productIds[] = $pid;

            // Mettre du stock
            DB::table('stock_levels')->insert([
                'team_id' => $team->id,
                'product_id' => $pid,
                'warehouse_id' => $warehouseId,
                'quantity' => rand(500, 5000),
                'min_threshold' => 100,
                'created_at' => now(),
            ]);
        }

        // 7. Clients (Bars, Restaurants, Grossistes)
        $clientNames = ['Bar Le Diplomate', 'Snack Bar La Joie', 'Restaurant Chez Wou', 'Grossiste Mboppi', 'Alimentation Centrale'];
        $clientIds = [];
        foreach ($clientNames as $index => $cName) {
            $cid = DB::table('clients')->insertGetId([
                'team_id' => $team->id,
                'name' => $cName,
                'phone' => '6'.rand(70000000, 99999999),
                'address' => 'Quartier '.['Deido', 'Akwa', 'Bonamoussadi', 'Bépanda', 'Makepe'][$index],
                'zone' => 'Douala',
                'client_type' => str_contains($cName, 'Grossiste') ? 'grossiste' : 'detail',
                'credit_limit' => 500000,
                'commercial_id' => $createdUsers['commercial'],
                'created_at' => now(),
            ]);
            $clientIds[] = $cid;

            // Solde de consignes (dette emballage)
            DB::table('client_packaging_balances')->insert([
                'team_id' => $team->id,
                'client_id' => $cid,
                'packaging_type_id' => $pkgIds['Casier 65cl (Vide)'],
                'quantity_owed' => rand(10, 50),
            ]);
        }

        // 8. Véhicules
        $vehicleId = DB::table('vehicles')->insertGetId([
            'team_id' => $team->id,
            'name' => 'Camion Fuso 5T',
            'plate' => 'LT 123 AB',
            'capacity_cases' => 300,
            'driver_id' => $createdUsers['livreur'],
            'created_at' => now(),
        ]);

        // 9. Commandes et Livraisons (historique)
        $routeId = DB::table('routes')->insertGetId([
            'team_id' => $team->id,
            'name' => 'Tournée Deido - '.now()->format('d/m/Y'),
            'date' => now()->toDateString(),
            'driver_id' => $createdUsers['livreur'],
            'vehicle_id' => $vehicleId,
            'status' => 'completed',
            'created_by' => $createdUsers['manager'],
            'created_at' => now()->subDay(),
        ]);

        foreach (array_slice($clientIds, 0, 3) as $idx => $clientId) {
            $total = 0;
            // Commande
            $orderId = DB::table('orders')->insertGetId([
                'team_id' => $team->id,
                'order_number' => 'CMD-'.date('Y').'-'.str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'client_id' => $clientId,
                'channel' => 'terrain',
                'status' => 'delivered',
                'delivery_date' => now()->toDateString(),
                'warehouse_id' => $warehouseId,
                'commercial_id' => $createdUsers['commercial'],
                'created_by' => $createdUsers['commercial'],
                'created_at' => now()->subDay(),
            ]);

            // Items commande
            for ($i = 0; $i < 3; $i++) {
                $pid = $productIds[array_rand($productIds)];
                $qty = rand(5, 20); // casiers
                $price = DB::table('products')->where('id', $pid)->value('sale_price') * 12; // prix par casier approximatif

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'line_total' => $qty * $price,
                    'created_at' => now(),
                ]);
                $total += ($qty * $price);
            }

            DB::table('orders')->where('id', $orderId)->update(['subtotal' => $total, 'total' => $total]);

            // Livraison
            $deliveryId = DB::table('deliveries')->insertGetId([
                'team_id' => $team->id,
                'route_id' => $routeId,
                'order_id' => $orderId,
                'client_id' => $clientId,
                'status' => 'delivered',
                'sequence_number' => $idx + 1,
                'delivered_at' => now(),
                'created_at' => now(),
            ]);

            // Facturation
            $invId = DB::table('invoices')->insertGetId([
                'team_id' => $team->id,
                'invoice_number' => 'FAC-'.date('Y').'-'.str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'client_id' => $clientId,
                'order_id' => $orderId,
                'type' => 'invoice',
                'status' => 'paid',
                'subtotal' => $total,
                'total' => $total,
                'paid_amount' => $total,
                'due_date' => now()->addDays(15)->toDateString(),
                'created_by' => $createdUsers['caissier'],
                'created_at' => now(),
            ]);

            // Paiement (Mobile Money)
            DB::table('payments')->insert([
                'team_id' => $team->id,
                'client_id' => $clientId,
                'invoice_id' => $invId,
                'amount' => $total,
                'method' => 'orange_money',
                'mobile_money_ref' => 'OM'.rand(10000000, 99999999),
                'received_at' => now(),
                'created_by' => $createdUsers['caissier'],
                'created_at' => now(),
            ]);

            // Consignation retour (Le livreur récupère des casiers vides)
            DB::table('packaging_movements')->insert([
                'team_id' => $team->id,
                'client_id' => $clientId,
                'packaging_type_id' => $pkgIds['Casier 65cl (Vide)'],
                'movement_type' => 'in',
                'quantity' => rand(10, 30),
                'delivery_id' => $deliveryId,
                'created_by' => $createdUsers['livreur'],
                'created_at' => now(),
            ]);
        }

        $this->command->info('✅ Démo SABD (Société Africaine de Boissons) générée avec succès !');
        $this->command->info('Accès admin : admin@sabd.cm / nexora_admin2026');
    }
}
