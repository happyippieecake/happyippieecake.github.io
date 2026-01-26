# Ringkasan Sistem & Teknologi Website HappyippieCake

Dokumen ini berisi rangkuman teknis mengenai sistem dan bahasa pemrograman yang digunakan dalam website HappyippieCake. Rangkuman ini disusun untuk membantu menjawab pertanyaan akademis/dosen.

## 1. Bahasa Pemrograman & Teknologi (Tech Stack)

Website ini dibangun menggunakan pendekatan **Native (Murni)** untuk memahami fundamental pemrograman web secara mendalam.

*   **Backend (Sisi Server):**
    *   **PHP (Native):** Digunakan sebagai otak utama sistem. Menangani logika pemrosesan data, koneksi database, perhitungan transaksi (revenue), session management, dan autentikasi admin.
*   **Database:**
    *   **MySQL (via MariaDB/XAMPP):** Digunakan sebagai Relational Database Management System (RDBMS) untuk menyimpan data `pesanan`, `menu`, `payments`, dan `users`.
    *   **SQL:** Bahasa query standar untuk operasi CRUD (Create, Read, Update, Delete) data.
*   **Frontend (Antarmuka Pengguna):**
    *   **HTML5:** Kerangka dasar struktur halaman web.
    *   **CSS3:** Digunakan untuk styling tampilan. Menggabungkan **Custom CSS** (`styles.css`) dengan framework **Tailwind CSS** (via CDN) untuk desain modern, responsif, dan utilitas cepat.
    *   **JavaScript (Vanilla JS):** Digunakan untuk interaksi sisi klien (client-side scripting) seperti:
        *   Logika keranjang belanja dinamis (AJAX-like behavior tanpa reload).
        *   Modal popup untuk formulir pesanan.
        *   Visualisasi data grafik menggunakan library **Chart.js**.
        *   Manipulasi DOM untuk filter dan pencarian real-time.

## 2. Arsitektur & Fitur Sistem

Sistem ini dikategorikan sebagai **Web-based POS (Point of Sales) & Ordering System**.

### A. Sistem Manajemen Pesanan (Order Management)
*   **Alur:** Pelanggan memilih menu -> Input data & pesanan -> Data masuk status `pending`.
*   **Validasi:** Sistem memastikan input tidak boleh kosong dan stok tersedia (jika fitur stok aktif).

### B. Sistem Verifikasi Pembayaran (Payment Gateway Simulation)
*   Menggunakan logika **Two-way Verification**:
    1.  Pelanggan melakukan konfirmasi/upload bukti bayar.
    2.  Admin memvalidasi manual di halaman `Payments`.
*   **Logic Filtering:** Pesanan yang belum diverifikasi pembayarannya tidak akan muncul di daftar antrian "Perlu Diproses", mencegah pesanan fiktif.

### C. Dashboard Analitik Cerdas
*   **Real-time Stats:** Menghitung total revenue, total order, dan item terlaris.
*   **Revenue Recognition:** Logika pendapatan hanya menghitung uang dari pesanan yang statusnya sudah **"Selesai"** dan pembayarannya **"Confirmed"**. Pesanan yang batal atau belum selesai tidak diakui sebagai pendapatan real.

### D. Integrasi Eksternal
*   **WhatsApp API:** Fitur konsultasi yang mengarahkan user langsung ke WhatsApp admin dengan template pesan otomatis berisi detail keranjang belanja.

---

### Contoh Jawaban Singkat ke Dosen:

> *"Sistem ini dibangun menggunakan **PHP Native** dan **MySQL** untuk backend agar saya bisa memahami alur logika dasar web tanpa "magic" dari framework. Untuk frontend, saya menggunakan kombinasi **HTML, JavaScript, dan Tailwind CSS** agar tampilannya modern dan responsif. Sistem ini mencakup fitur manajemen pesanan lengkap, validasi pembayaran manual untuk keamanan transaksi, serta dashboard analitik yang menyajikan data penjualan real-time."*
