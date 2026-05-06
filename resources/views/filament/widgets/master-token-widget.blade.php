<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <x-filament::icon
                    icon="heroicon-o-key"
                    class="h-5 w-5 text-primary-600"
                />
                <span>Master Data API Access</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Gunakan fitur ini untuk membuat <strong>Personal Access Token</strong> khusus untuk integrasi antar-sistem (M2M). 
                Token ini akan memberikan akses penuh ke endpoint <code>/api/master/*</code>.
            </p>

            @if ($token)
                <div class="p-4 bg-success-50 dark:bg-success-950 border border-success-200 dark:border-success-800 rounded-lg">
                    <div class="flex flex-col space-y-2">
                        <span class="text-sm font-medium text-success-800 dark:text-success-200">
                            Token Berhasil Dibuat:
                        </span>
                        <div class="flex items-center space-x-2">
                            <code class="flex-1 p-2 bg-white dark:bg-gray-900 border border-success-300 dark:border-success-700 rounded text-xs break-all">
                                {{ $token }}
                            </code>
                            <x-filament::button
                                color="success"
                                size="sm"
                                icon="heroicon-o-clipboard-document"
                                x-on:click="window.navigator.clipboard.writeText('{{ $token }}'); $tooltip('Copied!', { timeout: 1500 })"
                            >
                                Copy
                            </x-filament::button>
                        </div>
                        <p class="text-[10px] text-success-600 dark:text-success-400 italic">
                            *Token hanya ditampilkan sekali ini saja. Segera simpan ke file .env aplikasi client!
                        </p>
                    </div>
                </div>
            @endif

            <div class="flex justify-start">
                @if (!$token)
                    <x-filament::button
                        wire:click="generateToken"
                        wire:confirm="Apakah Anda yakin ingin membuat token API Master baru? Token lama (jika ada) mungkin tetap aktif kecuali dihapus manual."
                        icon="heroicon-o-plus-circle"
                        color="primary"
                    >
                        Generate New Master API Token
                    </x-filament::button>
                @else
                    <x-filament::button
                        wire:click="$set('token', null)"
                        color="gray"
                        size="sm"
                    >
                        Tutup Panel Token
                    </x-filament::button>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
