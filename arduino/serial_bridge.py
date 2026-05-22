"""
CleckBasket — Arduino RFID Serial Bridge
=========================================
Reads the card UID from Arduino (MFRC522) over USB Serial,
then POSTs it to localhost so the browser detects the tap.

Run this once before testing:   (already installed on this machine)
    pip install pyserial requests

Run the bridge:
    Double-click  start_rfid_bridge.bat
    -- OR --
    py serial_bridge.py
"""

import serial
import serial.tools.list_ports
import requests
import time
import sys

# ── CONFIGURATION ─────────────────────────────────────────────
COM_PORT   = 'AUTO'              # 'AUTO' = find Arduino automatically
BAUD_RATE  = 9600
SERVER_URL = 'http://localhost:8081/cleckbasket/backend/receive_card.php'
SECRET_KEY = 'CB-ARDUINO-2024'  # must match backend/receive_card.php
POLL_DELAY = 0.05               # seconds between serial reads
# ──────────────────────────────────────────────────────────────


ARDUINO_KEYWORDS = [
    'arduino', 'ch340', 'ch341', 'cp210', 'ftdi',
    'usb serial', 'usb-serial', 'uart'
]


def find_arduino_port():
    """Return the first COM port that looks like an Arduino."""
    ports = serial.tools.list_ports.comports()
    for p in ports:
        desc = (p.description or '').lower()
        if any(kw in desc for kw in ARDUINO_KEYWORDS):
            return p.device
    # Fallback: return the first available port if only one exists
    if len(ports) == 1:
        return ports[0].device
    return None


def list_ports():
    ports = serial.tools.list_ports.comports()
    if not ports:
        print("  (no serial ports found)")
    for p in ports:
        print(f"    {p.device:10s}  {p.description}")


def normalise_uid(raw: str) -> str:
    """Strip non-hex chars, uppercase — mirrors PHP normaliseUid()."""
    return ''.join(c for c in raw.upper() if c in '0123456789ABCDEF')


def send_to_server(uid: str) -> bool:
    try:
        resp = requests.post(
            SERVER_URL,
            json={'card_uid': uid, 'secret': SECRET_KEY},
            timeout=5
        )
        data = resp.json()
        if data.get('success'):
            print(f"  ✅ Server accepted UID: {uid}")
            return True
        else:
            print(f"  ⚠️  Server: {data.get('error','?')} — {data.get('message','')}")
            return False
    except requests.exceptions.ConnectionError:
        print("  ❌ Cannot reach server — is XAMPP / Apache running?")
        return False
    except Exception as exc:
        print(f"  ❌ HTTP error: {exc}")
        return False


def main():
    print("=" * 52)
    print("  CleckBasket RFID Serial Bridge  v1.0")
    print("=" * 52)

    # ── Resolve COM port ──────────────────────────────────────
    port = COM_PORT
    if port == 'AUTO':
        port = find_arduino_port()
        if port:
            print(f"\n  ✅ Arduino auto-detected on {port}")
        else:
            print("\n  Available serial ports:")
            list_ports()
            print("\n  ❌ Could not auto-detect Arduino.")
            print("     Plug in the USB cable and try again,")
            print("     or set COM_PORT manually at the top of this file.")
            input("\n  Press Enter to exit...")
            sys.exit(1)
    else:
        print(f"\n  Using configured port: {port}")

    print(f"  Connecting at {BAUD_RATE} baud...")

    try:
        ser = serial.Serial(port, BAUD_RATE, timeout=1)
    except serial.SerialException as exc:
        print(f"\n  ❌ Could not open {port}: {exc}")
        print("\n  Available ports:")
        list_ports()
        input("\n  Press Enter to exit...")
        sys.exit(1)

    # Give Arduino time to reset after serial connection
    time.sleep(2)
    ser.flushInput()

    print(f"  ✅ Connected to {port}\n")
    print("  Waiting for card tap...")
    print("  (Hold card near MFRC522 reader)\n")
    print("-" * 52)

    try:
        while True:
            if ser.in_waiting > 0:
                try:
                    line = ser.readline().decode('utf-8', errors='ignore').strip()
                except Exception:
                    continue

                if not line:
                    continue

                if line == 'RFID_READY':
                    print("  📡 Arduino RFID reader is ready")
                    continue

                if line.startswith('CARD:'):
                    raw_uid = line[5:]
                    norm    = normalise_uid(raw_uid)
                    print(f"\n  🃏 Card tapped  → {raw_uid}  (hex: {norm})")
                    send_to_server(raw_uid)
                    print("  Waiting for next tap...\n")

            time.sleep(POLL_DELAY)

    except KeyboardInterrupt:
        print("\n\n  Bridge stopped.")
        ser.close()
        sys.exit(0)


if __name__ == '__main__':
    main()
