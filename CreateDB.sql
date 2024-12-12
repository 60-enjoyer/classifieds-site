CREATE DATABASE IF NOT EXISTS Alex;

USE Alex;

CREATE TABLE IF NOT EXISTS Users (
   id INT PRIMARY KEY AUTO_INCREMENT,
   username VARCHAR(50),
   pass VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS Categories (
   id INT PRIMARY KEY AUTO_INCREMENT,
	cat_name VARCHAR(255) NOT NULL
);

-- INSERT INTO Users (username, pass) VALUES ('qwer', 'qwer');

CREATE TABLE IF NOT EXISTS advts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    photo_name VARCHAR(255) UNIQUE NOT NULL,
    ad_name VARCHAR(255) NOT NULL,
    ad_desc TEXT NOT NULL,
    ad_cost INT NOT NULL,
    ad_category INT,
    FOREIGN KEY (ad_category) REFERENCES Categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

SELECT * FROM Categories;
SELECT * FROM advts;

DELETE FROM advts;

