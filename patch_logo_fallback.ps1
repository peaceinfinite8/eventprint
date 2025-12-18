# Patch Script 2: Add logo fallback to navbar and footer
Write-Output "=== Logo Fallback Patch Script ==="
Write-Output ""

# Function to add fallback
function Add-LogoFallback {
    param($file)
    
    $backup = "$file.backup_" + (Get-Date -Format "yyyyMMdd_HHmmss")
    Copy-Item $file $backup
    Write-Output "✅ Backup: $backup"
    
    $content = Get-Content $file -Raw
    
    # Pattern 1: Simple uploadUrl with logo variable
    $pattern1 = '\<img\s+src="<\?=\s*uploadUrl\(\$(?:settings\[\'logo\'\]|logo)\)\s*\?>"\s*alt="[^"]*"'
    
    if ($content -match $pattern1) {
        # Replace with safe fallback
        $replacement = @'
<?php $logoPath = $settings['logo'] ?? $logo ?? ''; $logoFull = !empty($logoPath) ? uploadUrl($logoPath) : assetUrl('frontend/images/logo-placeholder.png'); ?>
                <img src="<?= $logoFull ?>" alt="<?= e($settings['site_name'] ?? 'Logo') ?>"
'@
        $content = $content -replace $pattern1, $replacement
        Set-Content -Path $file -Value $content -NoNewline
        Write-Output "✅ Added logo fallback to: $file"
        return $true
    }
    
    # Pattern 2: Direct variable echo
    $pattern2 = '\<img[^>]*src="<\?=\s*\$logo\s*\?>"'
    
    if ($content -match $pattern2) {
        Write-Output "⚠️  Found unsafe logo usage pattern in $file"
        Write-Output "    Manual review recommended"
        return $false
    }
    
    Write-Output "ℹ️  No logo image found in $file - skipping"
    return $false
}

# Patch navbar
Write-Output "`n1. Patching navbar..."
$navbarPatched = Add-LogoFallback -file "c:\xampp\htdocs\eventprint\views\frontend\partials\navbar.php"

# Patch footer  
Write-Output "`n2. Patching footer..."
$footerPatched = Add-LogoFallback -file "c:\xampp\htdocs\eventprint\views\frontend\partials\footer.php"

Write-Output "`n=== Summary ==="
if ($navbarPatched -or $footerPatched) {
    Write-Output "✅ Logo fallback patches applied successfully!"
} else {
    Write-Output "ℹ️  No patches needed or manual review required"
}
