-- Ce fichier contient le schéma de la base de données de l'application Cinephoria
-- Il n'a qu'un but strictement pédagogique et ne doit pas être utilisé en production
-- Il est préférable d'utiliser les migrations Doctrine pour gérer les changements de schéma

CREATE DATABASE cinephoria;

USE cinephoria;

SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;


CREATE TABLE `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `issue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_12AD233EA76ED395` (`user_id`),
  KEY `IDX_12AD233E54177093` (`room_id`),
  CONSTRAINT `FK_12AD233E54177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`),
  CONSTRAINT `FK_12AD233EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=743 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `movie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `imdb_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `duration` int NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimum_age` int NOT NULL,
  `is_team_favorite` tinyint(1) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `movie_genre` (
  `movie_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`movie_id`,`genre_id`),
  KEY `IDX_FD1229648F93B6FC` (`movie_id`),
  KEY `IDX_FD1229644296D31F` (`genre_id`),
  CONSTRAINT `FK_FD1229644296D31F` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FD1229648F93B6FC` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `movie_session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `movie_id` int NOT NULL,
  `room_id` int NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F0D297FA8F93B6FC` (`movie_id`),
  KEY `IDX_F0D297FA54177093` (`room_id`),
  CONSTRAINT `FK_F0D297FA54177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`),
  CONSTRAINT `FK_F0D297FA8F93B6FC` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ordertickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `status` int NOT NULL,
  `purchase_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EFA6AA2A76ED395` (`user_id`),
  CONSTRAINT `FK_6EFA6AA2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1763 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `quality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `movie_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_794381C6A76ED395` (`user_id`),
  KEY `IDX_794381C68F93B6FC` (`movie_id`),
  CONSTRAINT `FK_794381C68F93B6FC` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`),
  CONSTRAINT `FK_794381C6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `room` (
  `id` int NOT NULL AUTO_INCREMENT,
  `theater_id` int NOT NULL,
  `quality_id` int NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `columns` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_729F519BD70E4479` (`theater_id`),
  KEY `IDX_729F519BBCFC6D57` (`quality_id`),
  CONSTRAINT `FK_729F519BBCFC6D57` FOREIGN KEY (`quality_id`) REFERENCES `quality` (`id`),
  CONSTRAINT `FK_729F519BD70E4479` FOREIGN KEY (`theater_id`) REFERENCES `theater` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `seat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `number` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `for_disabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3D5C366654177093` (`room_id`),
  CONSTRAINT `FK_3D5C366654177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6931 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `theater` (
  `id` int NOT NULL AUTO_INCREMENT,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `movie_session_id` int NOT NULL,
  `ordertickets_id` int NOT NULL,
  `seat_id` int NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_97A0ADA388CF9CE3` (`movie_session_id`),
  KEY `IDX_97A0ADA37EC7D47F` (`ordertickets_id`),
  KEY `IDX_97A0ADA3C1DAFE35` (`seat_id`),
  CONSTRAINT `FK_97A0ADA37EC7D47F` FOREIGN KEY (`ordertickets_id`) REFERENCES `ordertickets` (`id`),
  CONSTRAINT `FK_97A0ADA388CF9CE3` FOREIGN KEY (`movie_session_id`) REFERENCES `movie_session` (`id`),
  CONSTRAINT `FK_97A0ADA3C1DAFE35` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
