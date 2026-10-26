ALTER TABLE offers DROP FOREIGN KEY offers_ibfk_1;
ALTER TABLE offers DROP FOREIGN KEY offers_ibfk_2;
ALTER TABLE offers DROP COLUMN tour_id;
ALTER TABLE offers DROP COLUMN destination_id;
ALTER TABLE offers ADD COLUMN link_url VARCHAR(255) NULL AFTER badge;
ALTER TABLE offers ADD COLUMN video_url VARCHAR(255) NULL AFTER image_url;
