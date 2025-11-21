<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use Illuminate\Http\Request;

class PagesController extends Controller
{


    public function index()
    {
        return response()->json(Pages::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',

        ]);

       

        $user = Pages::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, $id)
{
    
    $validated = $request->validate([
        'page' => 'required|string|max:255',
    ]);

    $page = Pages::findOrFail($id);

   
    $page->update($validated);

    
    return response()->json([
        'message' => 'Page updated successfully',
        'page' => $page,
    ], 200);
}


    public function destroy($id)
    {
        $page = Pages::findOrFail($id);

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully',
        ], 200);
    }
}
