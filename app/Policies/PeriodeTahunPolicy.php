<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PeriodeTahun;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeriodeTahunPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PeriodeTahun');
    }

    public function view(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('View:PeriodeTahun');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PeriodeTahun');
    }

    public function update(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('Update:PeriodeTahun');
    }

    public function delete(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('Delete:PeriodeTahun');
    }

    public function restore(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('Restore:PeriodeTahun');
    }

    public function forceDelete(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('ForceDelete:PeriodeTahun');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PeriodeTahun');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PeriodeTahun');
    }

    public function replicate(AuthUser $authUser, PeriodeTahun $periodeTahun): bool
    {
        return $authUser->can('Replicate:PeriodeTahun');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PeriodeTahun');
    }

}