-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_hearts2hearts
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albums` (
  `album_id` int NOT NULL AUTO_INCREMENT,
  `album_title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` date DEFAULT NULL,
  `album_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `albums`
--

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` VALUES (1,'The Chase','2025-01-10','Single','uploads/albums/d4415b3039cb5a4f3be39afb.png'),(2,'Style','2025-02-14','Single','assets/cover_album/Style.jpg'),(3,'Focus','2025-03-20','Mini Album','assets/cover_album/Focus.jpg'),(4,'Pretty Please','2025-04-18','Single','assets/cover_album/Pretty_Please.jpg'),(5,'Focus (Remixes)','2025-05-01','Remix Album','assets/cover_album/Focus_Remix.jpg'),(6,'Rude','2025-06-15','Single','assets/cover_album/Rude.jpg'),(7,'Flutter','2025-07-20','Single','assets/cover_album/Flutter.jpg'),(8,'Blue Moon','2025-08-12','Single','assets/cover_album/Blue_Moon.jpg'),(9,'Apple Pie','2025-09-05','Single','assets/cover_album/Apple_Pie.jpg'),(10,'Temp Album','2026-01-01','Single',NULL);
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_categories`
--

DROP TABLE IF EXISTS `event_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_categories`
--

LOCK TABLES `event_categories` WRITE;
/*!40000 ALTER TABLE `event_categories` DISABLE KEYS */;
INSERT INTO `event_categories` VALUES (1,'Fan Meeting','Pertemuan eksklusif dan interaktif antara anggota Hearts2Hearts dengan para penggemar.'),(2,'Concert','Konser panggung megah tur nasional dan internasional.'),(3,'Festival','Penampilan di festival musik terbuka.'),(4,'Temp Cat','Temp Desc');
/*!40000 ALTER TABLE `event_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` datetime DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category_id` int DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `ticket_stock` int DEFAULT '100',
  `ticket_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://www.tiket.com',
  PRIMARY KEY (`event_id`),
  KEY `fk_events_categories` (`category_id`),
  CONSTRAINT `fk_events_categories` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`category_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'HEARTS 2 HOUSE Fan Meeting in Jakarta','2026-06-20 19:00:00','Grand Ballroom, JIExpo Kemayoran, Jakarta','Official fan meeting tickets with exclusive member interactions.',1,NULL,750000.00,499,'https://www.tiket.com'),(2,'First Heartbeat Tour: Surabaya','2026-07-05 18:30:00','Grand City Convention Hall, Surabaya','Concert live performance across regional city stops.',2,NULL,850000.00,350,'https://www.tiket.com'),(3,'Hearts2Hearts Summer Festival Live in Bali','2026-07-18 16:00:00','GWK Cultural Park, Bali','Open-air summer music festival live stage.',3,NULL,600000.00,800,'https://www.tiket.com'),(4,'Focus Live in Bandung','2026-08-02 19:00:00','Eldorado Dome, Bandung','Album promotion concert live showcase.',2,NULL,550000.00,400,'https://www.tiket.com');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedbacks` (
  `feedback_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`),
  KEY `fk_feedbacks_user` (`user_id`),
  CONSTRAINT `fk_feedbacks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedbacks`
--

LOCK TABLES `feedbacks` WRITE;
/*!40000 ALTER TABLE `feedbacks` DISABLE KEYS */;
INSERT INTO `feedbacks` VALUES (1,2,'S2U Fan','fan@h2h.com','Halo Hearts2Hearts! Kami S2U selalu mendukung kalian. Mohon adakan fan meeting encore di kota lain juga ya!','2026-11-16 11:32:34');
/*!40000 ALTER TABLE `feedbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `gallery_id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`gallery_id`),
  KEY `fk_gallery_members` (`member_id`),
  CONSTRAINT `fk_gallery_members` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
INSERT INTO `gallery` VALUES (1,1,'assets/member_group/pg1.jpg','Precious Moments 1'),(2,2,'assets/member_group/pg2.jpg','Precious Moments 2'),(3,3,'assets/member_group/pg3.jpg','Precious Moments 3'),(4,4,'assets/member_group/pg4.jpg','Precious Moments 4'),(5,5,'assets/member_group/pg5.jpg','Precious Moments 5'),(6,6,'assets/member_group/pg6.jpg','Precious Moments 6');
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `member_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `blood_type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zodiac` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debut_year` year DEFAULT NULL,
  `height_cm` smallint DEFAULT NULL,
  `mbti` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fun_facts` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `profile_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Jiwoo','Leader, Main Vocalist','2002-03-15','O','Pisces',2025,167,'ENFJ','Korea','Jiwoo is a warm and dedicated leader. With strong vocals and captivating stage charisma, she is the main foundation of the group.','Can cook 12 traditional Korean dishes\nHas a pet cat named Mochi\nFavorite color: pastel blue','assets/member_group/jiwoo.png'),(2,'Ian','Lead Dancer, Sub Vocalist','2003-07-22','A','Cancer',2025,172,'ESTP','Korea','Ian is a dance machine with explosive stage energy. His precise dance movements always become the highlight of every choreography.','Can mimic dance moves after watching a video just once\nHas a sneaker collection of over 50 pairs','assets/member_group/ian.png'),(3,'Carmen','Main Rapper, Lead Dancer','2002-01-10','B','Capricorn',2025,165,'ENTP','Korea','Carmen brings sharp flow and fierce stage presence. Her rap delivery adds an edgy flavor to Hearts2Hearts music.','Writes her own rap verses\nLoves skateboarding','assets/member_group/carmen.png'),(4,'Stella','Lead Vocalist, Visual','2003-11-04','AB','Scorpio',2025,168,'ISFP','Korea','Stella captivates hearts with her crystal-clear vocals and elegant aura. Known for her gentle demeanor.','Plays acoustic guitar and piano\nLoves stargazing','assets/member_group/stella.png'),(5,'Yuha','Main Dancer, Sub Rapper','2004-05-18','O','Taurus',2025,166,'ESFP','Korea','Yuha is energetic and expressive on stage, commanding attention with sharp choreographic timing.','Loves baking cookies for the members\nAlways carries hand sanitizer','assets/member_group/yuha.png'),(6,'Yeon','Lead Vocalist, Center','2003-09-02','A','Virgo',2025,169,'INFJ','Korea','Yeon balances vocal harmony and expressive stage center roles with quiet confidence and unmatched dedication.','Night owl who loves writing journals\nFavorite season: Autumn','assets/member_group/yeon.png'),(7,'Ana','Sub Vocalist, Maknae','2005-12-14','O','Sagittarius',2025,164,'ENFP','Korea','Ana is the beloved youngest member who brings sunshine and infectious energy wherever she goes.','Huge fan of anime\nFastest runner in the group','assets/member_group/ana.png'),(8,'Juun','Lead Rapper, Sub Vocalist','2002-08-30','B','Virgo',2025,170,'INTP','Korea','Juun brings cool sophistication and rhythm to every track, blending rap delivery with modern electronic beats.','Composes beat tracks in his studio\nLoves vintage fashion','assets/member_group/juun.jpg');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchandise`
--

DROP TABLE IF EXISTS `merchandise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchandise` (
  `merchandise_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'accessories',
  PRIMARY KEY (`merchandise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchandise`
--

LOCK TABLES `merchandise` WRITE;
/*!40000 ALTER TABLE `merchandise` DISABLE KEYS */;
INSERT INTO `merchandise` VALUES (1,'Official Card Holder','Cute collectable card holder for your favorite photocards',165000.00,'assets/product/card_holder.png',47,'accessories'),(2,'Character Keychain','Official limited edition character keychain',197000.00,'assets/product/keyring.webp',43,'accessories'),(3,'Official Lightstick Ver 1','Wireless bluetooth sync for concerts',1850000.00,'assets/product/light_stick.webp',20,'lightstick'),(4,'Photocard Pack Summer Ed','Pack of 5 random signed photocards',89000.00,'assets/product/photo_card.webp',100,'photocard'),(5,'Premium Photo Holder Set','House of H : Hide & Seek special edition photo holder',180000.00,'assets/product/photo_holder.png',30,'accessories'),(6,'Official Photo Pack Collection','High-quality portrait sheets and concept photos',145000.00,'assets/product/photo_pack.png',60,'photocard'),(7,'Decorative Sticker Set','Hearts2Hearts theme stickers to decorate your journals',55000.00,'assets/product/sticker_set.png',150,'accessories'),(8,'4-Cut Strip Photo Set','Exclusive House of H themed photo strips',75000.00,'assets/product/strip_photo_set.png',70,'photocard'),(9,'Beauty Styling Kit','Includes heart mini mirror, pink comb, and hair stickers',135000.00,'assets/product/styling_set.png',40,'accessories'),(10,'\"Hearts2House\" Cozy Sweatshirt','Comfortable grey sweatshirt with embroidered typography',450000.00,'assets/product/sweatshirt.png',25,'apparel'),(11,'Hide & Seek Trading Card Set','Random photo card deck pack with special hidden elements',95000.00,'assets/product/trading_card_set.png',80,'photocard'),(12,'\"First Heartbeat\" T-Shirt','Premium 24s cotton oversized fit',225000.00,'assets/product/tshirt.webp',35,'apparel'),(13,'Temp Merch',NULL,50.00,NULL,10,'accessories');
/*!40000 ALTER TABLE `merchandise` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `news_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'Hearts2Hearts Announces Ticket Prices for HEARTS 2 HOUSE Fan Meeting in Jakarta','Headline','Official fan meeting tickets open next week with Early Bird and Regular categories. Don\'t miss the chance to meet the members in person! Special stages and soundcheck benefits await.','2026-06-08 00:00:00','assets/member_group/pg4.jpg'),(2,'Summer Single Concept Photo Teaser Released!','Teaser','Hearts2Hearts has released a series of concept photos for an exciting new music project, embracing vibrant summer pastel aesthetics.','2026-06-01 00:00:00','assets/cover_album/Flutter.jpg'),(3,'Album \"First Heartbeat\" Surpasses 1 Million Streams','Milestone','Hearts2Hearts debut album reaches a global streaming milestone across digital music platforms within two weeks of launch.','2026-05-28 00:00:00','assets/cover_album/The_Chase.jpg'),(4,'Official Lightstick Ver. 2 Pre-Orders Open','Merchandise','The newly designed official lightstick featuring Bluetooth sync technology is now available for pre-order at our Official Store.','2026-05-15 00:00:00','assets/product/light_stick.webp'),(5,'Hearts2Hearts Wins \"Best Rookie\" Award','Achievement','The group takes home the prestigious Best Rookie award at the K-Pop Music Awards, expressing profound love to S2U.','2026-05-01 00:00:00','assets/cover_album/Focus.jpg'),(6,'Summer Concert in Bali Announced','Concert','Hearts2Hearts announces a special open-air summer concert at GWK Cultural Park, Bali.','2026-04-15 00:00:00','assets/cover_album/Blue_Moon.jpg');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `merchandise_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `fk_order_items_orders` (`order_id`),
  KEY `fk_order_items_merchandise` (`merchandise_id`),
  CONSTRAINT `fk_order_items_merchandise` FOREIGN KEY (`merchandise_id`) REFERENCES `merchandise` (`merchandise_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,2,2,197000.00,394000.00),(2,2,1,2,165000.00,330000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  PRIMARY KEY (`order_id`),
  KEY `fk_orders_users` (`user_id`),
  CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'2026-11-16 17:43:14',394000.00,'Nama: Heartbeat Fan\nNo. HP: 085750410278\nAlamat/Email: Jalan desa kapur\r\nkomplek kota raya a35\nMetode Bayar: QRIS / GoPay / OVO','pending'),(2,2,'2026-11-16 17:44:29',330000.00,'Nama: Test Fan\nNo. HP: 08123456789\nAlamat/Email: Jakarta\nMetode Bayar: bank_transfer','pending'),(3,2,'2026-11-16 17:44:36',165000.00,'Nama: Budi Fan\nNo. HP: 081299998888\nAlamat/Email: Jl. Sudirman No. 10 Jakarta\nMetode Bayar: bank_transfer','pending');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `songs`
--

DROP TABLE IF EXISTS `songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `songs` (
  `song_id` int NOT NULL AUTO_INCREMENT,
  `album_id` int NOT NULL,
  `song_title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` time DEFAULT NULL,
  `audio_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`song_id`),
  KEY `fk_songs_albums` (`album_id`),
  CONSTRAINT `fk_songs_albums` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `songs`
--

LOCK TABLES `songs` WRITE;
/*!40000 ALTER TABLE `songs` DISABLE KEYS */;
INSERT INTO `songs` VALUES (1,1,'The Chase','00:03:15','assets/song/The_Chase.mp3'),(2,2,'Style','00:03:02','assets/song/Style.mp3'),(3,3,'Focus','00:03:24','assets/song/FOCUS.mp3'),(4,4,'Pretty Please','00:03:10','assets/song/Pretty_Please.mp3'),(5,5,'Focus (Remix)','00:03:30','assets/song/Focus_Remix.mp3'),(6,6,'Rude','00:02:58','assets/song/Rude.mp3'),(7,7,'Flutter','00:03:08','assets/song/Flutter.mp3'),(8,8,'Blue Moon','00:03:35','assets/song/Blue_Moon.mp3'),(9,9,'Apple Pie','00:02:55','assets/song/Apple_Pie.mp3'),(10,10,'Temp Song','03:30:00',NULL);
/*!40000 ALTER TABLE `songs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `role` enum('admin','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrator','admin@h2h.com','$2y$10$pD7BBZLaeT.rK3kT4vM6DuB5LGHsHuvTP7uZ/V9t3sFVftTPxyVYa',NULL,NULL,'admin','2026-11-16 17:28:33'),(2,'S2U Fan','fan@h2h.com','$2y$10$pD7BBZLaeT.rK3kT4vM6DuB5LGHsHuvTP7uZ/V9t3sFVftTPxyVYa',NULL,NULL,'customer','2026-11-16 17:28:33');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_hearts2hearts'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-16 22:21:43
