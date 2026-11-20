@echo off
echo ========================================================
echo MENJALANKAN SERVER DEMO RENTAN TERISOLASI LOOPBACK
echo Host: 127.0.0.1  Port: 8001 (Dilarang dibuka ke internet)
echo Basis Data: database/vulnerable.sqlite
echo Sesi Cookie: sip_vuln_session
echo ========================================================
php artisan serve --host=127.0.0.1 --port=8001 --env=vulnerable
