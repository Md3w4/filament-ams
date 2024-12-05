<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@2.0.2/build/global/luxon.min.js"></script>
    <style>
        #map {
            height: 400px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <header class="bg-white shadow rounded-lg p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Sistem Presensi</h1>
            <p class="text-gray-600">Selamat datang, {{ $user->name }}</p>
        </header>

        <main class="bg-white shadow rounded-lg p-6">
            @if (!$schedule)
                <div class="text-red-500 text-center py-4">
                    Anda tidak memiliki jadwal hari ini
                </div>
            @else
                <form id="attendanceForm" class="mb-6">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                    <div class="mb-4">
                        <p class="text-gray-700"><strong>Kantor:</strong> {{ $schedule->office->name }}</p>
                        <p class="text-gray-700"><strong>Shift:</strong> {{ $schedule->shift->name }}</p>
                        <p class="text-gray-700">
                            <strong>Jam Kerja:</strong>
                            {{ $schedule->shift->start_time->format('H:i') }} -
                            {{ $schedule->shift->end_time->format('H:i') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-700">
                            <strong>Status Presensi:</strong>
                            @if (!$lastAttendance)
                                Belum Absen
                            @elseif(!$lastAttendance->time_out)
                                Sudah Absen Masuk ({{ $lastAttendance->time_in->format('H:i') }})
                            @else
                                Sudah Absen Pulang
                            @endif
                        </p>
                    </div>

                    @if ($overtimeRequest && $overtimeRequest->status === \App\Models\OvertimeRequestStatus::APPROVED)
                        <div class="mb-4 p-4 bg-green-100 rounded">
                            <p class="text-green-700">
                                <strong>Lembur Disetujui:</strong><br>
                                {{ $overtimeRequest->estimated_start_time->format('H:i') }} -
                                {{ $overtimeRequest->estimated_end_time->format('H:i') }}
                            </p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-gray-700"><strong>Waktu Sekarang:</strong> <span id="currentTime"></span></p>
                    </div>

                    <div id="map" class="mb-4 rounded-lg shadow"></div>

                    <div class="flex items-center justify-between">
                        <button id="submitAttendance" type="button"
                            @if (!$lastAttendance || ($lastAttendance && !$lastAttendance->time_out)) class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @else
                                class="bg-gray-400 text-white font-bold py-2 px-4 rounded" disabled @endif>
                            {{ !$lastAttendance ? 'Absen Masuk' : ($lastAttendance->time_out ? 'Sudah Absen' : 'Absen Pulang') }}
                        </button>
                        <p id="statusMessage" class="text-sm text-gray-600"></p>
                    </div>
                </form>
            @endif
        </main>
    </div>

    <script>
        let map, marker, circle;
        let userLat, userLng;

        function initMap() {
            const office = {!! json_encode($schedule?->office) !!};
            if (!office) return;

            map = L.map('map').setView([office.latitude, office.longitude], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            circle = L.circle([office.latitude, office.longitude], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.2,
                radius: office.radius
            }).addTo(map);
        }

        function updateLocation(position) {
            userLat = position.coords.latitude;
            userLng = position.coords.longitude;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([userLat, userLng]).addTo(map);
            map.setView([userLat, userLng], 15);

            document.getElementById('statusMessage').textContent = 'Lokasi Anda terdeteksi.';
        }

        function handleLocationError(error) {
            console.error("Error: " + error.message);
            document.getElementById('statusMessage').textContent = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
        }

        function updateCurrentTime() {
            const now = luxon.DateTime.local().setZone('Asia/Jakarta');
            document.getElementById('currentTime').textContent = now.toFormat('dd-MM-yyyy HH:mm:ss');
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            updateCurrentTime();
            setInterval(updateCurrentTime, 1000);

            if ("geolocation" in navigator) {
                navigator.geolocation.watchPosition(updateLocation, handleLocationError);
            } else {
                document.getElementById('statusMessage').textContent = 'Geolokasi tidak didukung di perangkat ini.';
            }

            const submitButton = document.getElementById('submitAttendance');
            if (submitButton) {
                submitButton.addEventListener('click', function() {
                    const scheduleId = document.querySelector('input[name="schedule_id"]').value;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Mengirim...';

                    const statusMessage = document.getElementById('statusMessage');
                    statusMessage.textContent = 'Sedang mengirim presensi...';

                    fetch('{{ route('attendance.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                schedule_id: scheduleId,
                                latitude: userLat,
                                longitude: userLng
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            statusMessage.textContent = data.message;
                            if (data.message.includes('berhasil')) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            statusMessage.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            submitButton.textContent = 'Kirim Presensi';
                        });
                });
            }
        });
    </script>
</body>

</html>
