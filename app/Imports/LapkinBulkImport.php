<?php

namespace App\Imports;

use App\Jobs\DownloadLapkinJob;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class LapkinBulkImport implements ToCollection, WithHeadingRow
{
    protected $userCache = null;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // Pre-cache users for better performance
        $this->cacheUsers();

        foreach ($rows as $row) {
            $timestamp = $row['timestamp'] ?? null;
            $namaRaw = $row['nama_pegawai'] ?? null;
            $tahunRaw = $row['tahun'] ?? null;
            $bulanRaw = $row['bulan'] ?? null;
            $gdriveUrl = $row['upload_file_lapkin'] ?? null;

            if (empty($namaRaw) || empty($gdriveUrl)) {
                continue;
            }

            // 1. Map Month
            $bulan = $this->mapMonth($bulanRaw);

            // 2. Map Year
            $tahun = $this->mapYear($tahunRaw, $timestamp);

            // 3. Find User by Name (Sanitized & Fuzzy)
            $user = $this->findUser($namaRaw);

            if (!$user) {
                Log::warning("Bulk Import: User not found for name: {$namaRaw}");
                continue;
            }

            // 4. Dispatch Job
            DownloadLapkinJob::dispatch(
                $user->id,
                $bulan,
                $tahun,
                $gdriveUrl
            );
        }
    }

    private function cacheUsers()
    {
        if ($this->userCache !== null) {
            return;
        }

        $this->userCache = [];
        $users = User::all();
        
        foreach ($users as $user) {
            $sanitized = $this->sanitizeName($user->name);
            $this->userCache[$sanitized] = $user;
        }
    }

    private function findUser($name)
    {
        $sanitizedSearchName = $this->sanitizeName($name);

        // 1. Exact match (sanitized)
        if (isset($this->userCache[$sanitizedSearchName])) {
            return $this->userCache[$sanitizedSearchName];
        }

        // 2. Fuzzy match: Substring or Word-based
        // To avoid false positives (e.g. "Budi" matching "Budi S." and "Budi A."),
        // we collect all potential matches and only return if there's exactly one strong candidate.
        $potentialMatches = [];
        foreach ($this->userCache as $sanitizedDbName => $user) {
            // Check if one contains the other
            if (str_contains($sanitizedSearchName, $sanitizedDbName) || str_contains($sanitizedDbName, $sanitizedSearchName)) {
                // Heuristic: The matched part should be significant (at least 4 chars or half of the name)
                if (strlen($sanitizedDbName) >= 4 || $sanitizedDbName === $sanitizedSearchName) {
                    $potentialMatches[] = $user;
                }
            }
        }

        if (count($potentialMatches) === 1) {
            return $potentialMatches[0];
        }

        return null;
    }

    private function mapMonth($monthRaw)
    {
        $map = [
            'January'   => 'Januari',
            'February'  => 'Februari',
            'March'     => 'Maret',
            'April'     => 'April',
            'May'       => 'Mei',
            'June'      => 'Juni',
            'July'      => 'Juli',
            'August'    => 'Agustus',
            'September' => 'September',
            'October'   => 'Oktober',
            'November'  => 'November',
            'December'  => 'Desember',
        ];

        return $map[trim($monthRaw)] ?? $monthRaw;
    }

    private function mapYear($tahunRaw, $timestamp)
    {
        if (!empty($tahunRaw) && is_numeric($tahunRaw)) {
            return (int)$tahunRaw;
        }

        // Try extract from timestamp (format: 4/1/2022 14:14:46)
        if (!empty($timestamp)) {
            try {
                $date = \Carbon\Carbon::parse($timestamp);
                return $date->year;
            } catch (\Exception $e) {
                return date('Y');
            }
        }

        return date('Y');
    }

    private function sanitizeName($name)
    {
        // 1. Lowercase
        $name = strtolower($name);

        // 2. Normalize common abbreviations at the start
        $name = preg_replace('/^m[\. ]/', 'muhamad ', $name);
        $name = preg_replace('/^muh[\. ]/', 'muhamad ', $name);
        $name = preg_replace('/^ach[\. ]/', 'achmad ', $name);

        // 3. Strip titles (common ones seen in DB)
        $titles = [
            's.t.', 'st', 's.si', 'ssi', 's.tr.stat', 'strstat', 
            's.stat', 's. stat', 'sstat', 's.pd', 'spd', 's.e.', 'se', 
            'm.m.', 'mm', 'm.si', 'msi', 'm.s.e.', 'mse',
            'a.md.bns', 'amdbns', 'a.md', 'amd', 
            'sst', 'sp', 'me', 'ir.', 'ir', 'dr.', 'dr',
            'm.stat', 'mstat', 'm.t.', 'mt', 'm.ec.dev', 'mecdev'
        ];

        // Replace titles with empty space
        foreach ($titles as $title) {
            $name = preg_replace('/\b' . preg_quote($title, '/') . '\b/i', '', $name);
        }

        // 4. Remove all non-alphanumeric (removes dots, commas, spaces)
        $name = preg_replace('/[^a-z0-9]/', '', $name);

        return $name;
    }
}
