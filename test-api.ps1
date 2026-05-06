try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080/api/posts" -Method GET -UseBasicParsing
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Response: $($response.Content)"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
}