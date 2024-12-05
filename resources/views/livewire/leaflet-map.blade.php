<div>
    <div id="map" style="height: 400px;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([0, 0], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function(e) {
                var latitude = e.latlng.lat;
                var longitude = e.latlng.lng;

                // Masukkan nilai latitude dan longitude ke input form
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;

                // Menampilkan marker pada peta di posisi yang dipilih
                L.marker([latitude, longitude]).addTo(map);
            });
        });
    </script>
</div>
