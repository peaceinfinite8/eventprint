-- Migration 007: Blog External Links
-- Date: 2025-12-21
-- Purpose: Support external blog post links (Blogspot, Kompas, etc)

ALTER TABLE posts
ADD COLUMN external_url VARCHAR(500) NULL AFTER content,
ADD COLUMN link_target ENUM('_self','_blank') DEFAULT '_self' AFTER external_url;
