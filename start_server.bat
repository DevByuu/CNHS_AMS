@echo off


echo Starting Laravel Server...
start cmd /k php artisan serve

echo Starting Python Serial Bridge...
start cmd /k python python_serial_bridge.py

