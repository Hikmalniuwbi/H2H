-- ==============================================================
-- Seed Data untuk Hearts2Hearts (db_hearts2hearts)
-- Menyiapkan data awal Admin, Members, Albums, Songs, Merchandise,
-- Event Categories, Events, News, dan Gallery
-- ==============================================================

USE `db_hearts2hearts`;

-- 1. Default Admin User (Password: admin123)
-- Hash: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Super Administrator', 'admin@h2h.com', '$2y$10$pD7BBZLaeT.rK3kT4vM6DuB5LGHsHuvTP7uZ/V9t3sFVftTPxyVYa', 'admin', NOW()),
(2, 'S2U Fan', 'fan@h2h.com', '$2y$10$pD7BBZLaeT.rK3kT4vM6DuB5LGHsHuvTP7uZ/V9t3sFVftTPxyVYa', 'customer', NOW())
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`), `password` = VALUES(`password`);

-- 2. Event Categories
INSERT INTO `event_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Fan Meeting', 'Pertemuan eksklusif dan interaktif antara anggota Hearts2Hearts dengan para penggemar.'),
(2, 'Concert', 'Konser panggung megah tur nasional dan internasional.'),
(3, 'Festival', 'Penampilan di festival musik terbuka.')
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- 3. Members
INSERT INTO `members` (`member_id`, `name`, `position`, `birth_date`, `height_cm`, `blood_type`, `mbti`, `zodiac`, `nationality`, `debut_year`, `about`, `fun_facts`, `profile_image`) VALUES
(1, 'Jiwoo', 'Leader, Main Vocalist', '2002-03-15', 167, 'O', 'ENFJ', 'Pisces', 'Korea', 2025, 'Jiwoo is a warm and dedicated leader. With strong vocals and captivating stage charisma, she is the main foundation of the group.', 'Can cook 12 traditional Korean dishes\nHas a pet cat named Mochi\nFavorite color: pastel blue', 'assets/member_group/jiwoo.png'),
(2, 'Ian', 'Lead Dancer, Sub Vocalist', '2003-07-22', 172, 'A', 'ESTP', 'Cancer', 'Korea', 2025, 'Ian is a dance machine with explosive stage energy. His precise dance movements always become the highlight of every choreography.', 'Can mimic dance moves after watching a video just once\nHas a sneaker collection of over 50 pairs', 'assets/member_group/ian.png'),
(3, 'Carmen', 'Main Rapper, Lead Dancer', '2002-01-10', 165, 'B', 'ENTP', 'Capricorn', 'Korea', 2025, 'Carmen brings sharp flow and fierce stage presence. Her rap delivery adds an edgy flavor to Hearts2Hearts music.', 'Writes her own rap verses\nLoves skateboarding', 'assets/member_group/carmen.png'),
(4, 'Stella', 'Lead Vocalist, Visual', '2003-11-04', 168, 'AB', 'ISFP', 'Scorpio', 'Korea', 2025, 'Stella captivates hearts with her crystal-clear vocals and elegant aura. Known for her gentle demeanor.', 'Plays acoustic guitar and piano\nLoves stargazing', 'assets/member_group/stella.png'),
(5, 'Yuha', 'Main Dancer, Sub Rapper', '2004-05-18', 166, 'O', 'ESFP', 'Taurus', 'Korea', 2025, 'Yuha is energetic and expressive on stage, commanding attention with sharp choreographic timing.', 'Loves baking cookies for the members\nAlways carries hand sanitizer', 'assets/member_group/yuha.png'),
(6, 'Yeon', 'Lead Vocalist, Center', '2003-09-02', 169, 'A', 'INFJ', 'Virgo', 'Korea', 2025, 'Yeon balances vocal harmony and expressive stage center roles with quiet confidence and unmatched dedication.', 'Night owl who loves writing journals\nFavorite season: Autumn', 'assets/member_group/yeon.png'),
(7, 'Ana', 'Sub Vocalist, Maknae', '2005-12-14', 164, 'O', 'ENFP', 'Sagittarius', 'Korea', 2025, 'Ana is the beloved youngest member who brings sunshine and infectious energy wherever she goes.', 'Huge fan of anime\nFastest runner in the group', 'assets/member_group/ana.png'),
(8, 'Juun', 'Lead Rapper, Sub Vocalist', '2002-08-30', 170, 'B', 'INTP', 'Virgo', 'Korea', 2025, 'Juun brings cool sophistication and rhythm to every track, blending rap delivery with modern electronic beats.', 'Composes beat tracks in his studio\nLoves vintage fashion', 'assets/member_group/juun.jpg')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. Albums & Songs
INSERT INTO `albums` (`album_id`, `album_title`, `release_date`, `album_type`, `cover_image`) VALUES
(1, 'The Chase', '2025-01-10', 'Single', 'assets/cover_album/The_Chase.jpg'),
(2, 'Style', '2025-02-14', 'Single', 'assets/cover_album/Style.jpg'),
(3, 'Focus', '2025-03-20', 'Mini Album', 'assets/cover_album/Focus.jpg'),
(4, 'Pretty Please', '2025-04-18', 'Single', 'assets/cover_album/Pretty_Please.jpg'),
(5, 'Focus (Remixes)', '2025-05-01', 'Remix Album', 'assets/cover_album/Focus_Remix.jpg'),
(6, 'Rude', '2025-06-15', 'Single', 'assets/cover_album/Rude.jpg'),
(7, 'Flutter', '2025-07-20', 'Single', 'assets/cover_album/Flutter.jpg'),
(8, 'Blue Moon', '2025-08-12', 'Single', 'assets/cover_album/Blue_Moon.jpg'),
(9, 'Apple Pie', '2025-09-05', 'Single', 'assets/cover_album/Apple_Pie.jpg')
ON DUPLICATE KEY UPDATE `album_title` = VALUES(`album_title`);

INSERT INTO `songs` (`song_id`, `album_id`, `song_title`, `duration`, `audio_file`) VALUES
(1, 1, 'The Chase', '00:03:15', 'assets/song/The_Chase.mp3'),
(2, 2, 'Style', '00:03:02', 'assets/song/Style.mp3'),
(3, 3, 'Focus', '00:03:24', 'assets/song/FOCUS.mp3'),
(4, 4, 'Pretty Please', '00:03:10', 'assets/song/Pretty_Please.mp3'),
(5, 5, 'Focus (Remix)', '00:03:30', 'assets/song/Focus_Remix.mp3'),
(6, 6, 'Rude', '00:02:58', 'assets/song/Rude.mp3'),
(7, 7, 'Flutter', '00:03:08', 'assets/song/Flutter.mp3'),
(8, 8, 'Blue Moon', '00:03:35', 'assets/song/Blue_Moon.mp3'),
(9, 9, 'Apple Pie', '00:02:55', 'assets/song/Apple_Pie.mp3')
ON DUPLICATE KEY UPDATE `song_title` = VALUES(`song_title`);

-- 5. Merchandise
INSERT INTO `merchandise` (`merchandise_id`, `product_name`, `category`, `description`, `price`, `stock`, `image`) VALUES
(1, 'Official Card Holder', 'accessories', 'Cute collectable card holder for your favorite photocards', 165000, 50, 'assets/product/card_holder.png'),
(2, 'Character Keychain', 'accessories', 'Official limited edition character keychain', 197000, 45, 'assets/product/keyring.webp'),
(3, 'Official Lightstick Ver 1', 'lightstick', 'Wireless bluetooth sync for concerts', 1850000, 20, 'assets/product/light_stick.webp'),
(4, 'Photocard Pack Summer Ed', 'photocard', 'Pack of 5 random signed photocards', 89000, 100, 'assets/product/photo_card.webp'),
(5, 'Premium Photo Holder Set', 'accessories', 'House of H : Hide & Seek special edition photo holder', 180000, 30, 'assets/product/photo_holder.png'),
(6, 'Official Photo Pack Collection', 'photocard', 'High-quality portrait sheets and concept photos', 145000, 60, 'assets/product/photo_pack.png'),
(7, 'Decorative Sticker Set', 'accessories', 'Hearts2Hearts theme stickers to decorate your journals', 55000, 150, 'assets/product/sticker_set.png'),
(8, '4-Cut Strip Photo Set', 'photocard', 'Exclusive House of H themed photo strips', 75000, 70, 'assets/product/strip_photo_set.png'),
(9, 'Beauty Styling Kit', 'accessories', 'Includes heart mini mirror, pink comb, and hair stickers', 135000, 40, 'assets/product/styling_set.png'),
(10, '\"Hearts2House\" Cozy Sweatshirt', 'apparel', 'Comfortable grey sweatshirt with embroidered typography', 450000, 25, 'assets/product/sweatshirt.png'),
(11, 'Hide & Seek Trading Card Set', 'photocard', 'Random photo card deck pack with special hidden elements', 95000, 80, 'assets/product/trading_card_set.png'),
(12, '\"First Heartbeat\" T-Shirt', 'apparel', 'Premium 24s cotton oversized fit', 225000, 35, 'assets/product/tshirt.webp')
ON DUPLICATE KEY UPDATE `product_name` = VALUES(`product_name`);

-- 6. Events
INSERT INTO `events` (`event_id`, `category_id`, `event_name`, `event_date`, `location`, `price`, `ticket_stock`, `ticket_link`, `description`) VALUES
(1, 1, 'HEARTS 2 HOUSE Fan Meeting in Jakarta', '2026-06-20 19:00:00', 'Grand Ballroom, JIExpo Kemayoran, Jakarta', 750000, 500, 'https://www.tiket.com', 'Official fan meeting tickets with exclusive member interactions.'),
(2, 2, 'First Heartbeat Tour: Surabaya', '2026-07-05 18:30:00', 'Grand City Convention Hall, Surabaya', 850000, 350, 'https://www.tiket.com', 'Concert live performance across regional city stops.'),
(3, 3, 'Hearts2Hearts Summer Festival Live in Bali', '2026-07-18 16:00:00', 'GWK Cultural Park, Bali', 600000, 800, 'https://www.tiket.com', 'Open-air summer music festival live stage.'),
(4, 2, 'Focus Live in Bandung', '2026-08-02 19:00:00', 'Eldorado Dome, Bandung', 550000, 400, 'https://www.tiket.com', 'Album promotion concert live showcase.')
ON DUPLICATE KEY UPDATE `event_name` = VALUES(`event_name`), `ticket_link` = VALUES(`ticket_link`);

-- 7. News
INSERT INTO `news` (`news_id`, `title`, `tagline`, `content`, `published_at`, `image`) VALUES
(1, 'Hearts2Hearts Announces Ticket Prices for HEARTS 2 HOUSE Fan Meeting in Jakarta', 'Headline', 'Official fan meeting tickets open next week with Early Bird and Regular categories. Don''t miss the chance to meet the members in person! Special stages and soundcheck benefits await.', '2026-06-08', 'assets/member_group/pg4.jpg'),
(2, 'Summer Single Concept Photo Teaser Released!', 'Teaser', 'Hearts2Hearts has released a series of concept photos for an exciting new music project, embracing vibrant summer pastel aesthetics.', '2026-06-01', 'assets/cover_album/Flutter.jpg'),
(3, 'Album \"First Heartbeat\" Surpasses 1 Million Streams', 'Milestone', 'Hearts2Hearts debut album reaches a global streaming milestone across digital music platforms within two weeks of launch.', '2026-05-28', 'assets/cover_album/The_Chase.jpg'),
(4, 'Official Lightstick Ver. 2 Pre-Orders Open', 'Merchandise', 'The newly designed official lightstick featuring Bluetooth sync technology is now available for pre-order at our Official Store.', '2026-05-15', 'assets/product/light_stick.webp'),
(5, 'Hearts2Hearts Wins \"Best Rookie\" Award', 'Achievement', 'The group takes home the prestigious Best Rookie award at the K-Pop Music Awards, expressing profound love to S2U.', '2026-05-01', 'assets/cover_album/Focus.jpg'),
(6, 'Summer Concert in Bali Announced', 'Concert', 'Hearts2Hearts announces a special open-air summer concert at GWK Cultural Park, Bali.', '2026-04-15', 'assets/cover_album/Blue_Moon.jpg')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 8. Gallery
INSERT INTO `gallery` (`gallery_id`, `member_id`, `caption`, `image`) VALUES
(1, 1, 'Precious Moments 1', 'assets/member_group/pg1.jpg'),
(2, 2, 'Precious Moments 2', 'assets/member_group/pg2.jpg'),
(3, 3, 'Precious Moments 3', 'assets/member_group/pg3.jpg'),
(4, 4, 'Precious Moments 4', 'assets/member_group/pg4.jpg'),
(5, 5, 'Precious Moments 5', 'assets/member_group/pg5.jpg'),
(6, 6, 'Precious Moments 6', 'assets/member_group/pg6.jpg')
ON DUPLICATE KEY UPDATE `caption` = VALUES(`caption`);

-- 9. Feedbacks
CREATE TABLE IF NOT EXISTS `feedbacks` (
    `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_feedbacks_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `feedbacks` (`feedback_id`, `user_id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 2, 'S2U Fan', 'fan@h2h.com', 'Halo Hearts2Hearts! Kami S2U selalu mendukung kalian. Mohon adakan fan meeting encore di kota lain juga ya!', NOW())
ON DUPLICATE KEY UPDATE `message` = VALUES(`message`);

