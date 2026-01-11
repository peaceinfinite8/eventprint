<?php
// public/migrate_dynamic_sections.php
require_once __DIR__ . '/../app/config/db.php';

$conn = db();

echo "<pre>";
echo "<h2>Migrating to Dynamic Homepage Sections</h2>";

// 1. Check if column exists
$check = $conn->query("SHOW COLUMNS FROM settings LIKE 'home_sections'");
if ($check->num_rows == 0) {
    // 2. Add column
    echo "Adding 'home_sections' column to settings table...\n";
    $conn->query("ALTER TABLE settings ADD COLUMN home_sections TEXT DEFAULT NULL");
    echo "Column added.\n";
} else {
    echo "Column 'home_sections' already exists.\n";
}

// 3. fetching existing settings
echo "Fetching existing settings...\n";
$settings = [];
$res = $conn->query("SELECT * FROM settings LIMIT 1");
if ($res && $res->num_rows > 0) {
    $settings = $res->fetch_assoc();
}

// 4. Construct JSON from old columns if JSON is empty
if (empty($settings['home_sections'])) {
    echo "Migrating old data to JSON format...\n";

    $sections = [];

    // Section 1 (Print) - Red - Standard
    if (!empty($settings['home_print_category_id'])) {
        $sections[] = [
            'id' => uniqid('sec_'),
            'label' => 'Section 1',
            'category_id' => $settings['home_print_category_id'],
            'theme' => 'red',
            'layout' => 'standard' // Banner Left
        ];
    }

    // Section 2 (Media) - Blue - Reverse
    if (!empty($settings['home_media_category_id'])) {
        $sections[] = [
            'id' => uniqid('sec_'),
            'label' => 'Section 2',
            'category_id' => $settings['home_media_category_id'],
            'theme' => 'blue',
            'layout' => 'reverse' // Banner Right
        ];
    }

    // Section 3 (Merch) - Custom - Standard
    if (!empty($settings['home_merch_category_id'])) {
        $sections[] = [
            'id' => uniqid('sec_'),
            'label' => 'Section 3',
            'category_id' => $settings['home_merch_category_id'],
            'theme' => 'custom',
            'layout' => 'standard' // Banner Left
        ];
    }

    if (empty($sections)) {
        // Default seed if no settings existed
        $sections = [
            ['id' => uniqid('sec_'), 'label' => 'New Section', 'category_id' => '0', 'theme' => 'red', 'layout' => 'standard']
        ];
    }

    $json = json_encode($sections);
    $escapedJson = $conn->real_escape_string($json);

    // UPDATE the single row
    $sql = "UPDATE settings SET home_sections = '$escapedJson'";

    if ($conn->query($sql)) {
        echo "Migration successful! Data saved to 'home_sections'.\n";
    } else {
        echo "Error saving data: " . $conn->error . "\n";
    }
} else {
    echo "Data already exists in 'home_sections'. Skipping migration.\n";
}

echo "\nDone.";
echo "</pre>";
