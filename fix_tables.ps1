$content = Get-Content 'app/Controllers/Documents.php' -Raw

# Replace all table return statements to add border-collapse
$pattern = "return '<table' \. `$attributes \. '>';"
$replacement = @"
if (strpos(`$attributes, 'style=') === false) {
    `$attributes .= ' style="border-collapse: collapse; border: 1px solid #000;"';
} else {
    `$attributes = preg_replace('/style="([^"]*)"/', 'style="`$1 border-collapse: collapse; border: 1px solid #000;"', `$attributes);
}
return '<table' . `$attributes . '>';
"@

$content = $content -replace [regex]::Escape($pattern), $replacement

$content | Set-Content 'app/Controllers/Documents.php'
Write-Host "Fixed table borders"
