<?php

namespace App\Http\Controllers;

use App\Models\Meta_tag;
use App\Models\Pages;
use Illuminate\Http\Request;

class MetaTagsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_id' => 'required|exists:pages,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'keywords' => 'required|string|max:255',
            'canonical_url' => 'required|string|max:255',
        ]);

        // fetch page name
        $page = Pages::findOrFail($validated['page_id']);
        $validated['page'] = $page->page;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'keywords' => 'required|string|max:255',
            'canonical_url' => 'required|string|max:255',
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
