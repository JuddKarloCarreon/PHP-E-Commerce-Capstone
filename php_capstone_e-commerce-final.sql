CREATE DATABASE  IF NOT EXISTS `php_e_commerce_capstone` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `php_e_commerce_capstone`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: php_e_commerce_capstone
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `amount` int unsigned NOT NULL DEFAULT '0' COMMENT 'Contains product_id and amount ordered',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_carts_users1_idx` (`user_id`),
  KEY `fk_user_cart_items_products1_idx` (`product_id`),
  CONSTRAINT `fk_user_cart_items_products1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_user_carts_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checkout_details`
--

DROP TABLE IF EXISTS `checkout_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkout_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `zip` varchar(20) NOT NULL COMMENT 'zip code can start with 0, so varchar is used',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkout_details`
--

LOCK TABLES `checkout_details` WRITE;
/*!40000 ALTER TABLE `checkout_details` DISABLE KEYS */;
INSERT INTO `checkout_details` VALUES (1,'asd','asd','asd','asd','asd','asd','2222','2024-02-26 00:09:14',NULL),(2,'asd','asd','asd','asd','asd','asd','3333','2024-02-26 00:11:14',NULL),(3,'asdasd','asd','asd','asdad','asd','asd','2222','2024-02-26 00:13:42',NULL),(4,'asdasd','cxvxcvbcxb','asd','asd','asd','asd','2222','2024-02-26 05:28:17',NULL),(5,'fgfgh','cvbcb','asd','asd','asd','asd','3333','2024-02-26 05:28:17',NULL),(6,'asd','asdsdf','asd','asd','asd','asd','2222','2024-02-26 05:42:39',NULL),(7,'asdsdf','asd','asd','asd','dfg','dfg','2222','2024-02-26 05:42:39',NULL);
/*!40000 ALTER TABLE `checkout_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `billing_id` int NOT NULL,
  `shipping_id` int NOT NULL,
  `status` tinyint unsigned DEFAULT '1',
  `products_json` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_users1_idx` (`user_id`),
  KEY `fk_orders_checkout_details1_idx` (`shipping_id`),
  KEY `fk_orders_checkout_details2_idx` (`billing_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_orders_checkout_details1` FOREIGN KEY (`shipping_id`) REFERENCES `checkout_details` (`id`),
  CONSTRAINT `fk_orders_checkout_details2` FOREIGN KEY (`billing_id`) REFERENCES `checkout_details` (`id`),
  CONSTRAINT `fk_orders_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,1,1,3,'{\"1\":\"10\",\"2\":\"12\"}',189.00,'2024-02-26 00:09:14','2024-02-26 05:43:01'),(2,1,2,2,3,'{\"2\":\"5\"}',75.00,'2024-02-26 00:11:14','2024-02-26 00:14:05'),(3,1,3,3,2,'{\"1\":\"4\"}',11.40,'2024-02-26 00:13:42','2024-02-26 05:26:49'),(4,1,5,4,3,'{\"1\":\"6\",\"2\":\"6\",\"3\":\"7\"}',189.60,'2024-02-26 05:28:17','2024-02-26 05:28:39'),(5,1,7,6,2,'{\"2\":\"5\"}',75.00,'2024-02-26 05:42:39','2024-02-26 05:43:14');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_type` tinyint unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `stock` int unsigned NOT NULL,
  `sold` int unsigned NOT NULL DEFAULT '0',
  `image_names_json` text,
  `rating` decimal(2,1) unsigned DEFAULT '0.0',
  `active` bit(1) NOT NULL DEFAULT b'1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_type` (`product_type`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,2,'test1','test test',1.60,12123,20,'[\"apple20240226000727.png\"]',3.8,_binary '\0','2024-02-26 00:07:27','2024-02-26 05:43:23'),(2,5,'test2','tes tfdhd',14.00,104,28,'[\"Chicken20240226000742.png\"]',4.0,_binary '','2024-02-26 00:07:42','2024-02-26 05:42:39'),(3,4,'teeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeest','asdxvcsweg',13.00,1338,7,'[\"all_products20240226052600.png\",\"Beef20240226052552.png\",\"apple20240226052552.png\",\"burger20240226052552.png\"]',3.6,_binary '','2024-02-26 05:25:52','2024-02-26 05:43:42'),(4,2,'sdf','xvcbr',1.24,114,0,'[\"Beef20240226054101.png\"]',0.0,_binary '','2024-02-26 05:40:52','2024-02-26 05:41:05');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `review_id` int unsigned NOT NULL,
  `content` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_replies_reviews1_idx` (`review_id`),
  KEY `fk_replies_users1_idx` (`user_id`),
  CONSTRAINT `fk_replies_reviews1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`),
  CONSTRAINT `fk_replies_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replies`
--

LOCK TABLES `replies` WRITE;
/*!40000 ALTER TABLE `replies` DISABLE KEYS */;
INSERT INTO `replies` VALUES (1,1,8,'asd','2024-02-26 03:56:40',NULL),(2,1,10,'asdasdasd','2024-02-26 03:57:22',NULL),(3,1,10,'asdasdasd','2024-02-26 03:57:52',NULL),(4,1,10,'xvxcvxcv','2024-02-26 03:57:54',NULL),(5,1,14,'asdasdasd','2024-02-26 05:29:28',NULL),(6,1,14,'asdasd','2024-02-26 05:29:30',NULL),(7,1,13,'zxczxc','2024-02-26 05:29:31',NULL),(8,1,12,'asdasd','2024-02-26 05:29:33',NULL),(9,1,14,'asdasd','2024-02-26 05:39:23',NULL),(10,1,17,'asdasd','2024-02-26 05:43:51',NULL),(11,1,17,'gdgdg','2024-02-26 05:43:54',NULL);
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `content` text NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_reviews_users1_idx` (`user_id`),
  KEY `fk_reviews_products1_idx` (`product_id`),
  CONSTRAINT `fk_reviews_products1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_reviews_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,1,'sample review',4,'2024-02-26 03:22:08',NULL),(2,1,1,'asd',4,'2024-02-26 03:23:24',NULL),(3,1,1,'asd',4,'2024-02-26 03:25:18',NULL),(4,1,1,'asd',4,'2024-02-26 03:26:05',NULL),(5,1,1,'asd',4,'2024-02-26 03:29:33',NULL),(6,1,1,'sample review',4,'2024-02-26 03:30:07',NULL),(7,1,1,'sample',4,'2024-02-26 03:30:52',NULL),(8,1,1,'test',2,'2024-02-26 03:48:24',NULL),(10,1,1,'asd',4,'2024-02-26 03:57:19',NULL),(11,1,1,'asdasdasdasdasd',4,'2024-02-26 04:18:34',NULL),(12,1,2,'review1',2,'2024-02-26 05:29:10',NULL),(13,1,2,'rrasdjfsdf',5,'2024-02-26 05:29:16',NULL),(14,1,2,'asdasdasd',5,'2024-02-26 05:29:21',NULL),(15,1,3,'bad product',1,'2024-02-26 05:43:34',NULL),(16,1,3,'asdasd',5,'2024-02-26 05:43:39',NULL),(17,1,3,'asdasd',5,'2024-02-26 05:43:42',NULL);
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_cards`
--

DROP TABLE IF EXISTS `user_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cards` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `number` varchar(19) NOT NULL,
  `expiration` varchar(7) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_cards_users1_idx` (`user_id`),
  CONSTRAINT `fk_user_cards_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_cards`
--

LOCK TABLES `user_cards` WRITE;
/*!40000 ALTER TABLE `user_cards` DISABLE KEYS */;
INSERT INTO `user_cards` VALUES (1,1,'asd','4242424242424242','2025-01','2024-02-26 00:09:14','2024-02-26 00:09:14'),(2,1,'asd','4242424242424242','2025-02','2024-02-26 00:13:42','2024-02-26 00:13:42'),(3,1,'aaaa','5555555555554444','2025-02','2024-02-26 05:28:17','2024-02-26 05:28:17'),(4,1,'fdsg','5555555555554444','2025-02','2024-02-26 05:42:39','2024-02-26 05:42:39');
/*!40000 ALTER TABLE `user_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `user_level` tinyint unsigned DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Judd','Carreon',1,'default.jpg','juddcarreon@email.com','68bc91130be36f3e6be34826c53e50f9','dbea0f78678b22b1a5b09d84c535d9c7eb21493c82cf','2024-02-26 00:07:12',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-26  6:57:45
