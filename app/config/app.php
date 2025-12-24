<?php
// app/config/app.php
return [
    'name' => 'EventPrint',
    'base_url' => 'http://localhost/eventprint/public', // CLEAN PATH!
    'env' => 'local',
    'debug' => true,

    // Asset versioning for cache busting (update when JS/CSS changes)
    'ASSET_VERSION' => '20251220_0455',

    // Phase 7: Enhancement Feature Flag
    // Set to true to enable micro-interactions, skeleton loading, toast, lazy load, A11y
    // Can be controlled via environment variable: EP_UI_ENHANCED=false to disable
    'EP_UI_ENHANCED' => getenv('EP_UI_ENHANCED') === 'false' ? false : true, // Default: enabled
];
