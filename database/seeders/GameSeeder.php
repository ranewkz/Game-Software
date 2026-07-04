<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            // Your Original 12
            ['title' => "Marvel's Spider-Man 2", 'genre' => 'Action', 'platform' => 'PS5', 'price' => 69.99, 'stock' => 12, 'status' => 'PS5 Exclusive'],
            ['title' => 'Elden Ring: Shadow of the Erdtree', 'genre' => 'RPG', 'platform' => 'PC, PS5, Xbox', 'price' => 39.99, 'stock' => 8, 'status' => 'Masterpiece'],
            ['title' => 'Halo Infinite: Campaign', 'genre' => 'Action', 'platform' => 'Xbox, PC', 'price' => 59.99, 'stock' => 5, 'status' => 'Xbox Essential'],
            ['title' => 'The Legend of Zelda: Tears of the Kingdom', 'genre' => 'Adventure', 'platform' => 'Switch', 'price' => 59.99, 'stock' => 15, 'status' => 'Overwhelmingly Positive'],
            ['title' => 'Cyberpunk 2077: Ultimate Edition', 'genre' => 'RPG', 'platform' => 'PC, PS5, Xbox', 'price' => 49.99, 'stock' => 20, 'status' => 'Hot Deal'],
            ['title' => 'Ghost of Yōtei', 'genre' => 'Action', 'platform' => 'PS5', 'price' => 69.99, 'stock' => 4, 'status' => 'New Release'],
            ['title' => 'Hades II', 'genre' => 'Rogue-like', 'platform' => 'PC, Switch', 'price' => 29.99, 'stock' => 30, 'status' => 'Early Access'],
            ['title' => 'Resident Evil 4 Remake', 'genre' => 'Action', 'platform' => 'PS5, Xbox, PC', 'price' => 39.99, 'stock' => 0, 'status' => 'Out of Stock'],
            ['title' => 'Forza Horizon 5', 'genre' => 'Sports', 'platform' => 'Xbox, PC', 'price' => 44.99, 'stock' => 9, 'status' => 'Adrenaline Rush'],
            ['title' => 'God of War Ragnarök', 'genre' => 'Adventure', 'platform' => 'PS5, PC', 'price' => 59.99, 'stock' => 7, 'status' => 'Highly Acclaimed'],
            ['title' => 'Red Dead Redemption 2', 'genre' => 'Adventure', 'platform' => 'PS5, Xbox, PC', 'price' => 19.99, 'stock' => 11, 'status' => 'Legendary RPG'],
            ['title' => 'EA Sports FC 26', 'genre' => 'Sports', 'platform' => 'PS5, Xbox, Switch, PC', 'price' => 59.99, 'stock' => 14, 'status' => 'Seasonal Update'],
            
            // The Next 23 Blockbusters!
            ['title' => 'Helldivers 2', 'genre' => 'Co-op Shooter', 'platform' => 'PS5, PC', 'price' => 39.99, 'stock' => 2, 'status' => 'Trending'],
            ['title' => 'Baldur\'s Gate 3', 'genre' => 'RPG', 'platform' => 'PC, PS5, Xbox', 'price' => 59.99, 'stock' => 18, 'status' => 'Game of the Year'],
            ['title' => 'Palworld', 'genre' => 'Survival', 'platform' => 'PC, Xbox', 'price' => 29.99, 'stock' => 25, 'status' => 'Viral Hit'],
            ['title' => 'Grand Theft Auto V', 'genre' => 'Action', 'platform' => 'All Platforms', 'price' => 14.99, 'stock' => 50, 'status' => 'Classic'],
            ['title' => 'The Witcher 3: Wild Hunt', 'genre' => 'RPG', 'platform' => 'All Platforms', 'price' => 19.99, 'stock' => 15, 'status' => 'Discounted'],
            ['title' => 'Minecraft', 'genre' => 'Sandbox', 'platform' => 'All Platforms', 'price' => 29.99, 'stock' => 100, 'status' => 'Evergreen'],
            ['title' => 'Super Mario Bros. Wonder', 'genre' => 'Platformer', 'platform' => 'Switch', 'price' => 59.99, 'stock' => 0, 'status' => 'Out of Stock'],
            ['title' => 'Final Fantasy VII Rebirth', 'genre' => 'RPG', 'platform' => 'PS5', 'price' => 69.99, 'stock' => 6, 'status' => 'Critically Acclaimed'],
            ['title' => 'Call of Duty: Black Ops 6', 'genre' => 'Shooter', 'platform' => 'PC, PS5, Xbox', 'price' => 69.99, 'stock' => 45, 'status' => 'Top Seller'],
            ['title' => 'Starfield', 'genre' => 'RPG', 'platform' => 'Xbox, PC', 'price' => 49.99, 'stock' => 12, 'status' => 'Standard'],
            ['title' => 'Hogwarts Legacy', 'genre' => 'Action RPG', 'platform' => 'All Platforms', 'price' => 49.99, 'stock' => 8, 'status' => 'Magical'],
            ['title' => 'Tekken 8', 'genre' => 'Fighting', 'platform' => 'PC, PS5, Xbox', 'price' => 69.99, 'stock' => 10, 'status' => 'Tournament Ready'],
            ['title' => 'Street Fighter 6', 'genre' => 'Fighting', 'platform' => 'PC, PS5, Xbox', 'price' => 59.99, 'stock' => 14, 'status' => 'Standard'],
            ['title' => 'Persona 3 Reload', 'genre' => 'JRPG', 'platform' => 'PC, PS5, Xbox', 'price' => 69.99, 'stock' => 5, 'status' => 'Low Stock'],
            ['title' => 'Dragon\'s Dogma 2', 'genre' => 'Action RPG', 'platform' => 'PC, PS5, Xbox', 'price' => 69.99, 'stock' => 7, 'status' => 'Epic Adventure'],
            ['title' => 'Animal Crossing: New Horizons', 'genre' => 'Simulation', 'platform' => 'Switch', 'price' => 59.99, 'stock' => 20, 'status' => 'Cozy'],
            ['title' => 'Doom Eternal', 'genre' => 'Shooter', 'platform' => 'All Platforms', 'price' => 19.99, 'stock' => 11, 'status' => 'Brutal'],
            ['title' => 'Horizon Forbidden West', 'genre' => 'Action RPG', 'platform' => 'PS5, PC', 'price' => 49.99, 'stock' => 9, 'status' => 'Breathtaking'],
            ['title' => 'Sekiro: Shadows Die Twice', 'genre' => 'Action', 'platform' => 'PC, PS5, Xbox', 'price' => 29.99, 'stock' => 3, 'status' => 'Hardcore'],
            ['title' => 'Bloodborne', 'genre' => 'Action RPG', 'platform' => 'PS4', 'price' => 19.99, 'stock' => 0, 'status' => 'Out of Stock'],
            ['title' => 'Super Smash Bros. Ultimate', 'genre' => 'Fighting', 'platform' => 'Switch', 'price' => 59.99, 'stock' => 16, 'status' => 'Party Essential'],
            ['title' => 'Mario Kart 8 Deluxe', 'genre' => 'Racing', 'platform' => 'Switch', 'price' => 59.99, 'stock' => 22, 'status' => 'Top Seller'],
            ['title' => 'The Last of Us Part I', 'genre' => 'Action Adventure', 'platform' => 'PS5, PC', 'price' => 49.99, 'stock' => 6, 'status' => 'Story Masterpiece']
        ];

        foreach ($games as $game) {
            DB::table('games')->insert([
                'title' => $game['title'],
                'genre' => $game['genre'],
                'platform' => $game['platform'],
                'price' => $game['price'],
                'stock' => $game['stock'],
                'status' => $game['status'],
                'image' => 'https://via.placeholder.com/150x200', 
                'rating' => 'N/A',
                'supports_physical' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}