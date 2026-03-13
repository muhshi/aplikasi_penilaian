<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NilaiKipapp;
use Illuminate\Auth\Access\HandlesAuthorization;

class NilaiKipappPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NilaiKipapp');
    }

    public function view(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('View:NilaiKipapp');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NilaiKipapp');
    }

    public function update(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('Update:NilaiKipapp');
    }

    public function delete(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('Delete:NilaiKipapp');
    }

    public function restore(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('Restore:NilaiKipapp');
    }

    public function forceDelete(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('ForceDelete:NilaiKipapp');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NilaiKipapp');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NilaiKipapp');
    }

    public function replicate(AuthUser $authUser, NilaiKipapp $nilaiKipapp): bool
    {
        return $authUser->can('Replicate:NilaiKipapp');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NilaiKipapp');
    }

}