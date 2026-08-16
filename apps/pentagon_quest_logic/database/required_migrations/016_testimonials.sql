-- Schema for App\Models\Testimonial
CREATE TABLE IF NOT EXISTS testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NULL,
  author_name VARCHAR(150) NOT NULL,
  author_location VARCHAR(100) NULL,
  quote TEXT NOT NULL,
  accent_color VARCHAR(20) DEFAULT 'gold',
  sort_order INT DEFAULT 0,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
