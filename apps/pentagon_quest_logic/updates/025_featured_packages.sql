CREATE TABLE IF NOT EXISTS featured_packages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hotel VARCHAR(200) NOT NULL,
  meal VARCHAR(100) NULL,
  location VARCHAR(120) NULL,
  two_nights_price VARCHAR(50) NULL,
  three_nights_price VARCHAR(50) NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO featured_packages (hotel, meal, location, two_nights_price, three_nights_price, status, sort_order) VALUES
  ('Turtle Bay Beach Club', 'All Inclusive', 'Watamu', '$205', '$289', 'active', 10),
  ('Papillon Lagoon Reef', 'All Inclusive', 'Diani', '$261', '$526', 'active', 20),
  ('Diani Reef Beach Resort & Spa', 'Breakfast', 'Diani', '$366', '$530', 'active', 30),
  ('Neptune Beach Hotel', 'All Inclusive', 'Bamburi', '$372', '$539', 'active', 40),
  ('Diani Sea Resort', 'All Inclusive', 'Diani', '$392', '$568', 'active', 50),
  ('Bamburi Beach Hotel', 'All Inclusive', 'Bamburi', '$445', '$649', 'active', 60),
  ('Diani Sea Lodge', 'All Inclusive', 'Diani', '$275', '$393', 'active', 70),
  ('Southern Pal Beach Resort', 'All Inclusive', 'Diani', '$507', '$741', 'active', 80),
  ('Leopard Beach Resort & Spa', 'Breakfast', 'Diani', '$215', '$303', 'active', 90);
