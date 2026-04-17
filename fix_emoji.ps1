$f = 'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\layouts\app.blade.php'
$t = [IO.File]::ReadAllText($f, [Text.Encoding]::UTF8)

# Find ALL ð (U+00F0) occurrences - start of 4-byte emoji mojibake
$pos = 0
while ($true) {
    $idx = $t.IndexOf([char]0x00F0, $pos)
    if ($idx -lt 0) { break }
    $len = [Math]::Min(5, $t.Length - $idx)
    $s = $t.Substring($idx, $len)
    $hex = ($s.ToCharArray() | ForEach-Object { '{0:X4}' -f [int]$_ }) -join ' '
    # Get surrounding context
    $ctxStart = [Math]::Max(0, $idx - 10)
    $ctx = $t.Substring($ctxStart, [Math]::Min(30, $t.Length - $ctxStart))
    $ctx = $ctx -replace "`n",' ' -replace "`r",''
    Write-Host "Pos $idx hex=$hex  ctx=[$ctx]"
    $pos = $idx + 1
}

# Also find â (U+00E2) occurrences that look like mojibake
Write-Host "--- E2 patterns ---"
$pos = 0
while ($true) {
    $idx = $t.IndexOf([char]0x00E2, $pos)
    if ($idx -lt 0) { break }
    $len = [Math]::Min(4, $t.Length - $idx)
    $s = $t.Substring($idx, $len)
    $hex = ($s.ToCharArray() | ForEach-Object { '{0:X4}' -f [int]$_ }) -join ' '
    $ctxStart = [Math]::Max(0, $idx - 5)
    $ctx = $t.Substring($ctxStart, [Math]::Min(25, $t.Length - $ctxStart))
    $ctx = $ctx -replace "`n",' ' -replace "`r",''
    Write-Host "Pos $idx hex=$hex  ctx=[$ctx]"
    $pos = $idx + 1
}
