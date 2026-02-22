<?php
// src/Http/ApiClient.php

/**
 * ApiClient
 * ----------
 * Kapselt die HTTP-Kommunikation zur Shop-API.
 * Der SyncService ruft nur diese Klasse auf und kennt
 * keine cURL-Details.
 *
 * Rückgabeformat:
 * [
 *   'status' => int,     // HTTP Status Code
 *   'body'   => string,  // Response Body
 *   'error'  => ?string  // Fehlerbeschreibung bei Transportfehlern
 * ]
 */

class ApiClient
{
    private string $baseUrl;
    private int $timeoutSeconds;

    public function __construct(string $baseUrl, int $timeoutSeconds = 10)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeoutSeconds = $timeoutSeconds;
    }

    /**
     * Sendet ein Produkt an die Shop-API.
     * Endpoint: /api/products.php
     */
    public function postProduct(array $product): array
    {
        return $this->postJson('/api/products.php', $product);
    }

    /**
     * Führt einen JSON POST Request aus.
     */
    private function postJson(string $path, array $payload): array
    {
        $url = $this->baseUrl . $path;

        $json = json_encode($payload);
        if ($json === false) {
            return [
                'status' => 0,
                'body' => '',
                'error' => 'json_encode failed: ' . json_last_error_msg()
            ];
        }

        $ch = curl_init($url);

        if ($ch === false) {
            return [
                'status' => 0,
                'body' => '',
                'error' => 'curl_init failed'
            ];
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_POSTFIELDS => $json
        ]);

        $body = curl_exec($ch);

        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'status' => 0,
                'body' => '',
                'error' => 'curl_exec failed: ' . $error
            ];
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'body' => (string) $body,
            'error' => null
        ];
    }
}