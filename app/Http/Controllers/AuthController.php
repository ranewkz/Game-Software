<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function forceImages($games, $isObj)
    {
        $map = [
            'Spider Man' => 'Spider_man2.jpg',
            'Elden Ring' => 'elden_ring.jpg',
            'Halo' => 'halo.jpg',
            'Zelda' => 'zelda.jpg',
            'Cyberpunk' => 'cyberpunk.jpg',
            'Yōtei' => 'ghost_of_yotei.jpg',
            'Yotei' => 'ghost_of_yotei.jpg',
            'Hades' => 'hades.jpeg',
            'Resident Evil' => 'resident_evil.jpg',
            'Forza' => 'forza.jpg',
            'God of War' => 'god_of_war.jpg',
            'Red Dead' => 'rdr2.jpg',
            'FC 26' => 'fc26.jpg',
            'Apex' => 'apex.jpg',
            'Baldur' => 'baldur.jpg',
            'Civilization' => 'civilization.jpg',
            'Control' => 'control.jpg',
            'CS2' => 'cs2.jpg',
            'Fable' => 'fable.jpg',
            'Finals' => 'finals.jpg',
            'Fortnite' => 'fortnite.jpg',
            'Genshin' => 'genshin.jpeg',
            'Grand Theft Auto' => 'gta.jpg',
            'GTA' => 'gta.jpg',
            'Hogwarts' => 'hogwarts.jpg',
            'Smash' => 'mario2.jpg',
            'Samsh' => 'mario2.jpg',
            'Wonder' => 'mario.jpg',
            'Mario' => 'mario.jpg',
            'Minecraft' => 'minecraft.jpeg',
            'Monster Hunter' => 'monster_hunter.jpg',
            'Path of Exile' => 'path_of_exile.jpg',
            'Rocket League' => 'rocket_league.jpg',
            'Valorant' => 'valorant.jpg',
            'Warframe' => 'warframe.jpg',
            'Witcher' => 'witcher3.jpg',
            'Wolverine' => 'wolverine.jpg',
            'Wukong' => 'wukong.jpg',
            'Helldivers' => 'helldivers2.jpg',
            'Palworld' => 'palworld.jpg',
            'Final Fantasy' => 'final_fantasy.jpg',
            'Call of Duty' => 'COD5.jpg',
            'Black Ops' => 'COD5.jpg',
            'Starfield' => 'starfield.jpg',
            'Tekken' => 'tekken8.jpg',
            'Street Fighter' => 'street_fighter.jpg',
            'Persona' => 'persona3.jpg',
            'Dragon' => 'dragon_dogma.jpg',
            'Animal Crossing' => 'animal_crossing.jpg',
            'Doom' => 'doom_enternal.jpg',
            'Horizon' => 'horizon.jpg',
            'Bloodborne' => 'bloodborne.jpg',
            'Sekiro' => 'sekiro.jpg',
            'The Last of Us' => 'the_last_of_us.jpg',
            'League' => 'lol.jpg'
        ];

        return collect($games)->map(function($g) use ($map, $isObj) {
            $t = $isObj ? $g->title : $g['title'];
            foreach($map as $k => $i) {
                if(stripos($t, $k) !== false) {
                    if($isObj) {
                        $g->image = $i;
                    } else {
                        $g['image'] = asset('assets/images/' . $i);
                    }
                    break;
                }
            }
            return $g;
        });
    }

    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        $user = DB::table('staff')->where('email', $request->email)->first();
        $roleName = 'admin';
        
        if (!$user) {
            $user = DB::table('customer')->where('email', $request->email)->first();
            $roleName = 'customer';
        }
        
        if ($user && Hash::check($request->password, $user->password)) {
            Session::put([
                'user_id' => $user->id, 
                'user_name' => $user->name, 
                'user_email' => $user->email, 
                'user_role' => $roleName
            ]);
            return ($roleName === 'admin') ? redirect()->route('admin.dashboard') : redirect()->route('customer.dashboard');
        }
        
        return back()->withErrors(['login_error' => 'Invalid credentials.']);
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customer,email'],
            'password' => ['required', 'min:6', 'confirmed'],
            'phone' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'address' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'role_type' => ['required', 'string']
        ]);
        
        $roleId = ($request->role_type === 'staff') ? 1 : 2;
        
        DB::table('customer')->insert([
            'name' => $request->name, 
            'email' => $request->email, 
            'password' => Hash::make($request->password),
            'role_id' => $roleId, 
            'phone' => $request->phone, 
            'gender' => $request->gender,
            'address' => $request->address, 
            'dob' => $request->dob, 
            'status' => 'active',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        
        return redirect()->route('login')->with('success', 'Profile deployed.');
    }

    public function logout() 
    { 
        Session::flush(); 
        return redirect()->route('login'); 
    }

    public function customerDashboard()
    {
        $rawGames = DB::table('games')->get()->map(fn($g) => (array)$g);
        $games = $this->forceImages($rawGames, false)->toArray();
        
        $featuredGames = array_slice($games, 0, 4);
        $physicalGames = array_filter($games, fn($g) => isset($g['supports_physical']) && $g['supports_physical'] == 1);
        
        $userCredits = 0;
        if (Session::has('user_id')) {
            $userCredits = DB::table('customer')->where('id', Session::get('user_id'))->value('credits') ?? 0;
        }
        
        $banners = DB::table('banners')->get();

        return view('customer_view.dashboard', compact('games', 'featuredGames', 'physicalGames', 'userCredits', 'banners'));
    }

    public function catalog()
    {
        $rawGames = DB::table('games')->get()->map(fn($g) => (array)$g);
        $games = $this->forceImages($rawGames, false)->toArray();
        
        $userCredits = 0;
        if (Session::has('user_id')) {
            $userCredits = DB::table('customer')->where('id', Session::get('user_id'))->value('credits') ?? 0;
        }
        
        return view('customer_view.catalog', compact('games', 'userCredits'));
    }

    public function showProfile()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        
        $user = DB::table('customer')->where('id', Session::get('user_id'))->first();
        $userCredits = $user->credits ?? 0;
        
        return view('customer_view.profile', compact('user', 'userCredits'));
    }

    public function updateProfile(Request $request)
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'min:6', 'confirmed']
        ]);
        
        $updateData = ['name' => $request->name];
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        DB::table('customer')->where('id', Session::get('user_id'))->update($updateData);
        Session::put('user_name', $request->name);
        
        return back();
    }

    public function checkoutPage()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        
        $user = DB::table('customer')->where('id', Session::get('user_id'))->first();
        $userCredits = $user->credits ?? 0;
        
        return view('customer_view.checkout', compact('user', 'userCredits'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => ['required', 'array'],
            'address' => ['required', 'string'],
            'stripeToken' => ['required', 'string'],
            'promoCode' => ['nullable', 'string']
        ]);
        
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $subtotal = 0;
            $freight = 0;
            $purchasedItems = [];
            
            foreach ($request->cart as $item) {
                $subtotal += ($item['price'] * $item['qty']);
                
                if (isset($item['format']) && $item['format'] === 'physical') {
                    $freight += (12 * $item['qty']);
                    $purchasedItems[] = [
                        'title' => $item['title'],
                        'qty' => $item['qty'],
                        'key' => 'PHYSICAL_DELIVERY_PENDING'
                    ];
                } else {
                    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    $generatedKey = substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 4);
                    
                    $purchasedItems[] = [
                        'title' => $item['title'],
                        'qty' => $item['qty'],
                        'key' => $generatedKey
                    ];
                }
            }
            
            $discount = 0;
            if ($request->filled('promoCode')) {
                $promo = DB::table('promo_codes')->where('code', strtoupper($request->promoCode))->first();
                if ($promo && ($promo->expires_at === null || \Carbon\Carbon::parse($promo->expires_at)->isFuture()) && ($promo->uses_count < $promo->max_uses)) {
                    $discount = $promo->discount_amount;
                    DB::table('promo_codes')->where('id', $promo->id)->increment('uses_count');
                }
            }

            $totalAmount = $subtotal + $freight - $discount;
            if ($totalAmount < 0) $totalAmount = 0;
            
            \Stripe\Charge::create([
                'amount' => $totalAmount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Steam Client Purchase',
            ]);
            
            $orderId = 'STM-' . rand(10000, 99990);
            
            DB::table('orders')->insert([
                'customer_id' => Session::get('user_id'),
                'total_amount' => $totalAmount, // This provides the mandatory value
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
                'items_summary' => 'Purchase via Steam Client',
                'shipping_address' => $request->address // Added this line
            ]);
            
            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'date' => now()->toDateTimeString(),
                'items' => $purchasedItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myOrders()
    {
        if (!Session::has('user_id')) return redirect()->route('login');

        $orders = DB::table('orders')
            ->where('customer_id', Session::get('user_id'))
            ->orderBy('created_at', 'desc')
            ->get();
            
        $user = DB::table('customer')->where('id', Session::get('user_id'))->first();

        return view('customer_view.orders', [
            'orders' => $orders,
            'userCredits' => $user ? $user->credits : 0
        ]);
    }

    public function toggleWishlist(Request $request)
    {
        if (!Session::has('user_id')) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $userId = Session::get('user_id');
        $gameId = $request->input('game_id');
        
        $exists = DB::table('wishlist')->where('customer_id', $userId)->where('game_id', $gameId)->first();
        
        if ($exists) { 
            DB::table('wishlist')->where('id', $exists->id)->delete(); 
            return response()->json(['status' => 'removed']);
        }
        
        DB::table('wishlist')->insert(['customer_id' => $userId, 'game_id' => $gameId]);
        return response()->json(['status' => 'added']);
    }

    public function getWishlist()
    {
        if (!Session::has('user_id')) {
            return response()->json([]);
        }
        return response()->json(DB::table('wishlist')->where('customer_id', Session::get('user_id'))->pluck('game_id'));
    }

    public function adminDashboard()
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'admin') {
            return redirect()->route('login');
        }

        $users = DB::table('customer')->get()->map(function ($user) {
            $user->role = $user->role_id == 1 ? 'admin' : 'user';
            return $user;
        });
        
        $rawGames = DB::table('games')->get();
        $games = $this->forceImages($rawGames, true);

        $banners = DB::table('banners')->get();
        $promos = DB::table('promo_codes')->get();

        return view('admin_view.dashboard', compact('users', 'games', 'banners', 'promos'));
    }

    public function storePromo(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:promo_codes,code'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['required', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date']
        ]);

        DB::table('promo_codes')->insert([
            'code' => strtoupper($request->code),
            'discount_amount' => $request->discount_amount,
            'max_uses' => $request->max_uses,
            'uses_count' => 0,
            'expires_at' => $request->expires_at,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Promo code successfully deployed to system archives.');
    }

    public function checkPromo(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);
        $promo = DB::table('promo_codes')->where('code', strtoupper($request->code))->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'INVALID SYSTEM CODE']);
        }

        if ($promo->expires_at && \Carbon\Carbon::parse($promo->expires_at)->isPast()) {
            return response()->json(['success' => false, 'message' => 'PROMO CODE EXPIRED']);
        }

        if ($promo->uses_count >= $promo->max_uses) {
            return response()->json(['success' => false, 'message' => 'PROMO USAGE LIMIT DEPLETED']);
        }

        return response()->json([
            'success' => true,
            'discount_amount' => (float)$promo->discount_amount
        ]);
    }
}