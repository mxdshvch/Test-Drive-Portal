CREATE DATABASE IF NOT EXISTS `test_drive_portal`;
USE `test_drive_portal`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `car_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `car_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `car_models_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `car_brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `driver_license` varchar(20) NOT NULL,
  `license_issue_date` date NOT NULL,
  `car_model_id` int(11) NOT NULL,
  `desired_date` date NOT NULL,
  `desired_time` time NOT NULL,
  `payment_type` enum('cash','card') NOT NULL,
  `status` enum('pending','approved','completed','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `car_model_id` (`car_model_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`car_model_id`) REFERENCES `car_models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `car_brands` (`brand_name`) VALUES
('BMW'),
('Mercedes-Benz'),
('Audi'),
('Toyota'),
('Volkswagen');

INSERT INTO `car_models` (`brand_id`, `model_name`) VALUES
(1, 'X5'), (1, 'X3'), (1, '5 Series'), (1, '3 Series'), (1, '7 Series'),
(2, 'GLE'), (2, 'C-Class'), (2, 'E-Class'), (2, 'S-Class'), (2, 'GLC'),
(3, 'Q5'), (3, 'A4'), (3, 'A6'), (3, 'Q7'), (3, 'A8'),
(4, 'Camry'), (4, 'RAV4'), (4, 'Land Cruiser'), (4, 'Corolla'), (4, 'Highlander'),
(5, 'Tiguan'), (5, 'Passat'), (5, 'Golf'), (5, 'Touareg'), (5, 'Polo');

-- Demo admin account. Change ADMIN_LOGIN and password after import.
INSERT INTO `users` (`login`, `password`, `fullname`, `phone`, `email`) VALUES
('avto2024', '$2y$10$H6XLg1rFMjrH2J1rQI8Yoe/1D9RsL5wVUoYQcBxR7dLzWtQKKj3hO', 'Администратор', '+7(000)-000-00-00', 'admin@example.com');
