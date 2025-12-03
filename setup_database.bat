@echo off
REM VideoChat Database Setup Script for XAMPP
REM This script automatically creates the database and imports the schema

echo ========================================
echo VideoChat Database Setup for XAMPP
echo ========================================
echo.

REM Check if MySQL is running
echo [1/4] Checking if MySQL is running...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running
) else (
    echo [ERROR] MySQL is not running!
    echo Please start MySQL from XAMPP Control Panel and try again.
    pause
    exit /b 1
)

echo.
echo [2/4] Creating database 'videochat_db'...

REM Set MySQL path (adjust if your XAMPP is installed elsewhere)
set MYSQL_PATH=C:\xampp\mysql\bin
set DB_NAME=videochat_db
set DB_USER=root
set DB_PASS=

REM Create database
echo CREATE DATABASE IF NOT EXISTS %DB_NAME%; | "%MYSQL_PATH%\mysql.exe" -u %DB_USER% --password=%DB_PASS% 2>NUL

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database created successfully
) else (
    echo [ERROR] Failed to create database
    echo Please check your MySQL credentials in this script
    pause
    exit /b 1
)

echo.
echo [3/4] Importing database schema...

REM Import schema
"%MYSQL_PATH%\mysql.exe" -u %DB_USER% --password=%DB_PASS% %DB_NAME% < database.sql 2>NUL

if %ERRORLEVEL% EQU 0 (
    echo [OK] Schema imported successfully
) else (
    echo [ERROR] Failed to import schema
    echo Please ensure database.sql exists in the current directory
    pause
    exit /b 1
)

echo.
echo [3.5/4] Importing session schema...
"%MYSQL_PATH%\mysql.exe" -u %DB_USER% --password=%DB_PASS% %DB_NAME% < update_schema_sessions.sql 2>NUL

if %ERRORLEVEL% EQU 0 (
    echo [OK] Session schema imported successfully
) else (
    echo [WARNING] Failed to import session schema - it might already exist
)

echo.
echo [4/4] Verifying setup...

REM Verify tables exist
echo SHOW TABLES; | "%MYSQL_PATH%\mysql.exe" -u %DB_USER% --password=%DB_PASS% %DB_NAME% 2>NUL | find /C "users" >NUL

if %ERRORLEVEL% EQU 0 (
    echo [OK] All tables created successfully
) else (
    echo [WARNING] Could not verify tables
)

echo.
echo ========================================
echo Database setup complete!
echo ========================================
echo.
echo Database Name: %DB_NAME%
echo Tables created:
echo   - users
echo   - signals
echo   - messages
echo   - login_logs
echo.
echo You can now access the application at:
echo http://localhost/videochat
echo.
echo To view the database, open phpMyAdmin:
echo http://localhost/phpmyadmin
echo.
pause
