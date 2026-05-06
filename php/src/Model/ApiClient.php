<?php

class ApiClient
{
    private string $baseUrl;

    public function __construct(string $baseUrl = 'http://localhost:8080')
    {
        $this->baseUrl = $baseUrl;
    }

    public function request(string $method, string $endpoint, array $data = null, array $headers = []): array|false
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $defaultHeaders = ['Content-Type: application/json'];
        $allHeaders = array_merge($defaultHeaders, $headers);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true) ?: [];
        }

        return false;
    }

    public function get(string $endpoint): array|false
    {
        return $this->request('GET', $endpoint);
    }

    public function getWithToken(string $endpoint, string $token): array|false
    {
        return $this->request('GET', $endpoint, null, ['Authorization: Bearer ' . $token]);
    }

    public function post(string $endpoint, array $data): array|false
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function put(string $endpoint, array $data): array|false
    {
        return $this->request('PUT', $endpoint, $data);
    }

    public function delete(string $endpoint): array|false
    {
        return $this->request('DELETE', $endpoint);
    }
}