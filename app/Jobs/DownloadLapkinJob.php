<?php

namespace App\Jobs;

use App\Helpers\GDriveHelper;
use App\Models\CkpKipapp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DownloadLapkinJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $bulan,
        public int $tahun,
        public string $gdriveUrl
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $downloadUrl = GDriveHelper::getDirectDownloadUrl($this->gdriveUrl);

        if (!$downloadUrl) {
            Log::error("Bulk Import: Invalid GDrive link for user {$this->userId}", ['url' => $this->gdriveUrl]);
            return;
        }

        try {
            $response = Http::timeout(60)->get($downloadUrl);

            if ($response->failed()) {
                Log::error("Bulk Import: Download failed for user {$this->userId}", [
                    'url' => $this->gdriveUrl,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return;
            }

            // Generate unique filename following the convention seen in the project
            $filename = 'ckp-documents/' . Str::upper(Str::random(26)) . '.pdf';
            
            // Save file to public disk
            Storage::disk('public')->put($filename, $response->body());

            // Create or update database record
            CkpKipapp::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'bulan' => $this->bulan,
                    'tahun' => $this->tahun,
                ],
                [
                    'nama_file' => $filename,
                ]
            );

        } catch (\Exception $e) {
            Log::error("Bulk Import: Exception during download for user {$this->userId}", [
                'message' => $e->getMessage(),
                'url' => $this->gdriveUrl
            ]);
        }
    }
}
