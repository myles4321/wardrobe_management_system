<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ClothingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'clothings' => Clothing::where('user_id', Auth::id())->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $clothing = new Clothing();
        $clothing->name = $request->name;
        $clothing->category = $request->category;
        $clothing->user_id = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('clothings', 'public');
        }

        $clothing->save();

        return response()->json([
            'message' => 'Clothing item added successfully!',
            'clothing' => $clothing
        ]);
    }        

    /**
     * Display the specified resource.
     */
    public function show(Clothing $clothing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clothing $clothing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clothing $clothing)
{
    if ($clothing->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);
    
    if ($request->hasFile('image')) {
        if ($clothing->image) {
            Storage::disk('public')->delete($clothing->image);
        }
        $data['image'] = $request->file('image')->store('clothings', 'public');
    }
    
    $clothing->update($data);
    
    return response()->json([
        'message' => 'Clothing item updated successfully!',
        'clothing' => $clothing
    ]);
}

public function destroy(Clothing $clothing)
{
    if ($clothing->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    if ($clothing->image) {
        Storage::disk('public')->delete($clothing->image);
    }

    $clothing->delete();
    
    return response()->json([
        'message' => 'Clothing item deleted successfully!'
    ]);
}
}
