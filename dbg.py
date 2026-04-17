CP1252_DECODE = {}
for b in range(256):
    try:
        CP1252_DECODE[b] = ord(bytes([b]).decode("cp1252"))
    except:
        CP1252_DECODE[b] = b
UNI_TO_CP1252 = {v: k for k, v in CP1252_DECODE.items()}
VARIATION_SELECTORS = {0xFE0F, 0xFE0E, 0x20E3}

with open("resources/views/employees/index.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

non_ascii = [(i, c) for i, c in enumerate(content) if ord(c) > 0x7F]
print("Total non-ASCII chars:", len(non_ascii))
print("First 5:", [(i, hex(ord(c))) for i, c in non_ascii[:5]])

# Try the match logic on position of first non-ASCII
for i, c in non_ascii[:1]:
    for L in [8,7,6,5,4,3,2]:
        chunk = content[i:i+L]
        m = [UNI_TO_CP1252.get(ord(ch)) for ch in chunk]
        print("L="+str(L), "mapped:", [hex(x) if x is not None else None for x in m])
        if None in m:
            print("  -> None in mapped, skip")
            continue
        try:
            orig = bytes(m).decode("utf-8")
            print("  -> decoded:", [hex(ord(x)) for x in orig], "len="+str(len(orig)))
        except Exception as e:
            print("  -> utf-8 decode fail:", e)