# Запуск сервера
$proc = Start-Process -FilePath "C:\Projects\Go\1\blog\go\server.exe" -PassThru
Write-Host "Server started (PID: $($proc.Id))"

# Для остановки:
# Stop-Process -Id $proc.Id -Force