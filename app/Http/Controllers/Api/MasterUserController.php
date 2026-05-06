<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasterUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller untuk Master Data API — diakses oleh aplikasi eksternal (M2M).
 * 
 * Implementasi mengikuti standar Sipetra untuk sinkronisasi data antar sistem.
 */
class MasterUserController extends Controller
{
    /**
     * GET /api/master/users
     * 
     * Mengembalikan daftar pengguna dengan pagination dan filter incremental.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'updated_after' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:500'],
            'page'          => ['nullable', 'integer', 'min:1'],
        ]);

        $query = User::query()->with('pegawai')->orderBy('id');

        // Incremental sync
        if ($request->filled('updated_after')) {
            $query->where('updated_at', '>', $request->input('updated_after'));
        }

        $perPage = (int) $request->input('per_page', 100);
        $users   = $query->paginate($perPage);

        return MasterUserResource::collection($users)
            ->additional([
                'synced_at' => now()->toIso8601String(),
            ]);
    }

    /**
     * GET /api/master/users/{id}
     */
    public function show(User $user): MasterUserResource
    {
        $user->load('pegawai');
        return new MasterUserResource($user);
    }
}
