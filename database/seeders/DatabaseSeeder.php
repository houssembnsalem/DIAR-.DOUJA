<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Property;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@gestionlocation.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+216 20 000 001',
            'is_active' => true,
        ]);

        // Create Assistant
        $assistant = User::create([
            'name' => 'Assistante',
            'email' => 'assistant@gestionlocation.com',
            'password' => Hash::make('password'),
            'role' => 'assistant',
            'phone' => '+216 20 000 002',
            'is_active' => true,
        ]);



        // Create Properties
        $properties = [
            [
                'name' => 'Bungalow Jasmin',
                'type' => 'bungalow',
                'description' => 'Bungalow luxueux avec vue sur la mer, terrasse privée et jardin tropical. Idéal pour les couples et petites familles cherchant tranquillité et confort.',
                'price_per_night' => 150.00,
                'capacity' => 4,
                'bedrooms' => 2,
                'surface' => '80',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'Terrasse', 'Cuisine équipée', 'TV', 'Parking']),
                'status' => 'available',
                'location' => 'Zone A - Bord de mer',
            ],
            [
                'name' => 'Bungalow Rose',
                'type' => 'bungalow',
                'description' => 'Bungalow confortable au cœur du domaine, entouré de verdure. Parfait pour un séjour familial ressourçant.',
                'price_per_night' => 120.00,
                'capacity' => 6,
                'bedrooms' => 3,
                'surface' => '95',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'BBQ', 'Jardin', 'TV', 'Lave-linge']),
                'status' => 'available',
                'location' => 'Zone B - Jardin',
            ],
            [
                'name' => 'Appartement Étoile',
                'type' => 'apartment',
                'description' => 'Appartement moderne avec vue panoramique. Entièrement équipé pour un séjour agréable. Situé au 3ème étage avec ascenseur.',
                'price_per_night' => 85.00,
                'capacity' => 4,
                'bedrooms' => 2,
                'surface' => '65',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'Balcon', 'Cuisine équipée', 'TV', 'Ascenseur']),
                'status' => 'available',
                'location' => 'Immeuble Principal - 3ème',
            ],
            [
                'name' => 'Appartement Soleil',
                'type' => 'apartment',
                'description' => 'Studio cosy idéal pour couples ou voyageurs solo. Décoré avec goût, lumineux et bien situé.',
                'price_per_night' => 60.00,
                'capacity' => 2,
                'bedrooms' => 1,
                'surface' => '35',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'TV', 'Micro-ondes', 'Réfrigérateur']),
                'status' => 'available',
                'location' => 'Immeuble Principal - 1er',
            ],
            [
                'name' => 'Chambre Deluxe 101',
                'type' => 'room',
                'description' => 'Chambre deluxe avec salle de bain privée, vue sur le jardin. Lit king-size et décoration raffinée.',
                'price_per_night' => 45.00,
                'capacity' => 2,
                'bedrooms' => 1,
                'surface' => '25',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'TV', 'Minibar', 'Sèche-cheveux']),
                'status' => 'available',
                'location' => 'Maison Principale - RDC',
            ],
            [
                'name' => 'Chambre Standard 201',
                'type' => 'room',
                'description' => 'Chambre standard confortable avec salle de bain partagée. Idéale pour les budgets serrés.',
                'price_per_night' => 30.00,
                'capacity' => 2,
                'bedrooms' => 1,
                'surface' => '18',
                'amenities' => json_encode(['WiFi', 'Climatisation', 'TV']),
                'status' => 'available',
                'location' => 'Maison Principale - 2ème',
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::create($propertyData);
        }

        // Create Clients
        $clients = [
            ['first_name' => 'Ahmed', 'last_name' => 'Ben Ali', 'email' => 'ahmed.benali@email.com', 'phone' => '+216 22 111 222', 'id_number' => 'TN12345678', 'nationality' => 'Tunisienne'],
            ['first_name' => 'Fatima', 'last_name' => 'Mansour', 'email' => 'fatima.m@email.com', 'phone' => '+216 25 333 444', 'id_number' => 'TN87654321', 'nationality' => 'Tunisienne'],
            ['first_name' => 'Pierre', 'last_name' => 'Dupont', 'email' => 'pierre.dupont@email.fr', 'phone' => '+33 6 12 34 56 78', 'id_number' => 'FR123456', 'nationality' => 'Française'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.j@email.com', 'phone' => '+44 7700 900000', 'id_number' => 'GB789012', 'nationality' => 'Britannique'],
            ['first_name' => 'Mohamed', 'last_name' => 'Trabelsi', 'email' => 'med.trabelsi@email.com', 'phone' => '+216 98 555 666', 'id_number' => 'TN11223344', 'nationality' => 'Tunisienne'],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }

        // Expense Categories
        $categories = [
            ['name' => 'Entretien & Réparations', 'color' => '#dc3545'],
            ['name' => 'Fournitures', 'color' => '#fd7e14'],
            ['name' => 'Eau & Électricité', 'color' => '#0dcaf0'],
            ['name' => 'Personnel', 'color' => '#6f42c1'],
            ['name' => 'Marketing', 'color' => '#20c997'],
            ['name' => 'Taxes & Assurances', 'color' => '#6c757d'],
            ['name' => 'Autres', 'color' => '#adb5bd'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::create($cat);
        }

        // Sample reservations
        $reservations = [
            [
                'property_id' => 1,
                'client_id' => 1,
                'check_in' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'check_out' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'guests_count' => 2,
                'price_per_night' => 150.00,
                'total_amount' => 450.00,
                'discount' => 0,
                'final_amount' => 450.00,
                'amount_paid' => 450.00,
                'payment_status' => 'paid',
                'status' => 'checked_out',
            ],
            [
                'property_id' => 3,
                'client_id' => 3,
                'check_in' => Carbon::now()->format('Y-m-d'),
                'check_out' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'guests_count' => 2,
                'price_per_night' => 85.00,
                'total_amount' => 255.00,
                'discount' => 25.00,
                'final_amount' => 230.00,
                'amount_paid' => 100.00,
                'payment_status' => 'partial',
                'status' => 'checked_in',
            ],
            [
                'property_id' => 2,
                'client_id' => 2,
                'check_in' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'check_out' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'guests_count' => 4,
                'price_per_night' => 120.00,
                'total_amount' => 600.00,
                'discount' => 0,
                'final_amount' => 600.00,
                'amount_paid' => 200.00,
                'payment_status' => 'partial',
                'status' => 'confirmed',
            ],
            [
                'property_id' => 5,
                'client_id' => 4,
                'check_in' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'check_out' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'guests_count' => 1,
                'price_per_night' => 45.00,
                'total_amount' => 90.00,
                'discount' => 0,
                'final_amount' => 90.00,
                'amount_paid' => 0,
                'payment_status' => 'pending',
                'status' => 'pending',
            ],
        ];

        foreach ($reservations as $i => $resData) {
            $reservation = Reservation::create(array_merge($resData, [
                'reservation_number' => 'RES-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'created_by' => $admin->id,
            ]));

            if ($resData['amount_paid'] > 0) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $resData['amount_paid'],
                    'payment_method' => 'cash',
                    'payment_date' => $resData['check_in'],
                    'created_by' => $admin->id,
                ]);
            }
        }

        // Sample expenses
        Expense::create([
            'title' => 'Réparation climatiseur Bungalow Jasmin',
            'amount' => 150.00,
            'category_id' => 1,
            'property_id' => 1,
            'expense_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'payment_method' => 'cash',
            'created_by' => $admin->id,
        ]);

        Expense::create([
            'title' => 'Fournitures nettoyage',
            'amount' => 45.50,
            'category_id' => 2,
            'expense_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'payment_method' => 'cash',
            'created_by' => $admin->id,
        ]);

        Expense::create([
            'title' => 'Facture électricité Juillet',
            'amount' => 320.00,
            'category_id' => 3,
            'expense_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'payment_method' => 'transfer',
            'created_by' => $admin->id,
        ]);

        $this->command->info('✅ Base de données initialisée avec succès!');
        $this->command->info('👤 Admin: admin@gestionlocation.com / password');
        $this->command->info('👩‍💼 Assistante: assistant@gestionlocation.com / password');

    }
}
