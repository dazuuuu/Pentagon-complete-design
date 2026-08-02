ALTER TABLE enquiries
  ADD COLUMN status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending' AFTER interest;
