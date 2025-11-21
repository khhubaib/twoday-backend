<?php

namespace App\Http\Controllers;

use App\Models\Meta_tag;
use App\Models\Pages;
use Illuminate\Http\Request;

class MetaTagsController extends Controller
{

    public function index()
    {
        $tags = Meta_tag::all(); // Fetch all records from meta_tags

        return response()->json([
            'message' => 'tags fetched successfully',
            'tags' => $tags
        ], 200);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'keywords' => [
                'required',
                'regex:/^(\s*[\w\s]+,)*\s*[\w\s]+$/'
            ],
            'canonical_url' => 'required|url',
        ]);



        $tags = Meta_tag::create($validated);

        return response()->json([
            'message' => 'Tag created successfully',
            'tag' => $tags,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tag = Meta_tag::findOrFail($id);

        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'keywords' => ['required', 'regex:/^(\s*[\w\s]+,)*\s*[\w\s]+$/'],
            'canonical_url' => 'required|url',
        ]);

        $tag->update($validated);

        return response()->json([
            'message' => 'Tag updated successfully',
            'tag' => $tag,
        ]);
    }


    public function getTagsByPage($id)
    {
        // Option 1: via relation (recommended)
        $page = Pages::with('metatags')->findOrFail($id);

        return response()->json([
            'page_id' => $page->id,
            'page_name' => $page->page,
            'tags' => $page->metatags,
        ]);
    }

    public function show($id)
    {
        $tag = Meta_tag::findOrFail($id);
        return response()->json($tag);
    }


    public function destroy($id)
    {
        $tag = Meta_tag::findOrFail($id);
        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ], 200);
    }
}
