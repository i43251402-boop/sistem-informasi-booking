-- Database baru: pinkcut_booking (tidak mengganggu database lama booking_editing)
CREATE DATABASE IF NOT EXISTS pinkcut_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pinkcut_booking;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  kategori VARCHAR(50) NOT NULL DEFAULT 'Lainnya',
  ikon VARCHAR(20) NOT NULL DEFAULT 'film',
  deskripsi TEXT,
  harga INT NOT NULL DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1
);
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(12) UNIQUE,
  nama VARCHAR(100) NOT NULL,
  wa VARCHAR(20) NOT NULL,
  service_id INT NOT NULL,
  tanggal DATE NOT NULL,
  jam VARCHAR(5) NOT NULL,
  catatan TEXT,
  link VARCHAR(255),
  pembayaran VARCHAR(20) NOT NULL DEFAULT 'QRIS',
  lunas TINYINT(1) NOT NULL DEFAULT 0,
  status VARCHAR(30) NOT NULL DEFAULT 'Menunggu konfirmasi',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (service_id) REFERENCES services(id)
);
CREATE TABLE closed_days (tanggal DATE PRIMARY KEY, keterangan VARCHAR(100));

-- Admin awal. Email: admin@booking.com | Password: password (segera ganti)
INSERT INTO admins (nama,email,password) VALUES ('Admin','admin@booking.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO services (nama,kategori,ikon,deskripsi,harga) VALUES
('Video Pendek','Sosmed','phone','Reels, TikTok, Shorts. Potong, subtitle, musik, transisi.',75000),
('Video YouTube','YouTube','play','Editing vlog atau konten panjang hingga 15 menit.',250000),
('Video Podcast','YouTube','mic','Potong, rapikan audio, dan buat klip highlight.',180000),
('Video Wedding','Event','heart','Highlight pernikahan dengan color grading sinematik.',500000),
('Video Dokumentasi Acara','Event','calendar','Wisuda, ulang tahun, dan acara keluarga atau kantor.',350000),
('Video Promosi Produk','Bisnis','tag','Iklan produk atau profil usaha dengan motion text.',200000);

-- Contoh data untuk uji Riwayat pesanan (cari: 081234567890)
INSERT INTO bookings (kode,nama,wa,service_id,tanggal,jam,catatan,pembayaran,lunas,status) VALUES
('BK-0001','Rani','6281234567890',1,CURDATE() + INTERVAL 3 DAY,'11:00','Reels peluncuran produk','QRIS',1,'Dalam pengerjaan'),
('BK-0002','Rani','6281234567890',6,CURDATE() - INTERVAL 5 DAY,'09:00','','Transfer Bank',1,'Selesai');
