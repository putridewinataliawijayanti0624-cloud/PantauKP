-- UPGRADE untuk database LAMA (yang sudah memakai kp_monitor.sql versi sebelumnya).
-- Menambah role "admin", kolom is_active, dan akun admin demo. Data lain tidak berubah.
-- Jalankan SEKALI pada database kp_monitor. Alternatif: cukup jalankan `php artisan migrate` lalu buat admin
-- dengan `php artisan app:buat-admin "Nama" email@contoh.ac.id`.
ALTER TABLE `users` MODIFY `role` ENUM('admin','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa';
ALTER TABLE `users` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `role`;
INSERT INTO `users` (`name`,`email`,`role`,`is_active`,`password`,`created_at`,`updated_at`)
VALUES ('Administrator Sistem','admin@kp.test','admin',1,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NOW(),NOW());
INSERT INTO `migrations` (`migration`,`batch`) SELECT '2026_10_16_000001_add_admin_and_active_to_users', COALESCE(MAX(`batch`),0)+1 FROM `migrations`;
