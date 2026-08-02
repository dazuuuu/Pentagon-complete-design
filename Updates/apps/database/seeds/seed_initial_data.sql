-- Seed initial content matching the existing site
INSERT INTO destinations (name, country, image_url, is_featured, sort_order, status) VALUES
('Masai Mara', 'Kenya', 'var(--green)', 1, 1, 'active'),
('Serengeti', 'Tanzania', 'var(--charcoal)', 1, 2, 'active'),
('Bwindi Forest', 'Uganda', 'var(--green-light)', 1, 3, 'active');

INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, status) VALUES
('Masai Mara Great Migration Safari', 1, 'Kenya', 'Wildlife Safari', '7 Days', 1850.00, 'Best Seller', 'Experience the best of Kenya with our expert guides.', 'active'),
('Serengeti & Ngorongoro Crater', 2, 'Tanzania', 'Wildlife Safari', '9 Days', 2200.00, 'Popular', 'Experience the best of Tanzania with our expert guides.', 'active'),
('Bwindi Gorilla Trekking Expedition', 3, 'Uganda', 'Gorilla Trekking', '5 Days', 2400.00, 'Adventure', 'Experience the best of Uganda with our expert guides.', 'active'),
('Kilimanjaro Summit — Machame Route', 2, 'Tanzania', 'Mountain Trek', '8 Days', 2100.00, 'Trekking', 'Experience the best of Tanzania with our expert guides.', 'active'),
('Rwanda Gorillas & Volcanoes', NULL, 'Rwanda', 'Gorilla Trekking', '4 Days', 2800.00, 'Exclusive', 'Experience the best of Rwanda with our expert guides.', 'active'),
('Zanzibar Beach & Spice Retreat', 2, 'Tanzania', 'Beach & Coastal', '6 Days', 1400.00, 'Relaxation', 'Experience the best of Tanzania with our expert guides.', 'active'),
('Amboseli & Tsavo Safari Circuit', 1, 'Kenya', 'Wildlife Safari', '6 Days', 1650.00, 'Value', 'Experience the best of Kenya with our expert guides.', 'active'),
('Victoria Falls & Botswana Safari', NULL, 'Botswana', 'Wildlife Safari', '10 Days', 3200.00, 'Premium', 'Experience the best of Botswana with our expert guides.', 'active'),
('Namibia Desert & Sossusvlei Dunes', NULL, 'Namibia', 'Wildlife Safari', '12 Days', 3600.00, 'Luxury', 'Experience the best of Namibia with our expert guides.', 'active');

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
