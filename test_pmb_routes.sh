#!/bin/bash

echo "Testing all PMB routes:"
echo "========================"

routes=(
    "/pmb-online"
    "/pmb-online/pendaftaran"
    "/pmb-online/jalur-seleksi"
    "/pmb-online/alur-pendaftaran"
    "/pmb-online/syarat"
    "/pmb-online/berita"
    "/pmb-online/fasilitas"
    "/pmb-online/program-studi"
    "/pmb-online/biaya"
    "/pmb-online/jadwal"
    "/pmb-online/faq"
    "/pmb-online/galeri"
    "/pmb-online/kontak"
    "/pmb-online/cek-pengumuman"
    "/pmb-online/login"
)

success=0
failed=0

for route in "${routes[@]}"; do
    http_status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8000$route")
    if [ "$http_status" = "200" ]; then
        echo "✅ $route - $http_status"
        ((success++))
    else
        echo "❌ $route - $http_status"
        ((failed++))
    fi
done

echo ""
echo "========================"
echo "Results: $success/15 passed, $failed/15 failed"
