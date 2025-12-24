-- Create activity_logs table
USE eventprint;

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

-- Insert a test log entry
INSERT INTO activity_logs (level, source, message, context) VALUES 
('info', 'system', 'System Logs feature initialized', '{"version": "1.0", "admin": "superadmin"}');

SELECT 'activity_logs table created successfully!' as status;
