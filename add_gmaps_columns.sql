-- Add Google Maps support
USE eventprint;

-- Add gmaps_url to our_store table
ALTER TABLE our_store 
ADD COLUMN gmaps_url VARCHAR(500) NULL 
AFTER description;

-- Add gmaps_embed to settings table  
ALTER TABLE settings 
ADD COLUMN gmaps_embed TEXT NULL;

SELECT 'Google Maps columns added successfully!' as status;
