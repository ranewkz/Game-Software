<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==========================================
    // 1. AUTHENTICATION (LOGIN, REGISTER, LOGOUT)
    // ==========================================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Try to find user in 'staff' table first
        $user = DB::table('staff')->where('email', $request->email)->first();
        $roleName = 'admin';

        // If not in 'staff', try 'customer' table
        if (!$user) {
            $user = DB::table('customer')->where('email', $request->email)->first();
            $roleName = 'customer';
        }

        // Verify password
        if ($user && Hash::check($request->password, $user->password)) {
            Session::put([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'user_role'  => $roleName
            ]);

            return ($roleName === 'admin') 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('customer.dashboard');
        }

        return back()->withErrors(['login_error' => 'Invalid comm-link credentials provided.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customer,email',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'required|string',
            'gender'   => 'required|string',
            'address'  => 'required|string',
            'dob'      => 'required|date',
            'role_type'=> 'required|string'
        ]);

        $roleId = ($request->role_type === 'staff') ? 1 : 2;

        DB::table('customer')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role_id'    => $roleId,
            'phone'      => $request->phone,
            'gender'     => $request->gender,
            'address'    => $request->address,
            'dob'        => $request->dob,
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('login')->with('success', 'Operative profile deployed. Please login.');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }

    // ==========================================
    // 2. CUSTOMER DASHBOARD, PROFILE, ORDERS
    // ==========================================

    public function customerDashboard()
    {
        if (!Session::has('user_id')) return redirect()->route('login');

        $banners = DB::table('banners')->orderBy('created_at', 'desc')->get();
        $games   = DB::table('games')->get()->map(fn($g) => (array)$g)->toArray();
        
        $physicalGames = array_filter($games, function($game) {
            return isset($game['supports_physical']) && $game['supports_physical'] == 1;
        });

        return view('customer_view.dashboard', compact('games', 'physicalGames', 'banners'));
    }

    public function showProfile()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        
        $user = DB::table('customer')->where('id', Session::get('user_id'))->first();
        return view('customer_view.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        if (!Session::has('user_id')) return redirect()->route('login');

        $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $updateData = ['name' => $request->name];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('customer')->where('id', Session::get('user_id'))->update($updateData);
        Session::put('user_name', $request->name);

        return back()->with('success', 'Profile override successful.');
    }

    public function myOrders()
    {
        if (!Session::has('user_id')) return redirect()->route('login');
        
        // Wrap the database call in a try/catch block.
        // If the 'orders' table is missing, it won't crash the server.
        try {
            $orders = DB::table('orders')
                ->where('customer_id', Session::get('user_id'))
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // Table doesn't exist yet, return an empty collection
            $orders = collect([]); 
        }
            
        return view('customer_view.orders', compact('orders'));
    }

    // ==========================================
    // 3. ADMIN DASHBOARD & CMS
    // ==========================================

    public function adminDashboard()
    {
        if (Session::get('user_role') !== 'admin') return redirect()->route('login');

        // Joined with role table to prevent "Undefined property: role" errors
        $users = DB::table('customer')
            ->leftJoin('role', 'customer.role_id', '=', 'role.id')
            ->select('customer.*', 'role.name as role')
            ->get();

        $games   = DB::table('games')->get();
        $banners = DB::table('banners')->orderBy('created_at', 'desc')->get();

        return view('admin_view.dashboard', compact('users', 'games', 'banners'));
    }

    public function storeBanner(Request $request)
    {
        $request->validate([
            'banner_image' => 'required|image|max:5120',
            'title'        => 'required|string'
        ]);

        $imageName = time() . '.' . $request->banner_image->extension();
        $request->banner_image->move(public_path('assets/images/banners'), $imageName);

        DB::table('banners')->insert([
            'image' => $imageName,
            'title' => $request->title,
            'created_at' => now()
        ]);

        return back()->with('success', 'Banner deployed.');
    }

    public function destroyBanner($id)
    {
        DB::table('banners')->where('id', $id)->delete();
        return back()->with('success', 'Banner purged.');
    }
}