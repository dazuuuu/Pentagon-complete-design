CREATE TABLE IF NOT EXISTS home_settings (
  setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
  setting_value TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posters (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  image_url VARCHAR(255) NOT NULL DEFAULT '',
  link_url VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO home_settings (setting_key, setting_value) VALUES
  ('site_logo', 'assets/images/logo.png'),
  ('home_hero_video', 'assets/videos/safaris.mp4'),
  ('home_show_posters', '1')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
