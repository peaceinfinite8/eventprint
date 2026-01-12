<?php
/* ============================================================================
   app/config/app.php — App Config
   ========================================================================== */

return [
    'name' => 'EventPrint',
    'base_url' => 'http://localhost/eventprint',
    'env' => 'local',
    'debug' => true,

    /* Cache busting (bump when CSS/JS changes) */
    'ASSET_VERSION' => '20251220_0455',

    /* UI enhancements feature flag (EP_UI_ENHANCED=false to disable) */
    'EP_UI_ENHANCED' => getenv('EP_UI_ENHANCED') === 'false' ? false : true,
];
