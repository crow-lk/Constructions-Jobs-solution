@echo off
ECHO =======================================
ECHO  API Troubleshooting Helper
ECHO =======================================

IF "%1"=="clear" (
    ECHO Clearing log files...
    DEL /F /Q "storage\logs\*.log"
    ECHO Log files cleared.
    EXIT /B
)

IF "%1"=="errors" (
    ECHO Displaying last 50 errors from API logs...
    FINDSTR /i "error exception failed" "storage\logs\*.log" | FINDSTR /i "api" > temp_errors.log
    ECHO Last 50 errors:
    ECHO =======================================
    TYPE temp_errors.log | MORE /E +1
    DEL /F /Q temp_errors.log
    EXIT /B
)

IF "%1"=="register" (
    ECHO Analyzing registration endpoint...
    FINDSTR /i "register" "storage\logs\*.log" > temp_register.log
    ECHO Registration logs:
    ECHO =======================================
    TYPE temp_register.log | MORE /E +1
    DEL /F /Q temp_register.log
    EXIT /B
)

IF "%1"=="test" (
    ECHO Running quick API test...
    ECHO.
    ECHO Testing /api/debug endpoint...
    curl -s -X GET http://staging.homebuilders.lk/api/debug
    ECHO.
    ECHO.
    ECHO Testing /api/register-test endpoint (GET)...
    curl -s -X GET http://staging.homebuilders.lk/api/register-test
    ECHO.
    ECHO.
    ECHO Testing /api/register-test endpoint (POST)...
    curl -s -X POST http://staging.homebuilders.lk/api/register-test -H "Content-Type: application/json" -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"password\",\"password_confirmation\":\"password\",\"role\":\"client\"}"
    ECHO.
    EXIT /B
)

ECHO Available commands:
ECHO   api-troubleshoot clear      - Clear all log files
ECHO   api-troubleshoot errors     - View the last API errors
ECHO   api-troubleshoot register   - View registration-related logs
ECHO   api-troubleshoot test       - Run a quick API test
