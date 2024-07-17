-- Drop existing tables if they exist
DROP TABLE IF EXISTS Messages;
DROP TABLE IF EXISTS Matches;
DROP TABLE IF EXISTS User_Interests;
DROP TABLE IF EXISTS Interests;
DROP TABLE IF EXISTS Travel_Preferences;
DROP TABLE IF EXISTS Profiles;
DROP TABLE IF EXISTS Locations;
DROP TABLE IF EXISTS Users;

-- Set SQL mode and start transaction
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create Users Table
CREATE TABLE Users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash CHAR(60) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Profiles Table
CREATE TABLE Profiles (
    profile_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES Users(user_id),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    birthdate DATE,
    gender ENUM('Male', 'Female', 'Non-binary', 'Prefer not to say') NOT NULL,
    bio TEXT,
    profile_photo TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Locations Table
CREATE TABLE Locations (
    location_id SERIAL PRIMARY KEY,
    location_name VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    region VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `Travel_Preferences` (
  `preference_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `origin_country_id` bigint(20) UNSIGNED NOT NULL,
  `destination_country_id` bigint(20) UNSIGNED NOT NULL,
  `travel_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `has_extra_space` tinyint(1) DEFAULT 0,
  `needs_space` tinyint(1) DEFAULT 0,
  `description` TEXT DEFAULT NULL,
  `number_of_travelers` INT(11) DEFAULT NULL,
  `accommodation_type` ENUM('hotel', 'hostel', 'airbnb', 'other') DEFAULT NULL,
  `preferences` TEXT DEFAULT NULL,
  `image_path` TEXT DEFAULT NULL,
  PRIMARY KEY (`preference_id`),
  CONSTRAINT `fk_origin_country` FOREIGN KEY (`origin_country_id`) REFERENCES `Countries` (`country_id`),
  CONSTRAINT `fk_destination_country` FOREIGN KEY (`destination_country_id`) REFERENCES `Countries` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Interests Table
CREATE TABLE Interests (
    interest_id SERIAL PRIMARY KEY,
    interest_name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create User Interests Table
CREATE TABLE User_Interests (
    user_id INT NOT NULL REFERENCES Users(user_id),
    interest_id INT NOT NULL REFERENCES Interests(interest_id),
    PRIMARY KEY (user_id, interest_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Matches Table
CREATE TABLE Matches (
    match_id SERIAL PRIMARY KEY,
    user_needing_space_id INT NOT NULL REFERENCES Users(user_id),
    user_with_extra_space_id INT NOT NULL REFERENCES Users(user_id),
    score INT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Messages Table
CREATE TABLE Messages (
    message_id SERIAL PRIMARY KEY,
    sender_id INT NOT NULL REFERENCES Users(user_id),
    receiver_id INT NOT NULL REFERENCES Users(user_id),
    message_text TEXT,
    sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Reviews Table
CREATE TABLE Reviews (
    review_id SERIAL PRIMARY KEY,
    reviewer_id INT NOT NULL REFERENCES Users(user_id),
    reviewee_id INT NOT NULL REFERENCES Users(user_id),
    rating INT,
    comment TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Countries` (
  `country_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert static countries
INSERT INTO `Countries` (`country_name`) VALUES
('Canada'),
('China'),
('Cameroon'),
('USA'),
('UK'),
('France'),
('Ghana'),
('Kenya'),
('Nigeria'),
('South Africa');
