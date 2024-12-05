<x-filament-panels::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}</h2>
        <p id="current-time" class="text-xl"></p>

        {{ $this->form }}

        <div id="map" style="height: 400px;"></div>

        <x-filament::button wire:click="checkIn">
            Check In
        </x-filament::button>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // (Sama seperti script sebelumnya, dengan penyesuaian untuk halaman Check In)
        </script>
    @endpush
</x-filament-panels::page>
