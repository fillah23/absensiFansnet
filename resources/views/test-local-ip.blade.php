<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Local IP Detection</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <style>
        .ip-box {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
        }
        .ip-public { background-color: #f8d7da; border: 2px solid #dc3545; }
        .ip-local { background-color: #d1e7dd; border: 2px solid #198754; }
        .ip-loading { background-color: #fff3cd; border: 2px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔍 Test Deteksi IP Lokal (WiFi)</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Tujuan:</strong> Mendeteksi IP WiFi lokal Anda (172.22.4.x) meskipun akses via Cloudflare
                        </div>

                        <h5>📡 IP yang Terdeteksi:</h5>
                        
                        <div class="ip-box ip-public">
                            <strong>IP Publik (dari Server):</strong> 
                            <span id="public-ip">Loading...</span>
                            <br><small>Ini IP yang dilihat Cloudflare (IP ISP Anda)</small>
                        </div>

                        <div class="ip-box ip-loading" id="local-ip-box">
                            <strong>IP Lokal WiFi (dari Browser):</strong> 
                            <span id="local-ip">Detecting...</span>
                            <br><small>Ini IP yang dibutuhkan untuk validasi absensi</small>
                        </div>

                        <div class="alert alert-warning mt-3" id="alert-box" style="display:none;">
                            <h6>⚠️ Masalah Terdeteksi</h6>
                            <p id="alert-message"></p>
                        </div>

                        <div class="alert alert-success mt-3" id="success-box" style="display:none;">
                            <h6>✅ IP Lokal Terdeteksi!</h6>
                            <p id="success-message"></p>
                        </div>

                        <hr>

                        <h5>🔧 Cara Implementasi:</h5>
                        <ol>
                            <li>Browser mendeteksi IP lokal menggunakan WebRTC</li>
                            <li>IP lokal dikirim ke server via custom header <code>X-Client-Local-IP</code></li>
                            <li>Server menggunakan IP lokal untuk validasi, bukan IP publik</li>
                        </ol>

                        <div class="mt-3">
                            <a href="/check-ip" class="btn btn-primary" target="_blank">Cek Detail IP</a>
                            <a href="/" class="btn btn-secondary">Kembali ke Absensi</a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Semua IP yang Terdeteksi</h5>
                    </div>
                    <div class="card-body">
                        <pre id="all-ips" style="background: #f5f5f5; padding: 15px; border-radius: 5px;">Loading...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get public IP from server
        fetch('/check-ip')
            .then(response => response.json())
            .then(data => {
                document.getElementById('public-ip').textContent = data.PUBLIC_IP || data.FINAL_IP_USED_FOR_VALIDATION;
                document.getElementById('all-ips').textContent = JSON.stringify(data, null, 2);
            })
            .catch(err => {
                document.getElementById('public-ip').textContent = 'Error: ' + err.message;
            });

        // Detect local IP using WebRTC
        function getLocalIPs(callback) {
            const ips = [];
            const RTCPeerConnection = window.RTCPeerConnection || 
                                     window.mozRTCPeerConnection || 
                                     window.webkitRTCPeerConnection;
            
            if (!RTCPeerConnection) {
                callback(['WebRTC not supported']);
                return;
            }

            const pc = new RTCPeerConnection({
                iceServers: [{urls: 'stun:stun.l.google.com:19302'}]
            });

            pc.createDataChannel('');
            pc.createOffer()
                .then(offer => pc.setLocalDescription(offer))
                .catch(err => console.error('Error creating offer:', err));

            pc.onicecandidate = (ice) => {
                if (!ice || !ice.candidate || !ice.candidate.candidate) {
                    return;
                }

                const parts = ice.candidate.candidate.split(' ');
                const ip = parts[4];

                if (ip && ips.indexOf(ip) === -1) {
                    ips.push(ip);
                }
            };

            // Wait for candidates
            setTimeout(() => {
                pc.close();
                callback(ips);
            }, 2000);
        }

        // Execute detection
        getLocalIPs((ips) => {
            const localIpBox = document.getElementById('local-ip-box');
            const localIpSpan = document.getElementById('local-ip');
            const alertBox = document.getElementById('alert-box');
            const alertMessage = document.getElementById('alert-message');
            const successBox = document.getElementById('success-box');
            const successMessage = document.getElementById('success-message');

            if (ips.length === 0 || ips[0] === 'WebRTC not supported') {
                localIpSpan.textContent = 'Tidak dapat mendeteksi (WebRTC tidak tersedia)';
                localIpBox.className = 'ip-box ip-public';
                alertBox.style.display = 'block';
                alertMessage.innerHTML = `
                    <strong>WebRTC tidak tersedia atau diblokir.</strong><br>
                    Alternatif: Whitelist IP publik ISP Anda atau gunakan akses langsung tanpa Cloudflare.
                `;
                return;
            }

            // Filter hanya IP lokal (192.168.x.x, 172.x.x.x, 10.x.x.x)
            const localIPs = ips.filter(ip => {
                return ip.startsWith('192.168.') || 
                       ip.startsWith('172.') || 
                       ip.startsWith('10.') ||
                       ip.startsWith('169.254.'); // Link-local
            });

            const wifiIP = localIPs.find(ip => ip.startsWith('172.22.4.')) || localIPs[0];

            if (wifiIP) {
                localIpSpan.textContent = wifiIP;
                localIpBox.className = 'ip-box ip-local';
                
                if (wifiIP.startsWith('172.22.4.')) {
                    successBox.style.display = 'block';
                    successMessage.innerHTML = `
                        <strong>IP WiFi kantor terdeteksi: ${wifiIP}</strong><br>
                        IP ini dapat digunakan untuk validasi absensi!
                    `;
                } else {
                    alertBox.style.display = 'block';
                    alertMessage.innerHTML = `
                        <strong>IP lokal terdeteksi (${wifiIP}), tapi bukan range WiFi kantor (172.22.4.x)</strong><br>
                        Pastikan Anda terhubung ke WiFi kantor yang benar.
                    `;
                }

                // Show all detected IPs
                if (localIPs.length > 1) {
                    localIpSpan.textContent += ` (Total: ${localIPs.length} IP ditemukan)`;
                    localIpSpan.title = localIPs.join(', ');
                }
            } else {
                localIpSpan.textContent = ips.join(', ') + ' (Tidak ada IP lokal terdeteksi)';
                localIpBox.className = 'ip-box ip-public';
                alertBox.style.display = 'block';
                alertMessage.innerHTML = `
                    <strong>Hanya IP publik yang terdeteksi.</strong><br>
                    Kemungkinan: VPN aktif, atau browser memblokir WebRTC local IP detection.
                `;
            }
        });
    </script>
</body>
</html>
