# Build custom cp1252 table that handles undefined bytes (0x81,0x8D,0x8F,0x90,0x9D)
CP1252_DECODE = {}
for b in range(256):
    try:
        CP1252_DECODE[b] = ord(bytes([b]).decode("cp1252"))
    except:
        CP1252_DECODE[b] = b  # undefined: map byte to same U+00xx

UNI_TO_CP1252 = {v: k for k, v in CP1252_DECODE.items()}

VARIATION_SELECTORS = {0xFE0F, 0xFE0E, 0x20E3}

def fix_mojibake(text):
    result = []
    i = 0
    while i < len(text):
        c = text[i]
        if ord(c) <= 0x7F:
            result.append(c)
            i += 1
            continue
        fixed = False
        for length in [8, 7, 6, 5, 4, 3, 2]:
            chunk = text[i:i+length]
            try:
                mapped = [UNI_TO_CP1252.get(ord(ch)) for ch in chunk]
                if None in mapped:
                    continue
                orig = bytes(mapped).decode("utf-8")
                if len(orig) == 1 and ord(orig) > 0xFF:
                    result.append("&#x{:X};".format(ord(orig)))
                    i += length
                    fixed = True
                    break
                elif (len(orig) == 2 and ord(orig[0]) > 0xFF and
                      ord(orig[1]) in VARIATION_SELECTORS):
                    result.append("&#x{:X};".format(ord(orig[0])))
                    i += length
                    fixed = True
                    break
            except:
                continue
        if not fixed:
            result.append(c)
            i += 1
    return "".join(result)

import glob
targets = [
    "resources/views/assets/index.blade.php",
    "resources/views/audit/index.blade.php",
    "resources/views/employees/index.blade.php",
    "resources/views/reports/index.blade.php",
    "resources/views/tickets/index.blade.php",
    "resources/views/visits/index.blade.php",
]
for path in targets:
    with open(path, "r", encoding="utf-8") as f:
        content = f.read()
    fixed = fix_mojibake(content)
    if fixed != content:
        with open(path, "w", encoding="utf-8", newline="") as f:
            f.write(fixed)
        print("Fixed: " + path)
    else:
        print("No change: " + path)