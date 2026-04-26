<?php
/**
 * Panenly - Reusable Footer Component
 */
?>
<footer>
    <div class="footer-grid">
        <div>
            <div class="footer-brand"><i class="fa-solid fa-leaf"></i> Panenly</div>
            <p class="footer-desc">Menjembatani petani lokal langsung ke tangan Anda. Sayuran lebih segar, harga lebih jujur.</p>
        </div>
        <div>
            <h4 class="footer-title">Panenly</h4>
            <ul class="footer-links">
                <li><a href="<?= BASE_URL ?>/catalog.php">Katalog Produk</a></li>
                <li><a href="#">Tentang Kami</a></li>
                <li><a href="#">Mitra Petani</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Karir</a></li>
            </ul>
        </div>
        <div>
            <h4 class="footer-title">Bantuan</h4>
            <ul class="footer-links">
                <li><a href="#">Cara Belanja</a></li>
                <li><a href="#">Pengiriman</a></li>
                <li><a href="#">Pengembalian Dana</a></li>
                <li><a href="#">Hubungi Kami</a></li>
            </ul>
        </div>
        <div>
            <h4 class="footer-title">Unduh Aplikasi</h4>
            <p class="footer-desc" style="margin-bottom: 1rem;">Segera hadir di App Store dan Google Play</p>
            <div style="display:flex; gap: 10px; font-size: 1.5rem; color: #9ca3af;">
                <i class="fa-brands fa-instagram" style="cursor:pointer"></i>
                <i class="fa-brands fa-facebook" style="cursor:pointer"></i>
                <i class="fa-brands fa-twitter" style="cursor:pointer"></i>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date("Y") ?> Panenly. All rights reserved.
    </div>
</footer>
