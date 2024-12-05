<!-- resources/views/filament/pages/attendance-check-out.blade.php -->
<x-filament-panels::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold">Ready to check out, {{ Auth::user()->name }}?</h2>
        <p id="current-time" class="text-xl"></p>

        <div id="map" style="height: 400px;"></div>

        <x-filament::button wire:click="checkOut">
            Check Out
        </x-filament::button>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // (Sama seperti script sebelumnya, dengan penyesuaian untuk halaman Check Out)
        </script>
    @endpush
</x-filament-panels::page>
