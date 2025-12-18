<?php
// Diagnostic Check #1: Verify PHP Execution
echo "✅ TEST 1 PASSED: PHP is executing!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __FILE__ . "<br>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<hr>";
echo "If you see this, PHP works. Proceeding to index.php test...";
