drop schema `jrods`;
create schema `jrods`;

use `jrods`;

create user `jrods_public`@`localhost`;
grant UPDATE on `jrods`.* to `jrods_public`@`localhost`;

# Create Tables
create table if not exists `jrods`.`users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(45) not null,
	`pass_hash` longtext not null,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table if not exists `jrods`.`tablename` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(45) not null,
	`value` longtext not null,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

