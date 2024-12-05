<div id="map" style="height: 400px;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([0, 0], 13); // Set view to default coordinates

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker to map when clicked
        var marker;
        map.on('click', function (e) {
            var latitude = e.latlng.lat;
            var longitude = e.latlng.lng;

            // Update the latitude and longitude input fields
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;

            // Remove previous marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([latitude, longitude]).addTo(map);
        });
    });
</script>
