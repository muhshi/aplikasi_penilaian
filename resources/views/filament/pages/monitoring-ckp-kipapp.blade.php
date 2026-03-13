<x-filament-panels::page>
    <div class="monitoring-wrapper">
        {{-- Monitoring Table --}}
        <div class="monitoring-table-container">
            <table class="monitoring-table">
                <thead>
                    {{-- Row 1: Year dropdown spanning all period columns --}}
                    <tr>
                        <th rowspan="2" class="monitoring-th monitoring-th-no">No</th>
                        <th rowspan="2" class="monitoring-th monitoring-th-name">Nama Pegawai</th>
                        <th colspan="15" class="monitoring-th monitoring-th-year">
                            <div class="monitoring-year-dropdown">
                                <span>Tahun</span>
                                <select wire:model.live="tahun" class="monitoring-year-select">
                                    @foreach ($this->availableYears as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </th>
                    </tr>
                    {{-- Row 2: Individual period columns --}}
                    <tr>
                        @foreach ($periods as $period)
                            <th class="monitoring-th monitoring-th-period">{{ $period }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (empty($this->monitoringData))
                        <tr>
                            <td colspan="17" class="monitoring-td monitoring-td-empty">
                                Tidak ada data pegawai.
                            </td>
                        </tr>
                    @else
                        @foreach ($this->monitoringData as $index => $row)
                            <tr class="monitoring-tr">
                                <td class="monitoring-td monitoring-td-no">{{ $index + 1 }}</td>
                                <td class="monitoring-td monitoring-td-name">{{ $row['name'] }}</td>
                                @foreach ($periods as $period)
                                    <td class="monitoring-td monitoring-td-status">
                                        @if ($row['status'][$period])
                                            <x-heroicon-o-check-circle class="monitoring-icon-check" />
                                        @else
                                            <x-heroicon-o-x-circle class="monitoring-icon-cross" />
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monitoring Nilai Pegawai per Ketua Tim --}}
    <div class="monitoring-wrapper" style="margin-top: 2rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; color: #1f2937;">
            Monitoring Nilai Pegawai per Ketua Tim
        </h2>
        <div class="monitoring-table-container">
            <table class="monitoring-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="monitoring-th monitoring-th-no">No</th>
                        <th rowspan="2" class="monitoring-th monitoring-th-name">Nama Ketua Tim</th>
                        <th colspan="12" class="monitoring-th monitoring-th-year">
                            <span>Status Penilaian Tahun {{ $tahun }}</span>
                        </th>
                    </tr>
                    <tr>
                        @foreach ($bulanList as $bulanNum => $bulanName)
                            <th class="monitoring-th monitoring-th-period">{{ $bulanName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (empty($this->monitoringNilaiData))
                        <tr>
                            <td colspan="14" class="monitoring-td monitoring-td-empty">
                                Tidak ada data ketua tim.
                            </td>
                        </tr>
                    @else
                        @foreach ($this->monitoringNilaiData as $index => $row)
                            <tr class="monitoring-tr">
                                <td class="monitoring-td monitoring-td-no">{{ $index + 1 }}</td>
                                <td class="monitoring-td monitoring-td-name">{{ $row['name'] }}</td>
                                @foreach ($bulanList as $bulanNum => $bulanName)
                                    <td class="monitoring-td monitoring-td-status"
                                        title="{{ $row['status'][$bulanName]['sudah'] }}/{{ $row['status'][$bulanName]['total'] }} pegawai dinilai">
                                        @if ($row['status'][$bulanName]['selesai'])
                                            <x-heroicon-o-check-circle class="monitoring-icon-check" />
                                        @elseif ($row['status'][$bulanName]['sudah'] > 0)
                                            <span style="font-size: 0.65rem; color: #f59e0b; font-weight: 600;">
                                                {{ $row['status'][$bulanName]['sudah'] }}/{{ $row['status'][$bulanName]['total'] }}
                                            </span>
                                        @else
                                            <x-heroicon-o-x-circle class="monitoring-icon-cross" />
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monitoring Progres Penilaian Pegawai --}}
    <div class="monitoring-wrapper" style="margin-top: 2rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; color: #1f2937;">
            Monitoring Progres Penilaian Pegawai
        </h2>
        <div class="monitoring-table-container">
            <table class="monitoring-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="monitoring-th monitoring-th-no">No</th>
                        <th rowspan="2" class="monitoring-th monitoring-th-name">Nama Pegawai</th>
                        <th colspan="12" class="monitoring-th monitoring-th-year">
                            <span>Status Progres Tahun {{ $tahun }}</span>
                        </th>
                    </tr>
                    <tr>
                        @foreach ($bulanList as $bulanNum => $bulanName)
                            <th class="monitoring-th monitoring-th-period">
                                {{ $bulanName }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (empty($this->monitoringProgresData))
                        <tr>
                            <td colspan="14" class="monitoring-td monitoring-td-empty">
                                Tidak ada data pegawai.
                            </td>
                        </tr>
                    @else
                        @foreach ($this->monitoringProgresData as $index => $row)
                            <tr class="monitoring-tr">
                                <td class="monitoring-td monitoring-td-no">{{ $index + 1 }}</td>
                                <td class="monitoring-td monitoring-td-name">{{ $row['name'] }}</td>
                                @foreach ($bulanList as $bulanNum => $bulanName)
                                    @php
                                        $data = $row['status'][$bulanName];
                                    @endphp
                                    <td class="monitoring-td monitoring-td-status"
                                        title="{{ $data['sudah'] }}/{{ $data['total'] }} penilai sudah menilai">
                                        @if ($data['lengkap'])
                                            <x-heroicon-o-check-circle style="width: 1.25rem; height: 1.25rem; color: #eab308; display: inline-block;" />
                                        @else
                                            <span style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">
                                                {{ $data['sudah'] }}/{{ $data['total'] }}
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .monitoring-wrapper {
            width: 100%;
        }

        .monitoring-table-container {
            width: 100%;
            overflow-x: auto;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .dark .monitoring-table-container {
            border-color: #374151;
        }

        .monitoring-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        /* ---- Header ---- */
        .monitoring-th {
            background-color: #0A2540;
            color: #ffffff;
            padding: 0.55rem 0.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            border: 1px solid #1a3a5c;
        }

        .monitoring-th-no {
            width: 3rem;
        }

        .monitoring-th-name {
            min-width: 10rem;
            text-align: left;
            padding-left: 0.75rem;
        }

        .monitoring-th-year {
            padding: 0.5rem;
        }

        .monitoring-year-dropdown {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            letter-spacing: 0.025em;
        }

        .monitoring-year-select {
            padding: 0.25rem 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.375rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            transition: border-color 0.15s;
        }

        .monitoring-year-select:focus {
            border-color: rgba(255, 255, 255, 0.6);
        }

        .monitoring-year-select option {
            background-color: #0A2540;
            color: #fff;
        }

        .monitoring-th-period {
            font-size: 0.7rem;
            padding: 0.5rem 0.35rem;
            min-width: 4.5rem;
        }

        /* ---- Body ---- */
        .monitoring-tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .dark .monitoring-tr:nth-child(even) {
            background-color: #1f2937;
        }

        .monitoring-tr:hover {
            background-color: #f0f4f8;
        }

        .dark .monitoring-tr:hover {
            background-color: #2d3748;
        }

        .monitoring-td {
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            color: #374151;
        }

        .dark .monitoring-td {
            border-color: #374151;
            color: #d1d5db;
        }

        .monitoring-td-no {
            font-weight: 500;
            width: 3rem;
        }

        .monitoring-td-name {
            text-align: left;
            padding-left: 0.75rem;
            font-weight: 500;
        }

        .monitoring-td-status {
            padding: 0.35rem;
        }

        .monitoring-td-empty {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
            font-style: italic;
        }

        /* ---- Heroicon Check / Cross ---- */
        .monitoring-icon-check {
            width: 1.25rem;
            height: 1.25rem;
            color: #16a34a;
            display: inline-block;
        }

        .monitoring-icon-cross {
            width: 1.25rem;
            height: 1.25rem;
            color: #dc2626;
            display: inline-block;
        }
    </style>
</x-filament-panels::page>