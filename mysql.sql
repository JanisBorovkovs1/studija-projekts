CREATE TABLE jb_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE jb_listings (
    id_listings INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    location VARCHAR(255),
    contact VARCHAR(255),
    description TEXT,
    price DECIMAL(6,2),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE jb_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    owner_id INT NOT NULL,
    applicant_id INT NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT DEFAULT 0
);
ALTER TABLE jb_applications 
MODIFY is_read TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE jb_users 
ADD role ENUM('user','admin') DEFAULT 'user';
UPDATE jb_users 
SET role = 'admin' 
WHERE email = 'your@email.com';