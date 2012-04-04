-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2012 at 06:15 PM
-- Server version: 5.1.61
-- PHP Version: 5.3.6-13ubuntu3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `1000pass_com`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `location` varchar(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;



CREATE TABLE IF NOT EXISTS `cake_sessions` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `data` text,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;


CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `login_url` text NOT NULL,
  `logout_url` varchar(250) NOT NULL,
  `username_field` text NOT NULL,
  `extra_field` varchar(100) NOT NULL,
  `password_field` text NOT NULL,
  `submit` text NOT NULL,
  `require_add_on` varchar(3) NOT NULL DEFAULT 'yes',
  `state` varchar(10) NOT NULL DEFAULT 'pending',
  `test_username` varchar(50) NOT NULL,
  `test_password` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

CREATE TABLE IF NOT EXISTS `sites_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `extra` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `order` int(11) NOT NULL,
  `description` text NOT NULL,
  `state` varchar(30) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sites_user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `user_id` (`user_id`),
  KEY `sites_user_id` (`sites_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS `thumbnails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;


CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(50) DEFAULT NULL,
  `password` char(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `state` varchar(30) NOT NULL DEFAULT 'pending',
  `country` varchar(50) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `birthdate` date NOT NULL,
  `token` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=105 ;

--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sites_users`
--
ALTER TABLE `sites_users`
  ADD CONSTRAINT `sites_users_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`),
  ADD CONSTRAINT `sites_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sites_users_ibfk_3` FOREIGN KEY (`sites_user_id`) REFERENCES `sites_users` (`id`);
SET FOREIGN_KEY_CHECKS=1;

