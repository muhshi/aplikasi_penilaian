<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CkpKipapp;
use Illuminate\Auth\Access\HandlesAuthorization;

class CkpKipappPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CkpKipapp');
    }

    public function view(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('View:CkpKipapp');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CkpKipapp');
    }

    public function update(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('Update:CkpKipapp');
    }

    public function delete(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('Delete:CkpKipapp');
    }

    public function restore(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('Restore:CkpKipapp');
    }

    public function forceDelete(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('ForceDelete:CkpKipapp');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CkpKipapp');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CkpKipapp');
    }

    public function replicate(AuthUser $authUser, CkpKipapp $ckpKipapp): bool
    {
        return $authUser->can('Replicate:CkpKipapp');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CkpKipapp');
    }

}