-- The shared site-wide "Send a Trip Request" form accepts an email OR a phone
-- number in a single field, so a submission may have no email at all.
ALTER TABLE enquiries MODIFY COLUMN email VARCHAR(150) NULL;
ALTER TABLE clients MODIFY COLUMN email VARCHAR(150) NULL;
