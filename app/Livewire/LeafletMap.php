<?php

namespace App\Livewire;

use Livewire\Component;

class LeafletMap extends Component
{
    public $latitude;
    public $longitude;

    protected $listeners = ['setCoordinates'];

    public function setCoordinates($latitude, $longitude)
    {
        // Update koordinat berdasarkan input dari map
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Emit ke Filament untuk update input
        $this->emit('coordinatesUpdated', $latitude, $longitude);
    }

    public function render()
    {
        return view('livewire.leaflet-map');
    }
}
