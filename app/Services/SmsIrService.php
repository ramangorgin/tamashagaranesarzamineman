<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsIrService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $lineNumber;
    protected string $baseUrl;

    public function __construct(array $config = [])
    {
        $this->apiKey = $config['api_key'] ?? config('smsir.api_key');
        $this->secretKey = $config['secret_key'] ?? config('smsir.secret_key');
        $this->lineNumber = $config['line_number'] ?? config('smsir.line_number');
        $this->baseUrl = rtrim($config['webservice_url'] ?? config('smsir.webservice_url'), '/') . '/';
    }

    protected function getToken(): ?string
    {
        $payload = [
            'UserApiKey' => $this->apiKey,
            'SecretKey' => $this->secretKey,
            'System' => 'laravel_custom_guzzle7'
        ];

        $response = Http::timeout(30)->post($this->baseUrl . 'api/Token', $payload);
        if (!$response->successful()) {
            return null;
        }
        return $response->json('TokenKey');
    }

    public function send($messages, $numbers, $sendDateTime = null): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['IsSuccessful' => false, 'Message' => 'Failed to get token'];
        }
        $messages = (array)$messages;
        $numbers = (array)$numbers;
        $body = [
            'Messages' => $messages,
            'MobileNumbers' => $numbers,
            'LineNumber' => $this->lineNumber,
        ];
        if ($sendDateTime !== null) {
            $body['SendDateTime'] = $sendDateTime; // format: yyyy-mm-dd HH:ii:ss ? depends on API
        }
        $response = Http::timeout(30)->withHeaders([
            'x-sms-ir-secure-token' => $token,
        ])->post($this->baseUrl . 'api/MessageSend', $body);
        return $response->json() ?? ['IsSuccessful' => false, 'Message' => 'Unknown error'];
    }

    public function credit(): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['IsSuccessful' => false, 'Message' => 'Failed to get token'];
        }
        $response = Http::timeout(30)->withHeaders([
            'x-sms-ir-secure-token' => $token,
        ])->get($this->baseUrl . 'api/credit');
        return $response->json() ?? [];
    }

    public function sendVerification(string $code, string $number, bool $log = false): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['IsSuccessful' => false, 'Message' => 'Failed to get token'];
        }
        $body = [
            'Code' => $code,
            'MobileNumber' => $number,
        ];
        $response = Http::timeout(30)->withHeaders([
            'x-sms-ir-secure-token' => $token,
        ])->post($this->baseUrl . 'api/VerificationCode', $body);
        return $response->json() ?? ['IsSuccessful' => false, 'Message' => 'Unknown error'];
    }

    public function ultraFastSend(array $parameters, int $templateId, string $number): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['IsSuccessful' => false, 'Message' => 'Failed to get token'];
        }
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[] = ['Parameter' => $key, 'ParameterValue' => $value];
        }
        $body = [
            'ParameterArray' => $params,
            'TemplateId' => $templateId,
            'Mobile' => $number,
        ];
        $response = Http::timeout(30)->withHeaders([
            'x-sms-ir-secure-token' => $token,
        ])->post($this->baseUrl . 'api/UltraFastSend', $body);
        return $response->json() ?? ['IsSuccessful' => false, 'Message' => 'Unknown error'];
    }
}
