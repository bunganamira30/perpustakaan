# Tutorial Ubuntu untuk Tugas Perpustakaan Mini Aman (SIP-Aman)

Panduan teknis bertahap untuk **Bunga Namira (NIM: 230170120)**, berdasarkan dokumen tugas resmi dosen Keamanan Sistem Komputer dan spesifikasi arsitektur proyek **Sistem Informasi Perpustakaan Mini Aman (SIP-Aman)**. Ini adalah panduan kerja sistem (*execution manual*) untuk memandu pengerjaan domain infrastruktur Linux (Tiket TKT-013 s/d TKT-016), pengujian lab interaktif (TKT-017 s/d TKT-020), dan penyusunan deliverable akhir.

**Cara Mengikuti:** Kerjakan langkah-langkah secara berurutan, satu blok perintah pada satu waktu. Setiap judul seperti `6.2.1` adalah nomor langkah operasional; jika suatu langkah sudah terbukti selesai, jangan mengulang perintah yang membuat atau menimpa file. Bagian 8–10 memuat **rencana pengujian dan checklist pembuktian** yang wajib dicatat hasilnya secara nyata ke dalam laporan.

---

## Peta Tugas Dosen: Apa yang Sedang Dikerjakan?

### Gambaran Tugas
Sesuai Bagian 1 dokumen tugas, Anda berperan sebagai **Security Engineer**: mengonfigurasi, menggelar (*deploy*), dan mengamankan aplikasi sistem informasi perpustakaan di server VM Ubuntu Server 24.04 LTS. Digit terakhir NIM Anda adalah **0**, sehingga penugasan spesifik Anda adalah:
- **Tema Proyek:** Perpustakaan (*Library Management System* / SIP-Aman).
- **Data Sensitif Terenkripsi (Syarat 3):** Nomor telepon anggota perpustakaan (`phone_encrypted`), dilindungi enkripsi mandiri **AES-256-GCM**.
- **Data Terlindungi dari IDOR (Syarat 10):** Riwayat peminjaman buku anggota lain (`borrowings`), dilindungi otorisasi server-side Policy (*anti-IDOR*).

Dosen mewajibkan aplikasi dapat diakses aman melalui HTTPS, memiliki otentikasi login/registrasi, pemisahan peran admin dan anggota biasa, pengelolaan koleksi buku, pencarian katalog, kolom ulasan teks bebas untuk pengujian XSS, serta minimal 2 akun anggota dan 1 akun admin fiktif. Seluruh 10 syarat keamanan wajib dibuktikan dengan bukti objektif tangkapan layar.

---

### Sepuluh Syarat yang Dinilai (Bobot: 8 Poin / Syarat = Total 80 Poin)

Syarat 6–10 **wajib memiliki perbandingan langsung kondisi Sebelum (Rentan) vs Sesudah (Aman)**:

| Syarat Dosen | Lingkup Pekerjaan & Pengamanan | Bukti yang Diminta | Bagian Tutorial |
| :--- | :--- | :--- | :--- |
| **1 / SEC-01 — Tabel Risiko** | Identifikasi 5 aset perpustakaan dan 5 risiko CIA; kontrol keamanan Syarat 2–10; penjelasan risiko tertinggi (kerusakan database). | Tabel matriks risiko CIA lengkap & analisis risiko tertinggi di laporan. | Bagian 10; sumber `tickets/TKT-017`. |
| **2 / SEC-02 — Password Hashing** | Simpan password menggunakan hashing lambat (Bcrypt/Argon2id) dengan salt unik otomatis; batas validasi 8–72 byte. | Cuplikan kode controller/request & verifikasi 2 akun password sama menghasilkan hash berbeda di DB. | Bagian 5.2 dan 9; sumber `TKT-005-WEB`. |
| **3 / SEC-03 — Enkripsi Telepon** | Enkripsi nomor telepon anggota dengan AES-256-GCM, nonce 12 byte acak baru tiap enkripsi, kunci 256-bit di `.env`, envelope `v1:<nonce>:<ct>:<tag>`. | Nilai ciphertext di SQLite CLI, tampilan dekripsi pengguna berhak di web, cuplikan service & `.gitignore`. | Bagian 5.2 dan 9; sumber `TKT-003-WEB`. |
| **4 / SEC-04 — HTTPS Self-Signed** | Enkripsi komunikasi web dengan sertifikat self-signed OpenSSL SAN, private key izin 600, redirect port 80 ke 443 via Nginx. | Gembok HTTPS browser, jendela detail sertifikat (SAN), bukti redirect HTTP 301. | Bagian 6; sumber `TKT-013`. |
| **5 / SEC-05 — Hardening Linux** | Operator non-root `bunga0120` (sudo); akun layanan aplikasi `appsvc` (shell `/usr/sbin/nologin`); `.env` izin 600; source code read-only; SSH hardening (`PermitRootLogin no`, `MaxAuthTries 3`). | Output `id appsvc`, `ls -l .env`, `sshd -T`, dan status layanan aktif tanpa root. | Bagian 3, 4, dan 5; sumber `TKT-014`. |
| **6 / SEC-06 — Firewall UFW** | Kebijakan *default deny incoming*; port 22 SSH dibatasi ke IP host laptop (`192.168.56.1`); port 80 & 443 terbuka; Nmap sebelum vs sesudah. | Hasil pemindaian Nmap dari Windows host sebelum/sesudah, `sudo ufw status verbose`, tabel port. | Bagian 7; sumber `TKT-015`. |
| **7 / SEC-07 — Backup & Restore Drill** | Backup atomik SQLite WAL via `VACUUM INTO`; kompresi tar; enkripsi OpenSSL AES-256; hash SHA-256 di `daftar_hash.txt`; cron harian; salinan offsite; uji restore $< 60$ detik. | Skrip backup, output `crontab -l`, `daftar_hash.txt`, dan 3 tangkapan layar kondisi: Sebelum Dihapus, Setelah Dihapus, Setelah Restore. | Bagian 8; sumber `TKT-016`. |
| **8 / SEC-08 — SQL Injection** | Buktikan pencarian rentan loopback 8001 dibobol `' OR '1'='1 -- `; buktikan versi aman 443 menetralkan payload via parameter binding. | Tangkapan layar hasil pencarian rentan vs aman dengan input identik & perbandingan potongan query. | Bagian 9, Langkah 1–3; sumber `TKT-018`. |
| **9 / SEC-09 — Stored XSS** | Buktikan ulasan buku rentan memicu popup JavaScript; buktikan versi aman meng-escape payload secara kontekstual via Blade `{{ }}` & CSP header. | Alert popup versi rentan, teks biasa versi aman, perbandingan template Blade & header CSP. | Bagian 9, Langkah 1–2 & 4; sumber `TKT-019`. |
| **10 / SEC-10 — IDOR & Sesi** | Buktikan akses riwayat pinjam anggota lain tembus di versi rentan; buktikan versi aman menolak dengan HTTP 403 Forbidden via Policy; cookie `HttpOnly`, `Secure`, `SameSite=Lax`. | Tangkapan layar akses silang rentan vs 403 aman, DevTools flag cookie, bukti regenerasi/invalidation sesi. | Bagian 6.3 dan 9, Langkah 5; sumber `TKT-020`. |

---

### Hasil yang Wajib Dikumpulkan ke Dosen
1. **Laporan Akademik PDF:** Standar A4, font 11–12 pt, maksimal 6 halaman badan laporan + maksimal 4 halaman lampiran bukti visual. Memuat topologi, implementasi 10 syarat, analisis risiko sisa, dan deklarasi AI.
2. **Repositori Git & Arsip ZIP:** Repositori privat GitHub (`https://github.com/bunganamira30/perpustakaan.git`) dengan minimal 10 commit bertahap yang rapi (saat ini sudah memiliki 12 commit modular `TKT-001-WEB` s/d `TKT-012-WEB`). Jangan menyertakan `.env`, private key, atau database operasional.
3. **Video Demonstrasi:** Durasi maksimal 5 menit, audio narasi jelas, menampilkan identitas mahasiswa (`whoami; date`), navigasi aplikasi via HTTPS, pembuktian keamanan, dan demonstrasi perbandingan rentan vs aman.
4. **Kesiapan Ujian / Tanya Jawab:** Penguasaan konsep enkripsi vs hashing, salt unik, nonce CSPRNG, integritas GCM tag, firewall default deny, dan logika stok atomik.

---

## 1. Parameter Lingkungan dan Pagar Pengaman

### Parameter Jaringan & Identitas Sistem

| Parameter | Nilai Standar Proyek | Keterangan |
| :--- | :--- | :--- |
| **Sistem Operasi VM** | Ubuntu Server 24.04 LTS | VirtualBox / VMware |
| **IP Host-Only Server VM** | `192.168.56.101` | Adapter Host-Only (misal: `enp0s3` atau `ens33`) |
| **IP Klien Host Windows** | `192.168.56.1` | Adapter VirtualBox Host-Only Network / VMnet1 |
| **Domain Lokal** | `sip-aman.test` | Dipetakan di `hosts` Windows ke `192.168.56.101` |
| **Operator Server (Sudo)** | `bunga0120` | Akun non-root mahasiswa pengembang |
| **Akun Layanan Aplikasi** | `appsvc` | Sistem akun tanpa login shell (`/usr/sbin/nologin`) |
| **Path Proyek di VM** | `/var/www/sip-perpustakaan` | Direktori web utama Nginx |
| **Port Operasional Aman** | `443` (HTTPS) & `80` (Redirect 301 ke 443) | Produksi terlindungi Nginx & UFW |
| **Port Modul Rentan (Demo)** | `127.0.0.1:8001` (Hanya Loopback) | **Dilarang keras dibuka di Nginx atau UFW** |

### Pagar Pengaman Sebelum Memulai
1. **Buat Snapshot VM:** Sebelum melakukan modifikasi SSH atau UFW, ambil *snapshot* VM dengan nama `Snapshot-Awal-Sebelum-Hardening`. Jika terjadi kesalahan konfigurasi jaringan, Anda dapat mengembalikannya dalam hitungan detik.
2. **Kerahasiaan Kunci:** Jangan pernah memasukkan file `.env`, file sertifikat `.key`, atau password asli ke dalam repositori publik.
3. **Data Fiktif:** Seluruh data yang diuji adalah data fiktif (`Budi Santoso`, `Siti Aminah`, `Ahmad Rizki`).
4. **Isolasi Modul Rentan:** Modul demo celah keamanan hanya boleh berjalan pada loopback `127.0.0.1:8001` untuk kebutuhan video/laporan.

---

## 2. Urutan Pengerjaan Sisa di Server VM

Tahap pengerjaan tiket web (`TKT-001-WEB` s/d `TKT-012-WEB`) telah rampung 100%. Rangkaian kerja di bawah ini adalah eksekusi langsung Anda di VM Ubuntu:

```
[FASE 3: INFRASTRUKTUR LINUX & SERVER]
TKT-013 (HTTPS Self-Signed OpenSSL SAN & Nginx Reverse Proxy)
   │
   ▼
TKT-014 (Linux Hardening, User appsvc, Izin 600, SSH Hardening)
   │
   ▼
TKT-015 (Firewall UFW Default Deny & Pemindaian Nmap Komparatif)
   │
   ▼
TKT-016 (Backup SQLite WAL Terenkripsi & Restore Drill < 60 Detik)
   │
[FASE 4: PENGUJIAN LAB & DOKUMENTASI KOMPARATIF]
TKT-017 (Tabel Matriks Risiko CIA 5 Aset Perpustakaan)
TKT-018 (Dokumentasi Komparatif SQL Injection Sebelum vs Sesudah)
TKT-019 (Dokumentasi Komparatif Stored XSS & CSP Sebelum vs Sesudah)
TKT-020 (Dokumentasi Komparatif IDOR & Cookie Sesi Sebelum vs Sesudah)
   │
[FASE 5: DELIVERABLE AKADEMIK]
TKT-021 (Laporan PDF Akademik Maksimal 6 Halaman)
TKT-022 (Perekaman Video Demonstrasi 5 Menit)
TKT-023 (Audit Repositori Git 10 Commit, Arsip ZIP, & Pengumpulan)
```

> [!TIP]
> **Standar Tangkapan Layar Bukti VM:**  
> Setiap kali mengambil tangkapan layar terminal server Ubuntu, selalu awali dengan menjalankan:
> ```bash
> whoami; date
> ```
> Ini membuktikan ke dosen bahwa pengujian dilakukan langsung oleh Anda (`bunga0120`) pada waktu yang valid.

---

## 3. Persiapan Jaringan dan Paket Server (TKT-014)

### 3.1 Verifikasi Alamat IP Host-Only
Di terminal VM Ubuntu:
```bash
whoami; date
ip -br addr
```
Pastikan interface Host-Only memiliki IP `192.168.56.101`.  
Di PowerShell laptop Windows:
```powershell
ipconfig
ping 192.168.56.101
```
Jika ping berhasil, koneksi host-to-guest telah terhubung dengan sempurna.

### 3.2 Pembuatan Akun Operator & Akun Layanan
Sesuai syarat **SEC-05**, administrasi dilakukan oleh pengguna non-root `bunga0120`, dan aplikasi berjalan di bawah akun layanan `appsvc`:

```bash
# 1. Pastikan akun operator bunga0120 ada dan masuk grup sudo
id bunga0120 || sudo adduser bunga0120
sudo usermod -aG sudo bunga0120

# 2. Buat akun layanan sistem appsvc tanpa shell interaktif
sudo adduser --system --no-create-home --group --shell /usr/sbin/nologin appsvc

# 3. Verifikasi akun layanan
id appsvc
getent passwd appsvc
```
*Output verifikasi:* Menampilkan user `appsvc` dengan shell `/usr/sbin/nologin`.

### 3.3 Instalasi Paket Server yang Dibutuhkan
Pastikan koneksi internet sementara (NAT) aktif untuk mengunduh paket resmi Ubuntu:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx openssh-server php8.3-cli php8.3-fpm php8.3-sqlite3 php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip sqlite3 openssl curl ufw cron git unzip
```
Periksa status layanan utama:
```bash
sudo systemctl status nginx php8.3-fpm ssh cron --no-pager
```

---

## 4. Konfigurasi SSH Key dan Hardening (SEC-05)

### 4.1 Pembuatan Pasangan Kunci SSH di Windows Host
Di terminal **PowerShell Windows** (Laptop Anda):
```powershell
# 1. Buat direktori .ssh jika belum ada
New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.ssh"

# 2. Buat pasangan kunci Ed25519 khusus server perpustakaan
ssh-keygen -t ed25519 -f "$env:USERPROFILE\.ssh\id_ed25519_pustaka" -C "bunga0120@pustaka-aman"

# 3. Tampilkan public key untuk disalin ke VM
Get-Content "$env:USERPROFILE\.ssh\id_ed25519_pustaka.pub"
```
Salin satu baris kunci publik yang ditampilkan (diawali `ssh-ed25519 ...`).

### 4.2 Pemasangan Public Key di Ubuntu VM
Di terminal **Ubuntu Server** sebagai `bunga0120`:
```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
```
Tempelkan kunci publik Windows Anda pada baris baru, simpan (`Ctrl+O`, `Enter`, `Ctrl+X`), lalu kunci izinnya:
```bash
chmod 600 ~/.ssh/authorized_keys
```

### 4.3 Uji Login SSH Key dari Windows
Buka jendela PowerShell Windows baru dan uji koneksi berbasis kunci:
```powershell
ssh -i "$env:USERPROFILE\.ssh\id_ed25519_pustaka" -o PreferredAuthentications=publickey bunga0120@192.168.56.101
```
Jika Anda berhasil masuk tanpa diminta password akun Linux, autentikasi kunci telah aktif.

### 4.4 Penerapan SSH Hardening
Setelah login kunci terbukti berhasil, terapkan aturan pengerasan SSH di VM:
```bash
sudo nano /etc/ssh/sshd_config.d/pustaka-hardening.conf
```
Masukkan baris konfigurasi berikut:
```text
PermitRootLogin no
MaxAuthTries 3
PubkeyAuthentication yes
PasswordAuthentication yes
```
*(Catatan: Biarkan `PasswordAuthentication yes` untuk sementara hingga seluruh pengujian selesai, atau ubah ke `no` setelah yakin kunci SSH selalu tersedia).*  
Uji sintaks dan terapkan:
```bash
sudo sshd -t && sudo systemctl reload ssh
sudo sshd -T | grep -E 'permitrootlogin|maxauthtries|pubkeyauthentication'
```
**[AMBIL BUKTI EVID-SEC05-SSH.png]**: Tangkap layar terminal yang menampilkan `whoami; date` dan output `sshd -T` di atas.

---

## 5. Deploy Aplikasi SIP-Aman di Server VM (TKT-014)

### 5.1 Clone Repositori dan Penyiapan Direktori
Di terminal Ubuntu sebagai `bunga0120`:
```bash
# 1. Buat direktori aplikasi web
sudo mkdir -p /var/www/sip-perpustakaan
sudo chown -R bunga0120:appsvc /var/www/sip-perpustakaan
sudo chmod 750 /var/www/sip-perpustakaan

# 2. Clone repositori dari GitHub
cd /var/www/sip-perpustakaan
git clone https://github.com/bunganamira30/perpustakaan.git .
git switch main
git log -1 --oneline
```

### 5.2 Konfigurasi Berkas `.env` dan Database SQLite WAL
Salin berkas konfigurasi lingkungan dan buat kunci enkripsi:
```bash
cp .env.example .env

# Generate APP_KEY Laravel
php artisan key:generate

# Generate Kunci Kriptografi AES-256-GCM Telepon (32-byte hex = 64 karakter hex)
openssl rand -hex 32
```
Edit berkas `.env`:
```bash
nano .env
```
Sesuaikan parameter penting berikut:
```dotenv
APP_NAME="SIP Pustaka Aman"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://192.168.56.101

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/sip-perpustakaan/database/database.sqlite

# Tempelkan 64 karakter hex hasil openssl rand -hex 32 di atas:
APP_PHONE_ENCRYPTION_KEY=PASTE_KUNCI_HEX_64_KARAKTER_DI_SINI

# Pengaturan Keamanan Sesi Cookie
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```
Simpan (`Ctrl+O`, `Enter`, `Ctrl+X`).

Jalankan migrasi dan seeder data fiktif:
```bash
# Buat file basis data SQLite kosong
touch database/database.sqlite

# Aktifkan mode WAL (Write-Ahead Logging) untuk transaksi atomik
sqlite3 database/database.sqlite "PRAGMA journal_mode=WAL;"

# Eksekusi migrasi skema tabel dan seeder data uji
php artisan migrate --force
php artisan db:seed --force
```

### 5.3 Penataan Hak Akses Minimal (*Least Privilege Permissions*)
Sesuai mandat **SEC-05**, proses worker web `appsvc` tidak boleh memiliki izin tulis pada kode program:
```bash
# 1. Kepemilikan kode: bunga0120 (pemilik) dan appsvc (grup pembaca)
sudo chown -R bunga0120:appsvc /var/www/sip-perpustakaan
sudo find /var/www/sip-perpustakaan -type d -exec chmod 755 {} \;
sudo find /var/www/sip-perpustakaan -type f -exec chmod 644 {} \;

# 2. Storage, Cache, dan Database wajib writable oleh appsvc
sudo chown -R appsvc:appsvc /var/www/sip-perpustakaan/storage \
  /var/www/sip-perpustakaan/bootstrap/cache \
  /var/www/sip-perpustakaan/database
sudo chmod -R 775 /var/www/sip-perpustakaan/storage \
  /var/www/sip-perpustakaan/bootstrap/cache \
  /var/www/sip-perpustakaan/database

# 3. Kunci izin rahasia .env menjadi 600 milik appsvc
sudo chown appsvc:appsvc /var/www/sip-perpustakaan/.env
sudo chmod 600 /var/www/sip-perpustakaan/.env

# 4. Konfigurasi worker PHP-FPM agar berjalan sebagai user appsvc
sudo sed -i 's/user = www-data/user = appsvc/g' /etc/php/8.3/fpm/pool.d/www.conf
sudo sed -i 's/group = www-data/group = appsvc/g' /etc/php/8.3/fpm/pool.d/www.conf
sudo systemctl restart php8.3-fpm
```

Verifikasi hak akses:
```bash
whoami; date
ls -la /var/www/sip-perpustakaan/.env
sudo -u appsvc test -r /var/www/sip-perpustakaan/.env && echo "OK: appsvc dapat membaca .env"
sudo -u appsvc test ! -w /var/www/sip-perpustakaan/app && echo "OK: appsvc DITOLAK menulis source code"
```
**[AMBIL BUKTI EVID-SEC05-PERM.png]**: Tangkap layar output verifikasi di atas.

---

## 6. Konfigurasi HTTPS Self-Signed Nginx Reverse Proxy (TKT-013 / SEC-04)

### 6.1 Pembuatan Sertifikat OpenSSL RSA 2048-bit dengan Ekstensi SAN
Di terminal Ubuntu sebagai `bunga0120`:
```bash
# 1. Buat folder penyimpanan sertifikat jika belum ada
sudo mkdir -p /etc/ssl/certs /etc/ssl/private

# 2. Generate sertifikat X.509 berumur 365 hari dengan SAN IP & DNS
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/sip_library.key \
  -out /etc/ssl/certs/sip_library.crt \
  -subj "/C=ID/ST=Aceh/L=Lhokseumawe/O=PustakaAman/CN=192.168.56.101" \
  -addext "subjectAltName=IP:192.168.56.101,DNS:localhost,DNS:sip-aman.test"

# 3. Kunci izin private key menjadi 600 (Hanya root yang dapat membaca)
sudo chmod 600 /etc/ssl/private/sip_library.key
sudo chmod 644 /etc/ssl/certs/sip_library.crt

# 4. Verifikasi informasi SAN pada sertifikat
sudo openssl x509 -in /etc/ssl/certs/sip_library.crt -noout -text | grep -A 2 "Subject Alternative Name"
```
*Output Verifikasi:* Memuat `IP Address:192.168.56.101, DNS:localhost, DNS:sip-aman.test`.

### 6.2 Konfigurasi Server Block Nginx
Buat berkas konfigurasi situs baru di Nginx:
```bash
sudo nano /etc/nginx/sites-available/sip-perpustakaan
```
Isi konfigurasi berikut:
```nginx
# Blok Port 80: Redirect permanen (HTTP 301) ke HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name 192.168.56.101 localhost sip-aman.test;

    return 301 https://$host$request_uri;
}

# Blok Port 443: HTTPS Reverse Proxy dengan TLS Modern
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name 192.168.56.101 localhost sip-aman.test;

    root /var/www/sip-perpustakaan/public;
    index index.php index.html;

    # Sertifikat SSL/TLS
    ssl_certificate /etc/ssl/certs/sip_library.crt;
    ssl_certificate_key /etc/ssl/private/sip_library.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Header Keamanan
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Routing Laravel Front Controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Proxy ke PHP 8.3-FPM UNIX Socket
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Blokir akses ke file sensitif dan tersembunyi
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(sqlite|sqlite3|db|env|key|log)$ {
        deny all;
    }
}
```
Simpan (`Ctrl+O`, `Enter`, `Ctrl+X`).

Aktifkan konfigurasi dan nonaktifkan default site:
```bash
sudo ln -sf /etc/nginx/sites-available/sip-perpustakaan /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```
Pastikan `nginx -t` menghasilkan `syntax is ok` dan `test is successful`.

### 6.3 Pengujian dari Browser Laptop Windows
1. Buka Notepad di Windows dengan mode **Run as Administrator**, buka file `C:\Windows\System32\drivers\etc\hosts`, tambahkan baris:
   ```text
   192.168.56.101 sip-aman.test
   ```
2. Buka peramban (Chrome / Edge), akses: `http://192.168.56.101`  
   *Hasil:* Browser otomatis beralih (*redirect*) ke `https://192.168.56.101`.
3. Klik ikon gembok / peringatan sertifikat -> **Certificate Viewer** -> Tampilkan tab **Details** (Subject Alternative Name).  
   **[AMBIL BUKTI EVID-SEC04-SSL-CERT.png]**: Tangkap layar jendela sertifikat SAN dan URL HTTPS.
4. Buka **F12 (Developer Tools)** -> tab **Application** (atau **Storage**) -> **Cookies**.  
   Periksa cookie `sip_aman_session`:  
   - `HttpOnly`: Centang / True  
   - `Secure`: Centang / True  
   - `SameSite`: Lax  
   **[AMBIL BUKTI EVID-SEC10-COOKIE.png]**: Tangkap layar tabel cookie yang memperlihatkan ketiga flag keamanan tersebut.

> [!NOTE]
> **Jawaban 3 Kalimat Mengapa Browser Memunculkan Peringatan (Syarat 4):**  
> *"Peramban memunculkan peringatan keamanan karena sertifikat SSL/TLS yang digunakan berstatus self-signed dan tidak ditandatangani oleh Otoritas Sertifikat (Certificate Authority / CA) publik tepercaya yang terdaftar pada root store sistem operasi klien. Meskipun demikian, kanal komunikasi data antara peramban dan server tetap terenkripsi secara kriptografis menggunakan algoritma RSA 2048-bit dan protokol TLSv1.3. Peringatan ini merupakan perilaku standar peramban untuk memberi tahu pengguna bahwa identitas penerbit sertifikat diverifikasi secara mandiri di lingkungan pengujian laboratorium lokal."*

---

## 7. Firewall UFW dan Pemindaian Nmap Komparatif (TKT-015 / SEC-06)

### 7.1 Pemindaian Port SEBELUM Firewall Aktif
Dari terminal **PowerShell Windows** (Laptop Host):
```powershell
nmap -Pn -p- 192.168.56.101
```
Simpan keluaran terminal.  
**[AMBIL BUKTI EVID-SEC06-NMAP-BEFORE.png]**: Tangkap layar hasil pemindaian sebelum UFW aktif.

### 7.2 Konfigurasi Aturan UFW di Server VM
Di terminal Ubuntu sebagai `bunga0120`:
```bash
whoami; date

# 1. Kebijakan default deny incoming dan allow outgoing
sudo ufw default deny incoming
sudo ufw default allow outgoing

# 2. Batasi port SSH 22 HANYA dari IP Host Laptop 192.168.56.1
sudo ufw allow from 192.168.56.1 to any port 22 proto tcp comment 'SSH Admin Host Only'

# 3. Buka port web HTTP 80 dan HTTPS 443 untuk semua klien
sudo ufw allow 80/tcp comment 'HTTP Redirect'
sudo ufw allow 443/tcp comment 'HTTPS Secure'

# 4. Aktifkan firewall
sudo ufw --force enable

# 5. Tampilkan status verbose
sudo ufw status verbose
```
**[AMBIL BUKTI EVID-SEC06-UFW-STATUS.png]**: Tangkap layar output `sudo ufw status verbose`.

### 7.3 Pemindaian Port SESUDAH Firewall Aktif
Dari terminal **PowerShell Windows**:
```powershell
nmap -Pn -p- 192.168.56.101
```
Hasil pemindaian sekarang menunjukkan hanya port 22, 80, dan 443 yang berstatus `open`, sedangkan seluruh port lainnya berstatus `filtered`.  
**[AMBIL BUKTI EVID-SEC06-NMAP-AFTER.png]**: Tangkap layar hasil pemindaian sesudah UFW aktif.

### 7.4 Tabel Komparasi Port untuk Laporan Akademik

| Port | Protokol | Layanan | Status Sebelum UFW | Status Sesudah UFW | Keterangan Akses & Keamanan |
| :---: | :---: | :---: | :---: | :---: | :--- |
| **22** | TCP | SSH | Open (Semua IP) | Open (Dibatasi `192.168.56.1`) | Akses remote dibatasi khusus laptop administrator |
| **80** | TCP | HTTP | Open | Open | Melayani redirect HTTP 301 ke HTTPS 443 |
| **443** | TCP | HTTPS | Open | Open | Melayani aplikasi web terenkripsi SSL/TLS |
| **Port Lain** | TCP/UDP | Layanan OS | Open / Closed | **Filtered (Drop)** | Menutup permukaan serangan dari pemindaian liar |

> [!NOTE]
> **Analisis 3 Kalimat Pengurangan Permukaan Serangan (Syarat 6):**  
> *"Penerapan kebijakan default deny pada UFW secara efektif menutup seluruh port layanan yang tidak digunakan dan mengubah status port yang tidak terdaftar menjadi filtered, sehingga penyerang potensial tidak dapat melakukan pemetaan layanan aktif. Pembatasan akses port 22 SSH khusus ke alamat IP klien administrator (192.168.56.1) mencegah serangan brute-force kredensial dari host lain di jaringan lokal. Dengan membukanya hanya port 80 dan 443, permukaan serangan jaringan server berhasil diminimalkan hanya pada pintu masuk web proxy yang terkontrol."*

---

## 8. Backup Otomatis Terenkripsi & Restore Drill (TKT-016 / SEC-07)

### 8.1 Pembuatan Kunci Sandi Backup
Di terminal Ubuntu sebagai `bunga0120`:
```bash
# Buat kunci sandi cadangan 32-byte acak milik root (izin 600)
sudo sh -c 'openssl rand -hex 32 > /etc/backup.pass && chmod 600 /etc/backup.pass'
sudo mkdir -p /backup && sudo chmod 700 /backup
```

### 8.2 Skrip Pencadangan Otomatis (`/usr/local/bin/sip-backup.sh`)
```bash
sudo nano /usr/local/bin/sip-backup.sh
```
Masukkan skrip bash berikut:
```bash
#!/bin/bash
set -euo pipefail

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/backup"
DB_SRC="/var/www/sip-perpustakaan/database/database.sqlite"
ENV_SRC="/var/www/sip-perpustakaan/.env"
PASS_FILE="/etc/backup.pass"
TMP_DIR="/tmp/backup_${TIMESTAMP}"

mkdir -p "${TMP_DIR}"

# 1. Snapshot Atomik SQLite WAL Mode via VACUUM INTO
sqlite3 "${DB_SRC}" "VACUUM INTO '${TMP_DIR}/database_consistent.sqlite'"

# 2. Arsipkan database snapshot dan file konfigurasi .env
cp "${ENV_SRC}" "${TMP_DIR}/.env"
tar -czf "${TMP_DIR}/raw_backup.tar.gz" -C "${TMP_DIR}" database_consistent.sqlite .env

# 3. Enkripsi Arsip dengan OpenSSL AES-256-CBC & PBKDF2
ENC_FILE="${BACKUP_DIR}/sip_backup_${TIMESTAMP}.tar.gz.enc"
openssl enc -aes-256-cbc -pbkdf2 -iter 100000 -salt \
  -in "${TMP_DIR}/raw_backup.tar.gz" \
  -out "${ENC_FILE}" \
  -pass file:"${PASS_FILE}"

# 4. Hitung dan Catat Hash SHA-256 untuk Integritas
sha256sum "${ENC_FILE}" >> "${BACKUP_DIR}/daftar_hash.txt"

# 5. Bersihkan direktori sementara
rm -rf "${TMP_DIR}"

echo "[$(date)] Backup sukses: ${ENC_FILE}"
```
Berikan izin eksekusi eksklusif:
```bash
sudo chmod 700 /usr/local/bin/sip-backup.sh
```

### 8.3 Skrip Pemulihan Bencana (`/usr/local/bin/sip-restore.sh`)
```bash
sudo nano /usr/local/bin/sip-restore.sh
```
Masukkan skrip bash pemulihan berikut:
```bash
#!/bin/bash
set -euo pipefail

if [ "$#" -ne 1 ]; then
    echo "Penggunaan: $0 /path/to/sip_backup_xxx.tar.gz.enc"
    exit 1
fi

ENC_FILE="$1"
PASS_FILE="/etc/backup.pass"
TMP_RESTORE="/tmp/restore_run"
TARGET_DB="/var/www/sip-perpustakaan/database/database.sqlite"

mkdir -p "${TMP_RESTORE}"

# 1. Dekripsi Berkas Backup
openssl enc -d -aes-256-cbc -pbkdf2 -iter 100000 \
  -in "${ENC_FILE}" \
  -out "${TMP_RESTORE}/raw_backup.tar.gz" \
  -pass file:"${PASS_FILE}"

# 2. Ekstrak Arsip
tar -xzf "${TMP_RESTORE}/raw_backup.tar.gz" -C "${TMP_RESTORE}"

# 3. Pulihkan Basis Data dan Kembalikan Izin appsvc
cp "${TMP_RESTORE}/database_consistent.sqlite" "${TARGET_DB}"
chown appsvc:appsvc "${TARGET_DB}"
chmod 664 "${TARGET_DB}"

# Bersihkan file temporer
rm -rf "${TMP_RESTORE}"

echo "[$(date)] Pemulihan sukses ke ${TARGET_DB}"
```
Berikan izin eksekusi:
```bash
sudo chmod 700 /usr/local/bin/sip-restore.sh
```

### 8.4 Penjadwalan Cron Harian
Daftarkan skrip backup ke crontab milik user root:
```bash
sudo crontab -e
```
Tambahkan baris berikut di akhir file (berjalan setiap pukul 02:00 dini hari):
```cron
0 2 * * * /usr/local/bin/sip-backup.sh >> /var/log/sip_backup.log 2>&1
```
Verifikasi cron terdaftar:
```bash
sudo crontab -l
```
Uji jalankan skrip cadangan sekali untuk membuat file awal:
```bash
sudo /usr/local/bin/sip-backup.sh
cat /backup/daftar_hash.txt
ls -lh /backup/
```
**[AMBIL BUKTI EVID-SEC07-CRON-HASH.png]**: Tangkap layar output `sudo crontab -l` dan isi `cat /backup/daftar_hash.txt`.

### 8.5 Pelaksanaan Uji Pemulihan Bencana (*Restore Drill*)
Ikuti langkah berikut dan rekam ketiga kondisi wajib:

#### Langkah 1: Catat Kondisi Normal (Sebelum Dihapus)
```bash
whoami; date
sqlite3 /var/www/sip-perpustakaan/database/database.sqlite \
  "SELECT 'Buku:', count(*) FROM books; SELECT 'Peminjaman:', count(*) FROM borrowings;"
```
*Output:* Misal `Buku: 10`, `Peminjaman: 5`.  
**[AMBIL FOTO 1: EVID-SEC07-BEFORE.png]**.

#### Langkah 2: Simulasikan Bencana Kerusakan Data (Setelah Dihapus)
Simulasikan insiden kehilangan data dengan menghapus sejumlah record:
```bash
whoami; date
sqlite3 /var/www/sip-perpustakaan/database/database.sqlite \
  "DELETE FROM borrowings WHERE id IN (1, 2); DELETE FROM books WHERE id IN (1, 2, 3);"
sqlite3 /var/www/sip-perpustakaan/database/database.sqlite \
  "SELECT 'Buku:', count(*) FROM books; SELECT 'Peminjaman:', count(*) FROM borrowings;"
```
*Output:* Menunjukkan jumlah data berkurang drastis (misal `Buku: 7`, `Peminjaman: 3`).  
**[AMBIL FOTO 2: EVID-SEC07-DELETED.png]**.

#### Langkah 3: Eksekusi Restore dan Hitung Durasi Waktu
Ambil file cadangan terbaru di `/backup/` dan eksekusi pemulihan dengan utilitas `time`:
```bash
whoami; date
LATEST_BACKUP=$(ls -t /backup/*.tar.gz.enc | head -n 1)
time sudo /usr/local/bin/sip-restore.sh "${LATEST_BACKUP}"

# Verifikasi integritas SQLite dan hitung kembali jumlah data
sqlite3 /var/www/sip-perpustakaan/database/database.sqlite "PRAGMA integrity_check;"
sqlite3 /var/www/sip-perpustakaan/database/database.sqlite \
  "SELECT 'Buku:', count(*) FROM books; SELECT 'Peminjaman:', count(*) FROM borrowings;"
```
*Hasil:* `PRAGMA integrity_check` bernilai `ok`, jumlah baris kembali utuh (`Buku: 10`, `Peminjaman: 5`), dan durasi tercatat nyata (misal `real 0m0.421s` $< 60$ detik).  
**[AMBIL FOTO 3: EVID-SEC07-RESTORED.png]**.

> [!NOTE]
> **Analisis 2 Kalimat Risiko Ransomware di Server yang Sama (Syarat 7):**  
> *"Penyimpanan berkas cadangan pada media disk atau server fisik yang sama dengan aplikasi aktif rentan terhadap serangan ransomware lokal, karena malware yang berhasil memperoleh hak akses root dapat mengenkripsi atau menghapus file cadangan dan file operasional secara bersamaan. Oleh karena itu, penerapan kontrol korektif wajib mereplikasi berkas cadangan terenkripsi ke media offsite terpisah (misalnya host laptop atau cloud storage) yang memiliki batas hak akses independen."*

---

## 9. Demonstrasi Komparatif Celah Keamanan (SEC-08, SEC-09, SEC-10)

Untuk pengujian perbandingan, jalankan modul demo rentan di server VM pada port loopback:
```bash
# Di terminal screen / background VM:
cd /var/www/sip-perpustakaan
php artisan serve --host=127.0.0.1 --port=8001
```

### 9.1 SQL Injection (SEC-08)
- **Versi Rentan (`http://127.0.0.1:8001/vulnerable/books`):**
  - Masukkan payload pada kolom pencarian: `' OR '1'='1 -- `
  - *Hasil:* Seluruh buku tersembunyi/terarsip bocor keluar ke layar karena konkatenasi string SQL mentah.  
  **[AMBIL BUKTI EVID-SEC08-SQLI-VULN.png]**.
- **Versi Aman (`https://192.168.56.101/books`):**
  - Masukkan payload yang sama persis: `' OR '1'='1 -- `
  - *Hasil:* Tidak ada buku yang bocor ("Tidak ada koleksi buku yang ditemukan") karena Eloquent ORM menggunakan PDO *parameter binding*.  
  **[AMBIL BUKTI EVID-SEC08-SQLI-SAFE.png]**.

### 9.2 Stored Cross-Site Scripting / XSS (SEC-09)
- **Versi Rentan (`http://127.0.0.1:8001/vulnerable/books/1`):**
  - Kirim ulasan dengan payload: `<script>alert('XSS-TERORISIR')</script>`
  - *Hasil:* Muncul kotak dialog peringatan (*alert popup*) JavaScript karena Blade merender unescaped `{!! $comment !!}`.  
  **[AMBIL BUKTI EVID-SEC09-XSS-VULN.png]**.
- **Versi Aman (`https://192.168.56.101/books/1`):**
  - Kirim payload yang sama persis.
  - *Hasil:* Teks ditampilkan secara literal (`<script>alert(...)</script>`) karena Blade meng-escape HTML via `{{ $comment }}` dan diperkuat oleh header `Content-Security-Policy`.  
  **[AMBIL BUKTI EVID-SEC09-XSS-SAFE.png]**.

### 9.3 Insecure Direct Object Reference / IDOR (SEC-10)
- **Versi Rentan (`http://127.0.0.1:8001/vulnerable/borrowings/1`):**
  - Login sebagai Anggota 2 (`siti_aminah`), ubah URL untuk mengakses pinjaman ID 1 milik `budi_santoso`.
  - *Hasil:* Data pinjaman Budi bocor dan dapat dilihat oleh Siti tanpa pemeriksaan otorisasi.  
  **[AMBIL BUKTI EVID-SEC10-IDOR-VULN.png]**.
- **Versi Aman (`https://192.168.56.101/borrowings/1`):**
  - Login sebagai Anggota 2 (`siti_aminah`), coba akses URL pinjaman milik Budi.
  - *Hasil:* Server mengembalikan halaman **403 Forbidden** berkat otorisasi `BorrowingPolicy::view()`.  
  **[AMBIL BUKTI EVID-SEC10-IDOR-SAFE.png]**.

---

## 10. Checklist Kelengkapan & Pengumpulan Akhir (TKT-021 s/d TKT-023)

Sebelum menyerahkan tugas ke dosen, pastikan seluruh checklist berikut telah lengkap:

- [ ] **Laporan PDF (Maksimal 6 Halaman Badan + 4 Halaman Lampiran):**
  - [ ] Bab 1: Topologi Jaringan & Arsitektur Sistem (`192.168.56.101`).
  - [ ] Bab 2: Matriks Tabel Risiko 5 Aset CIA Perpustakaan (Syarat 1).
  - [ ] Bab 3: Implementasi Keamanan & Hardening Server (Syarat 2–7).
  - [ ] Bab 4: Pengujian Komparatif Sebelum-Sesudah (Syarat 8–10).
  - [ ] Bab 5: Analisis Risiko Sisa (*Residual Risk*), Refleksi Diri, & Deklarasi Penggunaan AI secara jujur.
- [ ] **Repositori Git & Berkas Proyek:**
  - [ ] Minimal 10 commit bertahap dengan riwayat profesional (Sudah ada 12 commit modular).
  - [ ] File rahasia `.env`, `.key`, dan database aktif **TIDAK TERLENGKAP / TIDAK BOCOR** di repositori.
  - [ ] Seluruh pengujian otomatis berstatus hijau (`php artisan test` 23 passed, 73 assertions).
- [ ] **Video Demonstrasi (Maksimal 5 Menit):**
  - [ ] Menampilkan terminal server dengan perintah `whoami; date` (`bunga0120`).
  - [ ] Menampilkan akses gembok HTTPS dan detail sertifikat SAN di peramban.
  - [ ] Menunjukkan status firewall UFW dan skrip backup terjadwal cron.
  - [ ] Menunjukkan demo komparatif SQLi, XSS, dan IDOR (Sebelum vs Sesudah).
  - [ ] Narasi suara jelas dan tanpa membocorkan kunci rahasia aplikasi.
- [ ] **Kesiapan Ujian Wawancara Dosen:**
  - [ ] Mampu menjelaskan perbedaan enkripsi AES-256-GCM vs hashing Bcrypt.
  - [ ] Mampu menjelaskan alasan mengapa `VACUUM INTO` wajib digunakan pada SQLite mode WAL.
  - [ ] Mampu menjelaskan mekanisme penolakan IDOR melalui Policy di Laravel.

---
*Dokumentasi ini disiapkan secara presisi untuk Bunga Namira (NIM: 230170120) — Teknik Informatika Universitas Malikussaleh.*
