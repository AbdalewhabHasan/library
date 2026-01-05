# Create storage symbolic link for Laravel
$target = "C:\xampp\htdocs\library\storage\app\public"
$link = "C:\xampp\htdocs\library\public\storage"

# Check if link already exists
if (Test-Path $link) {
    Write-Host "Storage link already exists. Removing old link..."
    Remove-Item $link -Force
}

# Create the symbolic link
New-Item -ItemType SymbolicLink -Path $link -Target $target -Force

if (Test-Path $link) {
    Write-Host "Storage link created successfully!"
} else {
    Write-Host "Error: Failed to create storage link"
}

