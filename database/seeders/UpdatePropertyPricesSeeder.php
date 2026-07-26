<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class UpdatePropertyPricesSeeder extends Seeder
{
    /**
     * Met à jour ou crée les bungalows selon le tableau tarifaire officiel.
     * Prix 1 = Basse saison (1er Oct → 31 Mai)
     * Prix 2 = Épaule (Juin & Septembre)
     * Prix 3 = Haute saison (Juillet & Août)
     */
    public function run(): void
    {
        $bungalows = [
            'nesma' => [
                'official_name' => 'BUNGALOW NESMA S+ 2',
                'price_per_night' => 230.00,
                'weekend_price' => 390.00,
                'summer_price' => 490.00,
                'capacity' => 5,
                'bedrooms' => 2,
                'search_keys' => ['nesma'],
            ],
            'chams' => [
                'official_name' => 'BUNGALOW CHAMS S+2',
                'price_per_night' => 280.00,
                'weekend_price' => 440.00,
                'summer_price' => 540.00,
                'capacity' => 5,
                'bedrooms' => 2,
                'search_keys' => ['chams', 'shams'],
            ],
            'yamin' => [
                'official_name' => 'BUNGALOW YAMIN S+1',
                'price_per_night' => 340.00,
                'weekend_price' => 540.00,
                'summer_price' => 690.00,
                'capacity' => 4,
                'bedrooms' => 1,
                'search_keys' => ['yamin'],
            ],
            'belqis' => [
                'official_name' => 'BUNGALOW BELQIS S+2',
                'price_per_night' => 470.00,
                'weekend_price' => 640.00,
                'summer_price' => 840.00,
                'capacity' => 6,
                'bedrooms' => 2,
                'search_keys' => ['belqis'],
            ],
            'rihana' => [
                'official_name' => 'BUNGALOW RIHANA S+2',
                'price_per_night' => 520.00,
                'weekend_price' => 690.00,
                'summer_price' => 890.00,
                'capacity' => 6,
                'bedrooms' => 2,
                'search_keys' => ['rihana'],
            ],
        ];

        foreach ($bungalows as $key => $info) {
            // Tentative de recherche intelligente dans la base existante
            $property = null;
            foreach ($info['search_keys'] as $searchKey) {
                $property = Property::where('name', 'LIKE', '%' . $searchKey . '%')->first();
                if ($property) {
                    break;
                }
            }

            if ($property) {
                // Logement trouvé, on le met à jour
                $oldName = $property->name;
                $property->update([
                    'name' => $info['official_name'],
                    'type' => 'bungalow',
                    'price_per_night' => $info['price_per_night'],
                    'weekend_price' => $info['weekend_price'],
                    'summer_price' => $info['summer_price'],
                    'capacity' => $info['capacity'],
                    'bedrooms' => $info['bedrooms'],
                ]);
                $this->command->info("✅ Logement existant '{$oldName}' mis à jour vers '{$info['official_name']}'.");
            } else {
                // Logement non trouvé, on le crée de toutes pièces
                Property::create([
                    'name' => $info['official_name'],
                    'type' => 'bungalow',
                    'description' => "Superbe {$info['official_name']} tout confort avec climatisation, wifi et cuisine équipée.",
                    'price_per_night' => $info['price_per_night'],
                    'weekend_price' => $info['weekend_price'],
                    'summer_price' => $info['summer_price'],
                    'capacity' => $info['capacity'],
                    'bedrooms' => $info['bedrooms'],
                    'surface' => $info['bedrooms'] === 1 ? '55' : '85',
                    'amenities' => ['WiFi', 'Climatisation', 'TV', 'Cuisine équipée', 'Terrasse', 'Parking'],
                    'status' => 'available',
                    'location' => 'Zone A - Bord de mer',
                ]);
                $this->command->info("🆕 Nouveau logement '{$info['official_name']}' créé avec succès.");
            }
        }

        $this->command->info('');
        $this->command->info('Tarification officielle des Bungalows mise en place !');
    }
}
