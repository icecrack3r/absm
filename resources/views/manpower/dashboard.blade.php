<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manpower</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <img src="{{ asset('storage/' . $manpower->projek->logo_projek) }}" style="max-height: 50px;" class="img-fluid"">
                </div>
                <form method="POST" action="{{ route('manpower.logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Live Clock -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-center">
            <h2 class="text-xl font-semibold mb-2">Waktu Saat Ini</h2>
            <div id="live-clock" class="text-3xl font-bold text-blue-600"></div>
            <div id="live-date" class="text-lg text-gray-600"></div>
            <p class="text-gray-600">NIP: {{ $manpower->nip }} | {{ $manpower->nama_lengkap }}</p>
        </div>

        <!-- Status Absensi -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Status Absensi Hari Ini</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Check In</p>
                    <p id="check-in-status" class="text-lg font-semibold">
                        @if($absensi && $absensi->jam_check_in)
                            {{ $absensi->jam_check_in }}
                        @else
                            Belum Check In
                        @endif
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Check Out</p>
                    <p id="check-out-status" class="text-lg font-semibold">
                        @if($absensi && $absensi->jam_check_out)
                            {{ $absensi->jam_check_out }}
                        @else
                            Belum Check Out
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Jadwal Absensi -->
        @if($jadwal)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Jadwal Absensi Hari Ini</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Jam Masuk</p>
                    <p class="text-lg font-semibold">{{ $jadwal->jam_check_in }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jam Keluar</p>
                    <p class="text-lg font-semibold">{{ $jadwal->jam_check_out }}</p>
                </div>
            </div>
            <div class="mt-4">
                <!-- <p class="text-sm text-gray-600">Lokasi Absensi</p> -->
                <!-- <p class="text-sm">Lat: {{ $jadwal->latitude }}, Long: {{ $jadwal->longitude }}</p> -->
                <!-- <p class="text-sm">Radius: {{ $jadwal->radius_meter }} meter</p> -->
            </div>
        </div>
        @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <p>Tidak ada jadwal absensi untuk hari ini.</p>
        </div>
        @endif

        <!-- Tombol Absensi -->
        @if($jadwal)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Absensi</h3>
            <div class="grid grid-cols-2 gap-4">
                <button id="check-in-btn" 
                        onclick="handleCheckIn()"
                        class="bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        @if($absensi && $absensi->jam_check_in) disabled @endif>
                    Check In
                </button>
                
                <button id="check-out-btn" 
                        onclick="handleCheckOut()"
                        class="bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        @if(!$absensi || !$absensi->jam_check_in || ($absensi && $absensi->jam_check_out)) disabled @endif>
                    Check Out
                </button>
            </div>
        </div>
        @endif

        <!-- Alert Messages -->
        <div id="alert-container" class="fixed top-4 right-4 z-50"></div>
    </div>

    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            document.getElementById('live-clock').textContent = timeString;
            document.getElementById('live-date').textContent = dateString;
        }

        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call

        // Location functions
        let currentLocation = null;

        function getCurrentLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Geolocation tidak didukung oleh browser ini.'));
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    (error) => {
                        reject(new Error('Gagal mendapatkan lokasi: ' + error.message));
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            
            let bgColor = 'bg-blue-500';
            if (type === 'success') bgColor = 'bg-green-500';
            if (type === 'error') bgColor = 'bg-red-500';
            if (type === 'warning') bgColor = 'bg-yellow-500';
            
            alertDiv.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg mb-4 max-w-sm`;
            alertDiv.textContent = message;
            
            alertContainer.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        async function handleCheckIn() {
            try {
                showAlert('Mendapatkan lokasi...', 'info');
                const location = await getCurrentLocation();
                
                const response = await fetch('{{ route("manpower.check-in") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: location.latitude,
                        longitude: location.longitude
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    document.getElementById('check-in-btn').disabled = true;
                    document.getElementById('check-out-btn').disabled = false;
                    document.getElementById('check-in-status').textContent = new Date().toLocaleTimeString('id-ID');
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert(error.message, 'error');
            }
        }

        async function handleCheckOut() {
            try {
                showAlert('Mendapatkan lokasi...', 'info');
                const location = await getCurrentLocation();
                
                const response = await fetch('{{ route("manpower.check-out") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: location.latitude,
                        longitude: location.longitude
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    document.getElementById('check-out-btn').disabled = true;
                    document.getElementById('check-out-status').textContent = new Date().toLocaleTimeString('id-ID');
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert(error.message, 'error');
            }
        }

        // Request location permission on page load
        window.addEventListener('load', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    () => showAlert('Lokasi berhasil diaktifkan', 'success'),
                    () => showAlert('Harap aktifkan izin lokasi untuk menggunakan fitur absensi', 'warning')
                );
            }
        });
    </script>
</body>
</html>