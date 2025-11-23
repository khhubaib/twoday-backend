<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaTagsController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Http\Request;



Route::post('/pages', [PagesController::class, 'store']);
Route::get('/pages', [PagesController::class, 'index']);
Route::put('/pages/{id}', [PagesController::class, 'update']);
Route::delete('/pages/{id}', [PagesController::class, 'destroy']);


Route::post('/metatags', [MetaTagsController::class, 'store']);
Route::put('/metatags/{id}', [MetaTagsController::class, 'update']);
Route::get('/pages/{id}/tags', [MetaTagsController::class, 'getTagsByPage']);
Route::get('/metatags/{id}', [MetaTagsController::class, 'show']);
Route::delete('/metatags/{id}', [MetaTagsController::class, 'destroy']);

Route::get('/tags/search', [MetaTagsController::class, 'search']);

Route::get('/tags', [MetaTagsController::class, 'index']);
Route::get('/tags/all', [MetaTagsController::class, 'all']);

Route::get('/tags/all-status', [MetaTagsController::class, 'allWithStatus']);


Route::get('/tags/count-status', [MetaTagsController::class, 'countStatus']);





// --- LOGIN ---
Route::post('/admin/login', function (Request $req) {
    $req->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $admin = Admin::where('email', $req->email)->first();

    if (!$admin || !Hash::check($req->password, $admin->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // generate token (store single active token)
    $token = Str::random(60);
    $admin->update(['api_token' => hash('sha256', $token)]);

    // return plain token to client (store unhashed in client)
    return response()->json(['token' => $token, 'admin' => $admin->only(['id','name','email'])]);
});

// --- HELPER: token check function (closure) ---
$checkToken = function (Request $req) {
    $header = $req->header('Authorization', '');
    // accept both "Bearer <token>" and raw token
    $token = str_starts_with($header, 'Bearer ') ? substr($header, 7) : $header;
    if (!$token) {
        return null;
    }
    $hashed = hash('sha256', $token);
    return Admin::where('api_token', $hashed)->first();
};

// --- GET CURRENT ADMIN ---
Route::get('/admin/me', function (Request $req) use ($checkToken) {
    $admin = $checkToken($req);
    if (!$admin) return response()->json(['message' => 'Unauthorized'], 401);
    return response()->json(['admin' => $admin->only(['id','name','email'])]);
});

// --- LOGOUT (revoke token) ---
Route::post('/admin/logout', function (Request $req) use ($checkToken) {
    $admin = $checkToken($req);
    if (!$admin) return response()->json(['message' => 'Unauthorized'], 401);
    $admin->update(['api_token' => null]);
    return response()->json(['message' => 'Logged out']);
});

// --- PROTECT YOUR ADMIN ROUTES INLINE ---
// Example: tags/index
Route::get('/tags', function (Request $req) use ($checkToken) {
    $admin = $checkToken($req);
    if (!$admin) return response()->json(['message' => 'Unauthorized'], 401);

    // your existing code to return tags; adjust to your model
    // if your tags are in pages/tags table, adapt accordingly.
    return app(MetaTagsController::class)->index($req); // or ->get()
});