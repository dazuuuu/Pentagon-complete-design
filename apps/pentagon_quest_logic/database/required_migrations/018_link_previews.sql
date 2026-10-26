-- Schema for App\Models\LinkPreview
CREATE TABLE IF NOT EXISTS link_previews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  url VARCHAR(1000) NOT NULL,
  url_hash CHAR(40) NOT NULL,
  title VARCHAR(500) NULL,
  description TEXT NULL,
  image_url VARCHAR(1000) NULL,
  fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY link_previews_url_hash_unique (url_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
