<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NilaiPegawai;
use Illuminate\Auth\Access\HandlesAuthorization;

class NilaiPegawaiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NilaiPegawai');
    }

    public function view(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('View:NilaiPegawai');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NilaiPegawai');
    }

    public function update(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('Update:NilaiPegawai');
    }

    public function delete(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('Delete:NilaiPegawai');
    }

    public function restore(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('Restore:NilaiPegawai');
    }

    public function forceDelete(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('ForceDelete:NilaiPegawai');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NilaiPegawai');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NilaiPegawai');
    }

    public function replicate(AuthUser $authUser, NilaiPegawai $nilaiPegawai): bool
    {
        return $authUser->can('Replicate:NilaiPegawai');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NilaiPegawai');
    }

}