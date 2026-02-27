<?php

namespace App\Observers;

use App\Models\PeriodeTahun;

class PeriodeTahunObserver
{
    /**
     * Handle the PeriodeTahun "saving" event.
     */
    public function saving(PeriodeTahun $periodeTahun): void
    {
        // Jika periode_tahun ini diset menjadi aktif
        if ($periodeTahun->is_active) {
            // Nonaktifkan semua periode_tahun yang lain
            PeriodeTahun::where('id', '!=', $periodeTahun->id)->update(['is_active' => false]);
        }
    }

    /**
     * Handle the PeriodeTahun "updated" event.
     */
    public function updated(PeriodeTahun $periodeTahun): void
    {
        //
    }

    /**
     * Handle the PeriodeTahun "deleted" event.
     */
    public function deleted(PeriodeTahun $periodeTahun): void
    {
        //
    }

    /**
     * Handle the PeriodeTahun "restored" event.
     */
    public function restored(PeriodeTahun $periodeTahun): void
    {
        //
    }

    /**
     * Handle the PeriodeTahun "force deleted" event.
     */
    public function forceDeleted(PeriodeTahun $periodeTahun): void
    {
        //
    }
}
