<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = DB::table('staff')->where('email', $request->email)->first();
        $roleName = 'admin';
        if (!$user) {
            $user = DB::table('customer')->where('email', $request->email)->first();
            $roleName = 'customer';
        }
        if ($user && Hash::check($request->password, $user->password)) {
            Session::put(['user_id' => $user->id, 'user_name' => $user->name, 'user_email' => $user->email, 'user_role' => $roleName]);
            return ($roleName === 'admin') ? redirect()->route('admin.dashboard') : redirect()->route('customer.dashboard');
        }
        return back()->withErrors(['login_error' => 'Invalid credentials.']);
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:6|confirmed', 'phone' => 'required|string',
            'gender' => 'required|string', 'address' => 'required|string',
            'dob' => 'required|date', 'role_type' => 'required|string'
        ]);
        $roleId = ($request->role_type === 'staff') ? 1 : 2;
        DB::table('customer')->insert([
            'name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password),
            'role_id' => $roleId, 'phone' => $request->phone, 'gender' => $request->gender,
            'address' => $request->address, 'dob' => $request->dob, 'status' => 'active',
            'created_at' => now(), 'updated_at' => now()
        ]);
        return redirect()->route('login')->with('success', 'Profile deployed.');
    }

    public function logout() { Session::flush(); return redirect()->route('login'); }

    public function customerDashboard()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        $games = DB::table('games')->get()->map(fn($g) => (array)$g)->toArray();
        $featuredGames = array_slice($games, 0, 4);
        $physicalGames = array_filter($games, fn($g) => isset($g['supports_physical']) && $g['supports_physical'] == 1);
        $userCredits = DB::table('customer')->where('id', Session::get('user_id'))->value('credits') ?? 0;
        return view('customer_view.dashboard', compact('games', 'featuredGames', 'physicalGames', 'userCredits'));
    }

    public function catalog()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        $games = DB::table('games')->get()->map(fn($g) => (array)$g)->toArray();
        $userCredits = DB::table('customer')->where('id', Session::get('user_id'))->value('credits') ?? 0;
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
        $request->validate(['name' => 'required|string|max:255', 'password' => 'nullable|min:6|confirmed']);
        $updateData = ['name' => $request->name];
        if ($request->filled('password')) $updateData['password'] = Hash::make($request->password);
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
            'cart' => 'required|array',
            'address' => 'required|string',
            'stripeToken' => 'required|string'
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
            
            $totalAmount = $subtotal + $freight;
            
            \Stripe\Charge::create([
                'amount' => $totalAmount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Steam Client Purchase',
            ]);
            
            $orderId = 'STM-' . rand(10000, 99990);
            
            DB::table('orders')->insert([
                'customer_id' => Session::get('user_id'),
                'payment_gateway' => 'Stripe Secure Uplink',
                'cargo_pathway' => $request->address,
                'total_charged' => $totalAmount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
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
        return response()->json(DB::table('wishlist')->where('customer_id', Session::get('user_id'))->pluck('game_id'));
    }

    public function adminDashboard()
{
    return view('admin_view.dashboard');
}
}