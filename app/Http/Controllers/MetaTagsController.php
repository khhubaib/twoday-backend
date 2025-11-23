<?php

namespace App\Http\Controllers;

use App\Models\Meta_tag;
use App\Models\Pages;
use Illuminate\Http\Request;
use Symfony\Component\Mime\Message;

class MetaTagsController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query'); 

        $tags = Meta_tag::where('page', 'like', "%{$query}%") // search for pages containing the query
            ->get();

        return response()->json([
            'message' => 'tags fetched successfully',
            'tags' => $tags
        ], 200);
    }


    public function index(Request $request)
    {
        $page = $request->query('page', 1); // default page = 1
        $perPage = 5;

        $tags = Meta_tag::offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'message' => 'tags fetched successfully',
            'tags' => $tags
        ], 200);
    }

     public function all(Request $request)
    {
        // Fetch all meta tags from the table
        $tags = Meta_tag::all(); // returns a collection of all rows

        return response()->json([
            'message' => 'Tags fetched successfully',
            'tags' => $tags
        ], 200);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'keywords' => [
                'nullable',
                'regex:/^(\s*[\w\s]+,)*\s*[\w\s]+$/'
            ],
            'canonical_url' => 'nullable|url',
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

    public function allWithStatus(Request $request)
{
    $query = Meta_tag::query();

    // Search by page or title if query param is present
    if ($request->has('query') && !empty($request->query('query'))) {
        $search = $request->query('query');
        $query->where(function($q) use ($search) {
            $q->where('page', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%");
        });
    }

    // Pagination
    $page = $request->query('page', 1);
    $pageSize = $request->query('pageSize', 5);

    $tags = $query->orderBy('id', 'desc')
                  ->skip(($page - 1) * $pageSize)
                  ->take($pageSize)
                  ->get();

    // Add status field
    $tagsWithStatus = $tags->map(function($tag) {
        $activeFields = ['title', 'description', 'keywords', 'canonical_url'];
        $isActive = false;

        foreach ($activeFields as $field) {
            if (!empty($tag->$field)) {
                $isActive = true;
                break;
            }
        }

        $tag->status = $isActive ? 'Active' : 'Non-Active';
        return $tag;
    });

    return response()->json([
        'tags' => $tagsWithStatus
    ]);
}



public function countStatus()
{
    // Get all tags
    $tags = Meta_tag::all();

    // Count active and inactive
    $activeCount = $tags->filter(function($tag) {
        // A tag is active if title, description, keywords, canonical_url are all non-null
        return $tag->title && $tag->description && $tag->keywords && $tag->canonical_url;
    })->count();

    $nonActiveCount = $tags->count() - $activeCount;

    // Count of pages
    $totalPages = $tags->pluck('page')->unique()->count();

    return response()->json([
        'message'=> 'count fetched successfully',
        'total_pages' => $totalPages,
        'active_tags' => $activeCount,
        'non_active_tags' => $nonActiveCount,
    ]);
}

}
