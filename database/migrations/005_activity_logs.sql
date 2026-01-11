-- Migration 005: Activity Logs Table
-- Date: 2025-12-21
-- Purpose: Realtime log viewer for super admin

CREATE TABLE IF NOT EXISTS activity_logs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  level ENUM('info','warning','error') NOT NULL,
  source ENUM('api','admin','system') NOT NULL,
  message VARCHAR(255) NOT NULL,
  context JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created (created_at),
  INDEX idx_level (level),
  INDEX idx_source (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
