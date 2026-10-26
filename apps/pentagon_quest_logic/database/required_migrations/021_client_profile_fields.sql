-- Adds richer profile fields to clients for manual registration
ALTER TABLE clients
  ADD COLUMN country_of_origin VARCHAR(100) NULL AFTER interest,
  ADD COLUMN purpose_of_visit VARCHAR(255) NULL AFTER country_of_origin,
  ADD COLUMN profile_picture VARCHAR(255) NULL AFTER purpose_of_visit;
