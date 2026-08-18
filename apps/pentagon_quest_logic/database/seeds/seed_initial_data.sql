-- Seed initial content matching the existing site.
-- Tours look up destination_id by name so auto-increment gaps cannot break the FK.

INSERT INTO destinations (name, country, image_url, is_featured, sort_order, status) VALUES
('Masai Mara', 'Kenya', 'var(--green)', 1, 1, 'active'),
('Serengeti', 'Tanzania', 'var(--charcoal)', 1, 2, 'active'),
('Bwindi Forest', 'Uganda', 'var(--green-light)', 1, 3, 'active');

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Masai Mara Great Migration Safari', id, 'Kenya', 'Wildlife Safari', '7 Days', 1850.00, 'Best Seller', 'Experience the best of Kenya with our expert guides.', 'active'
FROM destinations WHERE name = 'Masai Mara' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Serengeti & Ngorongoro Crater', id, 'Tanzania', 'Wildlife Safari', '9 Days', 2200.00, 'Popular', 'Experience the best of Tanzania with our expert guides.', 'active'
FROM destinations WHERE name = 'Serengeti' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Bwindi Gorilla Trekking Expedition', id, 'Uganda', 'Gorilla Trekking', '5 Days', 2400.00, 'Adventure', 'Experience the best of Uganda with our expert guides.', 'active'
FROM destinations WHERE name = 'Bwindi Forest' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Kilimanjaro Summit — Machame Route', id, 'Tanzania', 'Mountain Trek', '8 Days', 2100.00, 'Trekking', 'Experience the best of Tanzania with our expert guides.', 'active'
FROM destinations WHERE name = 'Serengeti' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
VALUES ('Rwanda Gorillas & Volcanoes', NULL, 'Rwanda', 'Gorilla Trekking', '4 Days', 2800.00, 'Exclusive', 'Experience the best of Rwanda with our expert guides.', 'active');

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Zanzibar Beach & Spice Retreat', id, 'Tanzania', 'Beach & Coastal', '6 Days', 1400.00, 'Relaxation', 'Experience the best of Tanzania with our expert guides.', 'active'
FROM destinations WHERE name = 'Serengeti' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
SELECT 'Amboseli & Tsavo Safari Circuit', id, 'Kenya', 'Wildlife Safari', '6 Days', 1650.00, 'Value', 'Experience the best of Kenya with our expert guides.', 'active'
FROM destinations WHERE name = 'Masai Mara' LIMIT 1;

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
VALUES ('Victoria Falls & Botswana Safari', NULL, 'Botswana', 'Wildlife Safari', '10 Days', 3200.00, 'Premium', 'Experience the best of Botswana with our expert guides.', 'active');

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status)
VALUES ('Namibia Desert & Sossusvlei Dunes', NULL, 'Namibia', 'Wildlife Safari', '12 Days', 3600.00, 'Luxury', 'Experience the best of Namibia with our expert guides.', 'active');

INSERT INTO gallery (title, category, image_url, sort_order, status) VALUES
('Lion at Sunrise', 'Wildlife', '', 1, 'active'),
('Elephant Herd', 'Wildlife', '', 2, 'active'),
('Kilimanjaro Peak', 'Landscape', '', 3, 'active'),
('Maasai Culture', 'Culture', '', 4, 'active'),
('Gorilla Trek', 'Adventure', '', 5, 'active'),
('Zanzibar Shores', 'Coastal', '', 6, 'active'),
('Serengeti Plains', 'Wildlife', '', 7, 'active'),
('Victoria Falls', 'Landscape', '', 8, 'active'),
('Cheetah Hunt', 'Wildlife', '', 9, 'active');

INSERT INTO testimonials (author_name, author_location, quote, accent_color, sort_order, status) VALUES
('Sarah Jenkins', 'United Kingdom', 'The most authentic safari experience I''ve ever had. Pentagon Quest''s attention to detail and knowledge of the land is unparalleled.', 'gold', 1, 'active'),
('Mark Thompson', 'USA', 'From the moment we landed in Nairobi, everything was seamless. The 4x4 expedition was rugged yet incredibly comfortable.', 'green', 2, 'active');

INSERT INTO experiences (title, description, status, sort_order) VALUES
('The Great Migration Expedition', 'Follow the wildebeest across the Masai Mara and Serengeti with expert guides.', 'active', 1),
('Cultural Immersion', 'Authentic encounters with Maasai communities connected to Africa''s living culture.', 'active', 2);

INSERT INTO offers (title, description, badge, status, sort_order) VALUES
('Early Bird Safari 2026', 'Book your 2026 safari by December and enjoy 15% off all inclusive packages.', 'Limited Time', 'active', 1),
('Self-Drive Expedition', 'Experience the freedom of Africa with our new fully-equipped 4x4 self-drive rentals.', 'New Launch', 'active', 2);

INSERT INTO blog_posts (title, category, excerpt, status, sort_order) VALUES
('Top 10 Safari Photography Tips', 'Photography', 'Capture the perfect shot with our expert guide to wildlife photography in the African bush.', 'active', 1),
('What to Pack for Your First Safari', 'Travel Guide', 'From neutral clothing to essential gear, here is everything you need to pack for your adventure.', 'active', 2),
('Understanding the Great Migration', 'Wildlife', 'A deep dive into one of nature''s greatest spectacles: the annual trek of millions of wildebeest.', 'active', 3),
('The Hidden Gems of Namibia', 'Destinations', 'Beyond the dunes: discovering the secret landscapes and wildlife of the Namib desert.', 'active', 4),
('A Guide to Cultural Etiquette', 'Culture', 'How to respectfully engage with local communities during your African safari.', 'active', 5),
('Sustainable Safari: Our Commitment', 'Sustainability', 'Learn how Pentagon Quest is working to preserve Africa''s wild spaces for future generations.', 'active', 6);

INSERT INTO service_offerings (title, description, status, sort_order) VALUES
('Wildlife Game Drives', 'Track the Big Five across Africa''s finest reserves with expert naturalist guides.', 'active', 1),
('Gorilla Trekking', 'Secure permits and handle all logistics for profound encounters in Uganda and Rwanda.', 'active', 2),
('Mountain Climbing', 'Guided ascents of Kilimanjaro and Mount Kenya with KPAP-certified porter welfare.', 'active', 3),
('Cultural Immersions', 'Authentic encounters with Maasai and other communities connecting you to living heritage.', 'active', 4),
('Photography Safaris', 'Specialist expeditions led by professionals with photography-optimised vehicles.', 'active', 5),
('Luxury Lodge Bookings', 'Exclusive access to Africa''s finest eco-lodges and private conservancies.', 'active', 6);

INSERT INTO service_tiers (name, price, features, is_popular, status, sort_order) VALUES
('Essential', 800.00, 'Shared 4WD Vehicle\nTented Camp Stay\nFull Board Meals', 0, 'active', 1),
('Classic', 1800.00, 'Private 4WD Vehicle\nMid-range Lodges\nAll Park Fees', 1, 'active', 2),
('Premium', 3500.00, 'Luxury Fly-in Safari\nExclusive Conservancies\nAll-Inclusive Drinks', 0, 'active', 3);
