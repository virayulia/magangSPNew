#!/usr/bin/env bash
# ==============================================================================
# Script Deployment & Setup Otomasi Cron Job magangSP (CloudPanel / VPS)
# PT Semen Padang - Sistem Informasi Magang & Penelitian
# ==============================================================================

set -e

# Warna untuk output terminal
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}===================================================================${NC}"
echo -e "${YELLOW}           Memulai Deployment & Aktivasi Cron Job magangSP         ${NC}"
echo -e "${YELLOW}===================================================================${NC}"

# 1. Tentukan Direktori Proyek & Deteksi PHP Binary
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"
echo -e "-> Direktori Proyek: ${GREEN}$PROJECT_DIR${NC}"

if command -v php8.2 &> /dev/null; then
    PHP_BIN="$(command -v php8.2)"
elif command -v php &> /dev/null; then
    PHP_BIN="$(command -v php)"
else
    echo -e "${RED}[ERROR] PHP CLI tidak ditemukan di server!${NC}"
    exit 1
fi
echo -e "-> Menggunakan PHP CLI: ${GREEN}$PHP_BIN${NC} ($($PHP_BIN -r 'echo PHP_VERSION;'))"

# 2. Git Pull (Jika dijalankan di Git Repository)
if [ -d ".git" ]; then
    echo -e "\n-> Mengambil update terbaru dari Git repository..."
    git pull origin main || git pull origin master || echo -e "${YELLOW}Peringatan: Git pull dilewati atau branch berbeda.${NC}"
fi

# 3. Update Composer Dependencies
if [ -f "composer.json" ]; then
    echo -e "\n-> Mengoptimasi dependencies dengan Composer..."
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        echo -e "${YELLOW}Composer tidak ditemukan di PATH, melewati composer install.${NC}"
    fi
fi

# 4. Pastikan Direktori Writable & Permissions Siap
echo -e "\n-> Menyesuaikan hak akses direktori writable dan upload..."
mkdir -p writable/cache writable/logs writable/session writable/uploads
chmod -R 775 writable/
mkdir -p public/uploads
chmod -R 775 public/uploads/
chmod +x spark

# 5. Bersihkan Cache CodeIgniter
echo -e "\n-> Membersihkan cache sistem..."
$PHP_BIN spark cache:clear || true

# 6. Setup / Update Crontab Server Secara Otomatis
echo -e "\n-> Mengonfigurasi Crontab Jadwal Sesuai CloudPanel..."

CRON_LOG="$PROJECT_DIR/writable/logs/cron.log"
touch "$CRON_LOG"
chmod 664 "$CRON_LOG"

CRON_START="# >>> magangsp-cron-cloudpanel >>>"
CRON_END="# <<< magangsp-cron-cloudpanel <<<"

# Jadwal sesuai yang telah ditentukan:
# 07:00 -> auto-tolak
# 09:00 -> remind-pembimbing
# 09:10 -> remind-unit
# 10:00 -> remind-laporan
# 14:00 -> email-akhir
# 16:00 -> remind-berkas
CRON_ENTRIES="$CRON_START
0 7 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run auto-tolak >> $CRON_LOG 2>&1
0 9 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run remind-pembimbing >> $CRON_LOG 2>&1
10 9 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run remind-unit >> $CRON_LOG 2>&1
0 10 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run remind-laporan >> $CRON_LOG 2>&1
0 14 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run email-akhir >> $CRON_LOG 2>&1
0 16 * * * cd $PROJECT_DIR && $PHP_BIN spark cron:run remind-berkas >> $CRON_LOG 2>&1
$CRON_END"

# Ambil crontab pengguna saat ini
EXISTING_CRON=$(crontab -l 2>/dev/null || true)

# Hapus blok lama jika pernah ada, lalu sisipkan blok baru
CLEANED_CRON=$(echo "$EXISTING_CRON" | sed "/$CRON_START/,/$CRON_END/d")

if [ -n "$CLEANED_CRON" ]; then
    NEW_CRONTAB="$CLEANED_CRON"$'\n'"$CRON_ENTRIES"
else
    NEW_CRONTAB="$CRON_ENTRIES"
fi

echo "$NEW_CRONTAB" | crontab -
echo -e "${GREEN}[OK] Crontab server berhasil diaktifkan dan diperbarui secara otomatis!${NC}"

echo -e "\n${YELLOW}===================================================================${NC}"
echo -e "${GREEN}             Deployment Selesai & Cron Job Berhasil Aktif!        ${NC}"
echo -e "${YELLOW}===================================================================${NC}"
echo -e "Daftar Cron Aktif di Server:"
echo -e "1. [07:00] Auto Tolak Tidak Konfirmasi"
echo -e "2. [09:00] Reminder Penetapan Pembimbing Unit"
echo -e "3. [09:10] Reminder Pimpinan Unit H-3 Peserta Masuk"
echo -e "4. [10:00] Reminder Upload Laporan & Absensi"
echo -e "5. [14:00] Email Akhir Program & Feedback"
echo -e "6. [16:00] Reminder Lengkapi Berkas BPJS Ketenagakerjaan"
echo -e "\nLog eksekusi akan otomatis dicatat ke: ${GREEN}$CRON_LOG${NC}"
echo -e "${YELLOW}===================================================================${NC}\n"
