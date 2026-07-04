<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Display a listing of physical and digital items on the storefront homepage.
     */
    public function index()
    {
        // Fetch all records from the 'item' table
        $items = DB::table('item')->get();
        
        return view('welcome', compact('items'));
    }

    /**
     * Show the form for creating a new game item.
     */
    public function create()
    {
        // Fetch types so the form dropdown can link an item to a type_id
        $types = DB::table('type')->get();
        
        return view('create_item', compact('types'));
    }

    /**
     * Store a newly created game item in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming data from the UI form fields
        $request->validate([
            'type_id' => 'required',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
            'status' => 'required|string',
        ]);

        DB::table('item')->insert([
            'type_id' => $request->type_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'description' => $request->description,
            'image' => $request->image,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect back to the homepage storefront with a success notice
        return redirect()->route('storefront.index')->with('success', 'Game added successfully!');
    }
}