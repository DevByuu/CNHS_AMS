"""
Python Serial Bridge for Arduino RFID Scanner with LED Control
Reads RFID from Arduino and sends to Laravel API
Sends SUCCESS/ERROR commands back to Arduino to control LEDs
"""

import serial
import requests
import time
from datetime import datetime

# Configuration
ARDUINO_PORT = 'COM3'  # Change to your port
BAUD_RATE = 9600
API_URL = 'http://localhost:8000/api/rfid/scan'

def send_led_command(ser, command):
    """Send LED command to Arduino"""
    try:
        ser.write(f"{command}\n".encode())
        ser.flush()
        print(f"   LED: {command}")
    except Exception as e:
        print(f"   ⚠️ LED command failed: {e}")

def send_to_api(rfid, ser):
    """Send RFID to Laravel API and control LEDs"""
    try:
        response = requests.post(API_URL, 
            json={'rfid': rfid},
            timeout=5
        )
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                action = data.get('action', 'unknown')
                student = data.get('student', {})
                
                print(f"\n✅ {data.get('message')}")
                print(f"   Name: {student.get('name')}")
                print(f"   LRN: {student.get('lrn')}")
                print(f"   Grade: {student.get('grade')}")
                
                if action == 'check_in':
                    print(f"   Time In: {student.get('time_in')}")
                elif action == 'check_out':
                    print(f"   Time Out: {student.get('time_out')}")
                    print(f"   Duration: {student.get('duration')}")
                
                # Send SUCCESS command to Arduino → Blue LED flashes
                send_led_command(ser, 'SUCCESS')
            else:
                print(f"\n❌ {data.get('message')}")
                # Send ERROR command to Arduino → Red LED flashes
                send_led_command(ser, 'ERROR')
        else:
            print(f"\n❌ Server error: {response.status_code}")
            send_led_command(ser, 'ERROR')
            
    except requests.exceptions.ConnectionError:
        print("\n❌ Cannot connect to server. Is Laravel running?")
        send_led_command(ser, 'ERROR')
    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
        send_led_command(ser, 'ERROR')

def main():
    """Main loop"""
    print("=" * 60)
    print("CNHS RFID Attendance System - Serial Bridge with LED")
    print("=" * 60)
    print(f"Arduino Port: {ARDUINO_PORT}")
    print(f"Baud Rate: {BAUD_RATE}")
    print(f"API URL: {API_URL}")
    print("=" * 60)
    print("\nConnecting to Arduino...")
    
    try:
        # Connect to Arduino
        ser = serial.Serial(ARDUINO_PORT, BAUD_RATE, timeout=1)
        time.sleep(2)  # Wait for Arduino reset
        
        print("✅ Connected to Arduino!")
        print("💡 Both LEDs should flash once (startup test)")
        print("\nWaiting for RFID scans...\n")
        
        while True:
            if ser.in_waiting > 0:
                line = ser.readline().decode('utf-8').strip()
                
                if line == "READY":
                    print("✅ Arduino is ready!")
                elif line.startswith("RFID:"):
                    rfid = line.replace("RFID:", "").strip()
                    print(f"\n📱 RFID Scanned: {rfid}")
                    print(f"   Time: {datetime.now().strftime('%I:%M:%S %p')}")
                    
                    # Send to API (will also control LED)
                    send_to_api(rfid, ser)
                    
    except serial.SerialException as e:
        print(f"\n❌ Serial Error: {e}")
        print(f"\nTroubleshooting:")
        print(f"1. Check if Arduino is connected")
        print(f"2. Verify port: {ARDUINO_PORT}")
        print(f"3. Close Arduino IDE Serial Monitor")
        
    except KeyboardInterrupt:
        print("\n\n👋 Shutting down...")
        
    finally:
        if 'ser' in locals() and ser.is_open:
            ser.close()
            print("Serial connection closed.")

if __name__ == "__main__":
    main()