# Master Patch Script - Run all patches
Write-Output "======================================"
Write-Output "  EventPrint - Master Patch Script"
Write-Output "======================================"
Write-Output ""

$patchDir = "c:\xampp\htdocs\eventprint"
$scripts = @(
    "patch_add_apiList.ps1",
    "patch_logo_fallback.ps1"
)

$success = 0
$failed = 0

foreach ($script in $scripts) {
    $scriptPath = Join-Path $patchDir $script
    
    if (Test-Path $scriptPath) {
        Write-Output ""
        Write-Output ">>> Running: $script"
        Write-Output "---"
        
        try {
            & $scriptPath
            $success++
            Write-Output "---"
            Write-Output "✅ $script completed"
        } catch {
            Write-Output "❌ $script failed: $_"
            $failed++
        }
    } else {
        Write-Output "❌ Script not found: $script"
        $failed++
    }
}

Write-Output ""
Write-Output "======================================"
Write-Output "  Patch Summary"
Write-Output "======================================"
Write-Output "✅ Successful: $success"
if ($failed -gt 0) {
    Write-Output "❌ Failed: $failed"
}
Write-Output ""

if ($failed -eq 0) {
    Write-Output "🎉 All patches applied successfully!"
    Write-Output ""
    Write-Output "Next steps:"
    Write-Output "1. Test /api/products endpoint"
    Write-Output "2. Refresh frontend pages (Ctrl+F5)"
    Write-Output "3. Check console for errors"
    Write-Output "4. Verify logo displays (or placeholder)"
} else {
    Write-Output "⚠️  Some patches failed. Review errors above."
}
