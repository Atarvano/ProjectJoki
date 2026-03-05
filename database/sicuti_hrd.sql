-- =====================================================
-- Sicuti HRD - Database Foundation
-- Import file for phpMyAdmin / MySQL CLI
-- =====================================================

-- =====================================================
-- 1) Create database and select it
-- =====================================================
CREATE DATABASE IF NOT EXISTS `sicuti_hrd`;
USE `sicuti_hrd`;

-- =====================================================
-- 2) Table: karyawan (master employee data)
-- =====================================================
CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(20) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `telepon` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `jabatan` VARCHAR(50) DEFAULT NULL,
  `departemen` VARCHAR(50) DEFAULT NULL,
  `tanggal_bergabung` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_karyawan_nik` (`nik`)
) ENGINE=InnoDB;

-- =====================================================
-- 3) Table: users (login data)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` INT UNSIGNED DEFAULT NULL,
  `username` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('hr','employee') NOT NULL DEFAULT 'employee',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_username` (`username`),
  KEY `idx_role_active` (`role`, `is_active`),
  CONSTRAINT `fk_users_karyawan`
    FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 4) Table: schema_migrations (for migration runner)
-- =====================================================
CREATE TABLE IF NOT EXISTS `schema_migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_schema_migrations_migration` (`migration`)
) ENGINE=InnoDB;

-- =====================================================
-- 5) Seed data: sample karyawan (3 rows)
-- =====================================================
INSERT INTO `karyawan`
  (`nik`, `nama`, `tanggal_lahir`, `email`, `telepon`, `alamat`, `jabatan`, `departemen`, `tanggal_bergabung`)
VALUES
  ('EMP001', 'Budi Santoso', '1990-05-15', 'budi@perusahaan.com', '081234567890', 'Jakarta', 'Staff HRD', 'HRD', '2018-03-15'),
  ('EMP002', 'Siti Rahayu', '1995-08-20', 'siti@perusahaan.com', '081234567891', 'Bandung', 'Staff Keuangan', 'Keuangan', '2020-07-01'),
  ('EMP003', 'Ahmad Fauzi', '2000-01-15', 'ahmad@perusahaan.com', '081234567892', 'Surabaya', 'Staff IT', 'IT', '2022-01-10')
ON DUPLICATE KEY UPDATE
  `nama` = VALUES(`nama`),
  `tanggal_lahir` = VALUES(`tanggal_lahir`),
  `email` = VALUES(`email`),
  `telepon` = VALUES(`telepon`),
  `alamat` = VALUES(`alamat`),
  `jabatan` = VALUES(`jabatan`),
  `departemen` = VALUES(`departemen`),
  `tanggal_bergabung` = VALUES(`tanggal_bergabung`);

-- =====================================================
-- 6) Seed data: HR admin user
-- =====================================================
-- HR Admin: username=HR0001, password=Admin123!
INSERT INTO `users` (`karyawan_id`, `username`, `password`, `role`, `is_active`)
VALUES (NULL, 'HR0001', '$2y$12$moqRBWZKjIcFUeQ1bGviOOeY9QJlLsSU.Qk6u9n038RTILA87KDMy', 'hr', 1)
ON DUPLICATE KEY UPDATE
  `password` = VALUES(`password`),
  `role` = VALUES(`role`),
  `is_active` = VALUES(`is_active`);
