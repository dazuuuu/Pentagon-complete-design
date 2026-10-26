-- Schema for App\Models\ClientTravel
CREATE TABLE IF NOT EXISTS client_travels (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NOT NULL,
  destination VARCHAR(200) NULL,
  travel_date DATE NOT NULL,
  return_date DATE NULL,
  status ENUM('planned','confirmed','completed','cancelled') NOT NULL DEFAULT 'planned',
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
