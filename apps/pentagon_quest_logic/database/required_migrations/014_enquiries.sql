-- Schema for App\Models\Enquiry
CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(30) NULL,
  interest VARCHAR(200) NULL,
  offer_id INT UNSIGNED NULL,
  message TEXT NULL,
  status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  ip_address VARCHAR(45) NULL,
  email_sent TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
