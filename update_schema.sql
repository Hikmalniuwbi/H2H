-- Skrip migrasi pembaruan database untuk Hearts2Hearts
-- Jalankan setelah db_hearts2hearts.sql diimpor

USE `db_hearts2hearts`;

-- 1. Tambah kategori pada merchandise jika belum ada
ALTER TABLE `merchandise`
    ADD COLUMN `category` VARCHAR(50) DEFAULT 'accessories';

-- 2. Tambah harga, kuota tiket, dan link tiket eksternal pada events
ALTER TABLE `events`
    ADD COLUMN `price` DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN `ticket_stock` INT DEFAULT 100,
    ADD COLUMN `ticket_link` VARCHAR(255) DEFAULT 'https://www.tiket.com';

-- 3. Tabel order dan order_items dikhususkan hanya untuk merchandise
-- Hapus kolom event_id jika ada dan pastikan merchandise_id NOT NULL
DELETE FROM `order_items` WHERE `merchandise_id` IS NULL;
DELETE FROM `orders` WHERE `order_id` NOT IN (SELECT DISTINCT `order_id` FROM `order_items`);
ALTER TABLE `order_items`
    DROP COLUMN IF EXISTS `event_id`,
    MODIFY COLUMN `merchandise_id` INT NOT NULL;

-- 4. Set kategori merchandise yang sudah ada sesuai data awal
UPDATE `merchandise` SET `category` = 'accessories' WHERE `product_name` LIKE '%Holder%' OR `product_name` LIKE '%Keyring%' OR `product_name` LIKE '%Keychain%';
UPDATE `merchandise` SET `category` = 'lightstick' WHERE `product_name` LIKE '%Lightstick%' OR `product_name` LIKE '%Light Stick%';
UPDATE `merchandise` SET `category` = 'photocard' WHERE `product_name` LIKE '%Photocard%' OR `product_name` LIKE '%Photo%';
UPDATE `merchandise` SET `category` = 'apparel' WHERE `product_name` LIKE '%T-shirt%' OR `product_name` LIKE '%Sweatshirt%' OR `product_name` LIKE '%Shirt%';

-- 5. Set harga tiket default pada events yang sudah ada
UPDATE `events` SET `price` = 350000.00 WHERE `event_name` LIKE '%Fan Meeting%' AND (`price` IS NULL OR `price` = 0);
UPDATE `events` SET `price` = 450000.00 WHERE `event_name` LIKE '%Showcase%' AND (`price` IS NULL OR `price` = 0);
UPDATE `events` SET `price` = 750000.00 WHERE `event_name` LIKE '%Concert%' AND (`price` IS NULL OR `price` = 0);
UPDATE `events` SET `price` = 1200000.00 WHERE `event_name` LIKE '%World Tour%' AND (`price` IS NULL OR `price` = 0);
UPDATE `events` SET `price` = 250000.00 WHERE `price` IS NULL OR `price` = 0;

-- 6. Tabel feedbacks untuk menampung pesan dan feedback dari user fan S2U
CREATE TABLE IF NOT EXISTS `feedbacks` (
    `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_feedbacks_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

