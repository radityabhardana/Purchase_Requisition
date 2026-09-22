        </main>
        
        <!-- Footer Bar -->
        <footer class="bg-white border-top py-3 px-4 text-secondary small no-print d-flex flex-column flex-sm-row align-items-center justify-content-between gap-1">
            <div>
                &copy; <?= date('Y') ?> <strong class="text-dark">SIP-NKP</strong> • PT Nandya Karya Perkasa (Plant Komponen Otomotif)
            </div>
            <div class="text-muted" style="font-size: 0.75rem;">
                Sistem Informasi Pengadaan & Pengendalian Inventaris Terpadu
            </div>
        </footer>
    </div>
</div>

<!-- Local Bootstrap 5.3.3 JS Bundle (Popper Included) -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- Render Lucide Icons & Realtime Clock -->
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Jam Digital Real-time (WIB)
    function updateRealtimeClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const clockEl = document.getElementById('realtimeClock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    updateRealtimeClock();
    setInterval(updateRealtimeClock, 1000);
</script>

</body>
</html>
