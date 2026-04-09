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
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

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
            Log::error("Bulk Import: Link GDrive tidak valid untuk user {$this->userId}", ['url' => $this->gdriveUrl]);
            return;
        }

        try {
            // Meningkatkan timeout ke 120 detik karena file PDF mungkin besar
            // Menggunakan withoutVerifying() jika ada masalah SSL cert pada environment tertentu
            $response = Http::timeout(120)
                ->withOptions(['verify' => false]) // Hindari masalah SSL EOF di beberapa server
                ->get($downloadUrl);

            if ($response->failed()) {
                Log::error("Bulk Import: Download gagal untuk user {$this->userId}", [
                    'url' => $this->gdriveUrl,
                    'status' => $response->status(),
                    'hint' => 'Pastikan share link GDrive bersifat Public/Anyone with the link'
                ]);
                
                // Melempar exception agar Laravel melakukan retry sesuai property $tries
                throw new \Exception("Download HTTP failed with status " . $response->status());
            }

            // Generate unique filename
            $filename = 'ckp-documents/' . Str::upper(Str::random(26)) . '.pdf';
            
            // Simpan file ke disk public
            Storage::disk('public')->put($filename, $response->body());

            // Create atau update record di database
            // Note: Pastikan koneksi DB mengarah ke server yang benar saat dijalankan
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
            Log::error("Bulk Import: Kesalahan saat memproses download untuk user {$this->userId}", [
                'message' => $e->getMessage(),
                'url' => $this->gdriveUrl
            ]);
            
            // Lempar kembali agar retry jalan
            throw $e;
        }
    }
}
