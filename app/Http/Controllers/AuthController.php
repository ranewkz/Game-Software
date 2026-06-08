<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the registration page view.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle a new registration submission. Automatically defaults to Customer.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email|unique:staff,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:7',
            'address' => 'required|string',
            'gender' => 'required|string',
            'dob' => 'required|date',
        ]);

        $hashedPassword = Hash::make($request->password);

        DB::table('customer')->insert([
            'role_id' => 2, 
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'password' => $hashedPassword,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Account created! Level up and log in.');
    }

    /**
     * Show the login page view.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Search staff table first
        $user = DB::table('staff')->where('email', $request->email)->first();
        $role = 'staff';

        // If not found in staff, check customer table
        if (!$user) {
            $user = DB::table('customer')->where('email', $request->email)->first();
            $role = 'customer';
        }

        // Verify password against secure hash match
        if ($user && Hash::check($request->password, $user->password)) {
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            Session::put('user_role', $role);
            Session::put('user_email', $user->email);
            Session::put('user_phone', $user->phone ?? '09-123456789');
            Session::put('user_address', $user->address ?? 'No physical shipping address provided.');

            if ($role === 'staff') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('customer.dashboard');
            }
        }

        return back()->withErrors(['login_error' => 'Invalid credentials. Your mission failed. Try again.']);
    }

    /**
     * Handle logging out and breaking the active session.
     */
    public function logout()
    {
        Session::forget(['user_id', 'user_name', 'user_role', 'user_email', 'user_phone', 'user_address']);
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    /**
     * Display the Admin Dashboard view.
     */
    public function adminDashboard()
    {
        if (Session::get('user_role') !== 'staff') {
            return redirect()->route('login')->withErrors(['auth' => 'Access Denied. Admins Only.']);
        }

        return "SUCCESS: Admin Authorized! Welcome to the bridge, " . Session::get('user_name') . ".";
    }

    /**
     * Display the Customer Dashboard view with an extensive, automated fallback catalog containing stock limits.
     */
    public function customerDashboard()
    {
        if (Session::get('user_role') !== 'customer') {
            return redirect()->route('login')->withErrors(['auth' => 'Please log in to view your dashboard.']);
        }

        // Safely pull entries out of your games table
        $dbGames = [];
        try {
            $dbGames = DB::table('games')->get();
        } catch (\Exception $e) {
            // Fails silently if table layout doesn't match yet
        }

        // 🎮 COMPLETE 35-GAME CATALOG WITH CUSTOMIZABLE IMAGE PATHS
        $mockGames = [
            [
                'id' => 201,
                'title' => 'Marvel\'s Spider-Man 2',
                'genre' => 'Action',
                'platform' => 'PS5',
                'price' => 69.99,
                'image' => asset('assets/images/Spider_man2.jpg'),
                'rating' => '9.7',
                'status' => 'PS5 Exclusive',
                'stock' => 12,
                'supports_physical' => true,
                'description' => 'Two Spider-Men, Peter Parker and Miles Morales, return for an exciting new adventure in the critically acclaimed Marvel\'s Spider-Man franchise for PS5.'
            ],
            [
                'id' => 202,
                'title' => 'Elden Ring: Shadow of the Erdtree',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 39.99,
                'image' => asset('assets/images/elden_ring.jpg'),
                'rating' => '9.8',
                'status' => 'Masterpiece',
                'stock' => 8,
                'supports_physical' => true,
                'description' => 'Guided by Empyrean Miquella, players are summoned to the Land of Shadow, a place obscured by the Erdtree where the goddess Marika first set foot.'
            ],
            [
                'id' => 203,
                'title' => 'Halo Infinite: Campaign',
                'genre' => 'Action',
                'platform' => 'Xbox, PC',
                'price' => 59.99,
                'image' => asset('assets/images/halo.jpg'),
                'rating' => '8.7',
                'status' => 'Xbox Essential',
                'stock' => 5,
                'supports_physical' => true,
                'description' => 'When all hope is lost and humanity\'s fate hangs in the balance, the Master Chief is ready to confront the most ruthless foe he\'s ever faced.'
            ],
            [
                'id' => 204,
                'title' => 'The Legend of Zelda: Tears of the Kingdom',
                'genre' => 'Adventure',
                'platform' => 'Switch',
                'price' => 59.99,
                'image' => asset('assets/images/zelda.jpg'),
                'rating' => '9.9',
                'status' => 'Overwhelmingly Positive',
                'stock' => 15,
                'supports_physical' => false,
                'description' => 'An epic adventure across the land and skies of Hyrule awaits in the sequel to The Legend of Zelda: Breath of the Wild.'
            ],
            [
                'id' => 205,
                'title' => 'Cyberpunk 2077: Ultimate Edition',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 49.99,
                'image' => asset('assets/images/cyberpunk.jpg'),
                'rating' => '9.4',
                'status' => 'Hot Deal',
                'stock' => 20,
                'supports_physical' => true,
                'description' => 'Play as V, a mercenary outlaw going up against the strongest forces of Night City in a bid for survival and immortality.'
            ],
            [
                'id' => 206,
                'title' => 'Ghost of Yōtei',
                'genre' => 'Action',
                'platform' => 'PS5',
                'price' => 69.99,
                'image' => asset('assets/images/ghost_of_yotei.jpg'),
                'rating' => '9.6',
                'status' => 'New Release',
                'stock' => 4,
                'supports_physical' => true,
                'description' => 'In 1603, a new Ghost named Atsu embarks on a journey of vengeance in the rugged lands surrounding Mount Yotei.'
            ],
            [
                'id' => 207,
                'title' => 'Hades II',
                'genre' => 'Rogue-like',
                'platform' => 'PC, Switch',
                'price' => 29.99,
                'image' => asset('assets/images/hades.jpeg'),
                'rating' => '9.5',
                'status' => 'Early Access',
                'stock' => 30,
                'supports_physical' => false,
                'description' => 'Battle beyond the Underworld using dark sorcery to take on the Titan of Time in this spellbinding rogue-like dungeon crawler.'
            ],
            [
                'id' => 208,
                'title' => 'Resident Evil 4 Remake',
                'genre' => 'Action',
                'platform' => 'PS5, Xbox, PC',
                'price' => 39.99,
                'image' => asset('assets/images/resident.jpg'),
                'rating' => '9.5',
                'status' => 'Out of Stock',
                'stock' => 0,
                'supports_physical' => true,
                'description' => 'Leon S. Kennedy, a survivor of the Raccoon City incident, is sent to rescue the President\'s kidnapped daughter from a secluded European village.'
            ],
            [
                'id' => 209,
                'title' => 'Forza Horizon 5',
                'genre' => 'Sports',
                'platform' => 'Xbox, PC',
                'price' => 44.99,
                'image' => asset('assets/images/forza.jpg'),
                'rating' => '9.2',
                'status' => 'Adrenaline Rush',
                'stock' => 9,
                'supports_physical' => true,
                'description' => 'Explore the vibrant and ever-evolving open world landscapes of Mexico with limitless, fun driving action in hundreds of the world\'s greatest cars.'
            ],
            [
                'id' => 210,
                'title' => 'God of War Ragnarök',
                'genre' => 'Adventure',
                'platform' => 'PS5, PC',
                'price' => 59.99,
                'image' => asset('assets/images/god_of_war.jpg'),
                'rating' => '9.8',
                'status' => 'Highly Acclaimed',
                'stock' => 7,
                'supports_physical' => true,
                'description' => 'Kratos and Atreus embark on a mythic journey for answers before the prophesied battle that will end the world.'
            ],
            [
                'id' => 211,
                'title' => 'Red Dead Redemption 2',
                'genre' => 'Adventure',
                'platform' => 'PS5, Xbox, PC',
                'price' => 19.99,
                'image' => asset('assets/images/rdr2(1).jpg'),
                'rating' => '9.9',
                'status' => 'Legendary RPG',
                'stock' => 11,
                'supports_physical' => true,
                'description' => 'Winner of over 175 Game of the Year Awards, Red Dead Redemption 2 is an epic tale of honor and loyalty at the dawn of the modern age.'
            ],
            [
                'id' => 212,
                'title' => 'EA Sports FC 26',
                'genre' => 'Sports',
                'platform' => 'PS5, Xbox, Switch, PC',
                'price' => 59.99,
                'image' => asset('assets/images/fc26.jpg'),
                'rating' => '8.0',
                'status' => 'Seasonal Update',
                'stock' => 14,
                'supports_physical' => true,
                'description' => 'Experience the world\'s game with unparalleled realism, featuring the biggest tournaments, leagues, and clubs across men\'s and women\'s football.'
            ],
            [
                'id' => 213,
                'title' => 'Civilization VII',
                'genre' => 'Strategy',
                'platform' => 'PC',
                'price' => 59.99,
                'image' => asset('assets/images/civilization.jpg'),
                'rating' => '9.1',
                'status' => 'Pre-purchase',
                'stock' => 50,
                'supports_physical' => false,
                'description' => 'Build an empire to stand the test of time in the revolutionary new chapter of the history-defining strategic simulation franchise.'
            ],
            [
                'id' => 215,
                'title' => 'Super Mario Bros. Wonder',
                'genre' => 'Adventure',
                'platform' => 'Switch',
                'price' => 54.99,
                'image' => asset('assets/images/mario.jpg'),
                'rating' => '9.6',
                'status' => 'Switch Classic',
                'stock' => 10,
                'supports_physical' => false,
                'description' => 'Find wonder in the next side-scrolling Mario adventure! Wonder Flowers trigger game-changing, spectacular phenomena.'
            ],
            [
                'id' => 216,
                'title' => 'Grand Theft Auto VI',
                'genre' => 'Action',
                'platform' => 'PS5, Xbox',
                'price' => 69.99,
                'image' => asset('assets/images/gta.jpg'),
                'rating' => '9.9',
                'status' => 'Highly Anticipated',
                'stock' => 10,
                'supports_physical' => true,
                'description' => 'Head to Leonida, home to neon-soaked streets and the ultimate open world crime saga.'
            ],
            [
                'id' => 217,
                'title' => 'Minecraft',
                'genre' => 'Strategy',
                'platform' => 'PC, Switch, PS5, Xbox',
                'price' => 26.99,
                'image' => asset('assets/images/minecraft.jpeg'),
                'rating' => '9.5',
                'status' => 'All-Time Classic',
                'stock' => 45,
                'supports_physical' => true,
                'description' => 'Explore infinite blocky worlds and build everything from simple homes to grand castles.'
            ],
            [
                'id' => 218,
                'title' => 'The Witcher 3: Wild Hunt',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 19.99,
                'image' => asset('assets/images/witcher3.jpg'),
                'rating' => '9.8',
                'status' => 'RPG Legend',
                'stock' => 12,
                'supports_physical' => true,
                'description' => 'Become Geralt of Rivia, professional monster slayer, in a vast dark-fantasy open world.'
            ],
            [
                'id' => 219,
                'title' => 'Hogwarts Legacy',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox, Switch',
                'price' => 49.99,
                'image' => asset('assets/images/hogwarts.jpg'),
                'rating' => '9.3',
                'status' => 'Magic RPG',
                'stock' => 18,
                'supports_physical' => true,
                'description' => 'Experience Hogwarts in the 1800s. Your character holds the key to an ancient, dark secret.'
            ],
            [
                'id' => 222,
                'title' => 'Black Myth: Wukong',
                'genre' => 'RPG',
                'platform' => 'PC, PS5',
                'price' => 59.99,
                'image' => asset('assets/images/wukong.jpg'),
                'rating' => '9.6',
                'status' => 'Top Seller',
                'stock' => 16,
                'supports_physical' => true,
                'description' => 'An action RPG rooted in Chinese mythology. Set out as the Destined One to uncover the truth of a glorious legend.'
            ],
            [
                'id' => 224,
                'title' => 'Baldur\'s Gate 3',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 59.99,
                'image' => asset('assets/images/baldur.jpg'),
                'rating' => '9.9',
                'status' => 'Game of the Year',
                'stock' => 14,
                'supports_physical' => true,
                'description' => 'Gather your party and return to the Forgotten Realms in a tale of fellowship and betrayal, sacrifice and survival.'
            ],
            [
                'id' => 226,
                'title' => 'Marvel\'s Wolverine',
                'genre' => 'Action',
                'platform' => 'PS5',
                'price' => 69.99,
                'image' => asset('assets/images/wolverine.jpg'),
                'rating' => '9.4',
                'status' => 'PS5 Showcase',
                'stock' => 15,
                'supports_physical' => true,
                'description' => 'A standalone visceral action experience from Insomniac Games following Logan\'s claws and rage.'
            ],
            [
                'id' => 227,
                'title' => 'Resident Evil: Requiem',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox',
                'price' => 69.99,
                'image' => asset('assets/images/resident_evil.jpg'),
                'rating' => '9.6',
                'status' => 'GOTY Nominee',
                'stock' => 9,
                'supports_physical' => true,
                'description' => 'The highly anticipated ninth installment in Capcom\'s iconic survival horror franchise.'
            ],
            [
                'id' => 228,
                'title' => 'Fable',
                'genre' => 'RPG',
                'platform' => 'Xbox, PC',
                'price' => 59.99,
                'image' => asset('assets/images/fable.jpg'),
                'rating' => '9.2',
                'status' => 'Xbox Blockbuster',
                'stock' => 11,
                'supports_physical' => true,
                'description' => 'A new beginning for the legendary franchise. Explore a world of heroes, magic, and whimsical choices.'
            ],
            [
                'id' => 229,
                'title' => 'Monster Hunter Wilds',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 69.99,
                'image' => asset('assets/images/monster_hunter.jpg'),
                'rating' => '9.5',
                'status' => 'Top Charting',
                'stock' => 14,
                'supports_physical' => true,
                'description' => 'Battle massive monsters in vast, dynamic living ecosystems with next-gen cooperative hunting.'
            ],
            [
                'id' => 230,
                'title' => 'Control Resonant',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox',
                'price' => 59.99,
                'image' => asset('assets/images/control.jpg'),
                'rating' => '9.3',
                'status' => 'Remedy Shared Universe',
                'stock' => 6,
                'supports_physical' => true,
                'description' => 'Uncover reality-bending federal mysteries and strange superpowers in this phenomenal sci-fi sequel.'
            ],
            [
                'id' => 214,
                'title' => 'Valorant',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox',
                'price' => 0.00,
                'image' => asset('assets/images/valorant.jpg'),
                'rating' => '8.9',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'Riot Games\' hyper-competitive 5v5 tactical shooter blending precise gunplay and sharp agent abilities.'
            ],
            [
                'id' => 231,
                'title' => 'Fortnite',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox, Switch',
                'price' => 0.00,
                'image' => asset('assets/images/fortnite.jpg'),
                'rating' => '9.1',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'The ultimate battle royale phenomenon! Drop in, build, and battle in massive seasonal sandbox structures.'
            ],
            [
                'id' => 232,
                'title' => 'Counter-Strike 2',
                'genre' => 'Action',
                'platform' => 'PC',
                'price' => 0.00,
                'image' => asset('assets/images/cs2.jpg'),
                'rating' => '9.0',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'The premier technical tactical shooter on Steam, featuring advanced smoke mechanics and sub-tick precision servers.'
            ],
            [
                'id' => 233,
                'title' => 'League of Legends',
                'genre' => 'Strategy',
                'platform' => 'PC',
                'price' => 0.00,
                'image' => asset('assets/images/lol.jpg'),
                'rating' => '8.8',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'The world\'s most popular MOBA battlefield strategy framework. Coordinate champions to destroy the enemy Nexus.'
            ],
            [
                'id' => 234,
                'title' => 'Apex Legends',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox, Switch',
                'price' => 0.00,
                'image' => asset('assets/images/apex.jpg'),
                'rating' => '8.9',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'High-speed squad battle royale from Respawn Entertainment featuring legendary character dynamics.'
            ],
            [
                'id' => 235,
                'title' => 'Warframe',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox, Switch',
                'price' => 0.00,
                'image' => asset('assets/images/warframe.jpg'),
                'rating' => '9.2',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'Awaken as a space ninja ninja cyborg warrior. Deep customization, rich sci-fi progression, and looting loops.'
            ],
            [
                'id' => 236,
                'title' => 'Genshin Impact',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Mobile',
                'price' => 0.00,
                'image' => asset('assets/images/genshin.jpeg'),
                'rating' => '9.3',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'Explore Teyvat, a beautiful open-world action RPG filled with elemental combat mechanics and tracking lore.'
            ],
            [
                'id' => 237,
                'title' => 'Path of Exile 2',
                'genre' => 'RPG',
                'platform' => 'PC, PS5, Xbox',
                'price' => 0.00,
                'image' => asset('assets/images/path_of_exile.jpg'),
                'rating' => '9.7',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'The massive maximalist next-generation isometric action RPG featuring incredible customization and deep endgame builds.'
            ],
            [
                'id' => 238,
                'title' => 'Rocket League',
                'genre' => 'Sports',
                'platform' => 'PC, PS5, Xbox, Switch',
                'price' => 0.00,
                'image' => asset('assets/images/rocket_league.jpg'),
                'rating' => '9.0',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'High-octane physics-based car soccer action matches with fair competitive balancing rules.'
            ],
            [
                'id' => 239,
                'title' => 'The Finals',
                'genre' => 'Action',
                'platform' => 'PC, PS5, Xbox',
                'price' => 0.00,
                'image' => asset('assets/images/finals.jpg'),
                'rating' => '8.9',
                'status' => 'Free-To-Play',
                'stock' => 999,
                'supports_physical' => false,
                'description' => 'An explosive virtual game show shooter featuring fully destructible maps and arena dynamic tools.'
            ]
        ];

        // Format and merge data structures cleanly
        $games = [];
        if (!empty($dbGames) && count($dbGames) > 0) {
            foreach ($dbGames as $dbGame) {
                $games[] = [
                    'id' => $dbGame->id ?? rand(300, 999),
                    'title' => $dbGame->title ?? ($dbGame->name ?? 'DB Secure License'),
                    'genre' => $dbGame->genre ?? 'General',
                    'platform' => $dbGame->platform ?? 'PC',
                    'price' => floatval($dbGame->price ?? 19.99),
                    'image' => $dbGame->image ?? 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80',
                    'rating' => $dbGame->rating ?? '8.5',
                    'status' => ($dbGame->stock ?? 10) > 0 ? 'In Stock' : 'Out of Stock',
                    'stock' => intval($dbGame->stock ?? 10),
                    'supports_physical' => (strpos(strtolower($dbGame->platform ?? ''), 'ps5') !== false || strpos(strtolower($dbGame->platform ?? ''), 'xbox') !== false),
                    'description' => $dbGame->description ?? 'Secure gaming license directory release.'
                ];
            }
        } else {
            $games = $mockGames;
        }

        // Compile physical games cleanly for the top CD ribbon view
        $physicalGames = array_filter($games, function($game) {
            return isset($game['supports_physical']) && $game['supports_physical'] === true;
        });

        if (empty($physicalGames)) {
            $physicalGames = array_slice($games, 0, 4);
        }

        return view('customer_view.dashboard', compact('games', 'physicalGames'));
    }
}