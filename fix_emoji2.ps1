$f = 'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views\layouts\app.blade.php'
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$t = [IO.File]::ReadAllText($f, [Text.Encoding]::UTF8)

# Business Licenses & Closure Reports: U+00F0 U+0178 U+201C U+201E  -> 📄 (U+1F4C4)
$old1 = [string]::new([char[]]@(0x00F0, 0x0178, 0x201C, 0x201E))
$new1 = [char]::ConvertFromUtf32(0x1F4C4)   # 📄

# Role Management: U+00F0 U+0178 U+201D U+0090 -> 🔐 (U+1F510)
$old2 = [string]::new([char[]]@(0x00F0, 0x0178, 0x201D, 0x0090))
$new2 = [char]::ConvertFromUtf32(0x1F510)   # 🔐

$count1 = ($t.Split($old1).Count - 1)
$count2 = ($t.Split($old2).Count - 1)
Write-Host "Pattern 1 (page-emoji) found: $count1 times"
Write-Host "Pattern 2 (lock-emoji) found: $count2 times"

$t = $t.Replace($old1, $new1)
$t = $t.Replace($old2, $new2)

[IO.File]::WriteAllText($f, $t, $utf8NoBom)
Write-Host "Done"
