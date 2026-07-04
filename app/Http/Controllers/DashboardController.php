<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    // ==========================================
    // 1. CORE DASHBOARD & DATA RETRIEVAL
    // ==========================================
    public function index()
    {
        // SECURITY PROTOCOL
        if (!Session::has('user_role') || Session::get('user_role') !== 'staff') {
            return redirect()->route('login')->withErrors(['login_error' => 'SYSTEM OVERRIDE DENIED: Admin clearance required.']);
        }

        $staff = DB::table('staff')->get()->map(function ($user) { $user->role = 'admin'; return $user; });
        $customers = DB::table('customer')->get()->map(function ($user) { $user->role = 'user'; return $user; });
        $users = $staff->merge($customers)->sortByDesc('id');

        // --- THE INDESTRUCTIBLE KEYWORD MAP ---
        $games = DB::table('games')->get()->map(function($game) {
            
            // Just uses one unique word from the game to find the right image based on your exact folder
            $imageKeywords = [
                'spider' => 'Spider_man2.jpg',
                'elden' => 'elden_ring.jpg',
                'halo' => 'halo.jpg',
                'zelda' => 'zelda.jpg',
                'cyberpunk' => 'cyberpunk.jpg',
                'ghost' => 'ghost_of_yotei.jpg',
                'hades' => 'hades.jpg',
                'resident' => 'resident_evil.jpg',
                'forza' => 'forza.jpg',
                'god of war' => 'god_of_war.jpg',
                'red dead' => 'rdr2.jpg',
                'apex' => 'apex.jpg',
                'baldur' => 'baldur.jpg',
                'civilization' => 'civilization.jpg',
                'control' => 'control.jpg',
                'counter' => 'cs2.jpg',
                'fable' => 'fable.jpg',
                'fc' => 'fc26.jpg', 
                'finals' => 'finals.jpg',
                'fortnite' => 'fortnite.jpg',
                'genshin' => 'genshin.jpg',
                'hogwarts' => 'hogwarts.jpg',
                'league' => 'lol.jpg',
                'mario' => 'mario.jpg',
                'minecraft' => 'minecraft.jpg',
                'monster' => 'monster_hunter.jpg',
                'path' => 'path_of_exile.jpg',
                'rocket' => 'rocket_league.jpg',
                'valorant' => 'valorant.jpg',
                'warframe' => 'warframe.jpg',
                'witcher' => 'witcher3.jpg',
                'wolverine' => 'wolverine.jpg',
                'wukong' => 'wukong.jpg',
                'gta' => 'gta.jpg',
                'grand theft' => 'gta.jpg'
            ];

            $finalImage = null;

            // Loop through keywords to see if the game title contains one
            foreach ($imageKeywords as $keyword => $filename) {
                if (stripos($game->title, $keyword) !== false) {
                    $finalImage = $filename;
                    break;
                }
            }

            // If no keyword matches (like if you add a brand new game later), just try to clean up whatever is in the DB
            if (!$finalImage) {
                $cleanName = basename(parse_url($game->image ?? '', PHP_URL_PATH));
                $finalImage = $cleanName ?: 'placeholder.jpg';
            }

            // Attach it to the game for the HTML to read
            $game->final_image = $finalImage;

            return $game;
        });

        $pendingAdmins = collect([]);

        return view('admin_view.dashboard', compact('users', 'games', 'pendingAdmins'));
    }

    // ==========================================
    // 2. GAME VAULT MANAGEMENT (CRUD)
    // ==========================================

    public function create()
    {
        // Security Check
        if (!Session::has('user_role') || Session::get('user_role') !== 'staff') {
            return redirect()->route('login')->withErrors(['login_error' => 'SYSTEM OVERRIDE DENIED: Admin clearance required.']);
        }

        return view('admin_view.create_game');
    }


   public function store(Request $request)
    {
        // 1. Validate the new inputs
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|string', 
            'genre' => 'required|string|max:100',
            'platform' => 'required|string|max:100',
        ]);

        // 2. Insert EVERYTHING into the database
        DB::table('games')->insert([
            'title' => $request->title,
            'price' => $request->price,
            'image' => $request->image,
            'genre' => $request->genre,       
            'platform' => $request->platform, 
            'stock' => rand(5, 50),
            'status' => 'Standard',
            'rating' => 0, // <-- THIS FIXES THE CRASH. Bypasses the strict DB rule.
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Teleport back to the dashboard instead of staying on the create page
        return redirect()->route('admin.dashboard')->with('success', 'Game successfully added to the vault.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|string',
        ]);

        DB::table('games')->where('id', $id)->update([
            'title' => $request->title,
            'price' => $request->price,
            'image' => $request->image,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Game data overwritten.');
    }

    public function destroy($id)
    {
        DB::table('games')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Game purged from the system.');
    }

    // ==========================================
    // 3. ADMIN PROFILE OVERRIDE
    // ==========================================
    public function updateProfile(Request $request)
    {
        $adminId = Session::get('user_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $updateData['avatar_path'] = 'uploads/avatars/' . $filename;
            Session::put('user_avatar', $updateData['avatar_path']);
        }

        DB::table('staff')->where('id', $adminId)->update($updateData);

        Session::put('user_name', $request->name);
        Session::put('user_email', $request->email);
        Session::put('user_phone', $request->phone);
        Session::put('user_address', $request->address);

        return redirect()->back()->with('success', 'Admin profile parameters updated.');
    }

    // ==========================================
    // 4. SECURITY PROTOCOLS (BANS & ROLES)
    // ==========================================
    public function toggleBan($type, $id)
    {
        $table = ($type === 'admin') ? 'staff' : 'customer';

        if ($table === 'staff' && $id == Session::get('user_id')) {
            return redirect()->back()->withErrors(['login_error' => 'SYSTEM ERROR: You cannot ban yourself.']);
        }

        $user = DB::table($table)->where('id', $id)->first();

        if ($user) {
            $newStatus = (isset($user->status) && $user->status === 'banned') ? 'active' : 'banned';
            DB::table($table)->where('id', $id)->update(['status' => $newStatus]);
            return redirect()->back()->with('success', 'Operative status updated to: ' . strtoupper($newStatus));
        }

        return redirect()->back()->withErrors(['login_error' => 'Target not found in the matrix.']);
    }

    public function updateRole(Request $request, $type, $id)
    {
        $request->validate(['new_role' => 'required|in:admin,user']);
        $newRole = $request->new_role;

        if ($type === 'admin' && $id == Session::get('user_id')) {
            return redirect()->back()->withErrors(['login_error' => 'SYSTEM OVERRIDE: You cannot revoke your own clearance.']);
        }

        if ($type === $newRole) {
            return redirect()->back()->with('success', 'Operative is already assigned to that clearance level.');
        }

        $sourceTable = ($type === 'admin') ? 'staff' : 'customer';
        $targetTable = ($newRole === 'admin') ? 'staff' : 'customer';
        $newRoleId = ($newRole === 'admin') ? 1 : 2;

        $user = DB::table($sourceTable)->where('id', $id)->first();

        if ($user) {
            DB::table($targetTable)->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'phone' => $user->phone ?? null,
                'address' => $user->address ?? null,
                'dob' => $user->dob ?? '2000-01-01', 
                'gender' => $user->gender ?? 'Other',
                'status' => $user->status ?? 'active',
                'role_id' => $newRoleId,
                'created_at' => $user->created_at ?? now(),
                'updated_at' => now(),
            ]);

            DB::table($sourceTable)->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Operative clearance successfully reassigned.');
        }

        return redirect()->back()->withErrors(['login_error' => 'User not found in database.']);
    }
}