<?php
namespace Nodesol\Apiguard;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserService {
    public function getUser(): array
    {
        $cached = $this->checkCache();
        if($cached && is_array($cached) && count($cached) > 0) {
            return $cached;
        }

        $url = config("apiguard.api_url");

        $data = config("apiguard.post_data");
        $headers = [
            'Accept' => 'application/json',
        ];
        foreach(config("apiguard.forwarded_headers") as $header) {
            $headers[$header] = request()->header($header);
        }
        $response = Http::withHeaders($headers)->withBody($data, 'application/json')->post($url);

        if ($response->failed()) {
            \Log::error('Auth API Request Failed', ['response' => $response->body()]);
            throw new \Exception("Auth API Request Failed");
        }

        $result = $response->json();
        foreach(explode(".", config("apiguard.user_column", "")) as $key) {
            if(!isset($result[$key])) {
                \Log::error('Auth API Request Failed', ['response' => $response->body()]);
                throw new \Exception("Auth API returned an invalid response");
            }
            $result = $result[$key];
        }

        return $result;
    }
    
    public function checkCache(): ?array {
        $key = $this->cacheKey();

        if ($key === null) {
            return null;
        }

        $data = Cache::store(config('apiguard.cache_store'))->get($key);

        if (! is_array($data) || ! isset($data['tokenable'])) {
            return null;
        }

        return $data["tokenable"];
    }

    protected function cacheKey(): ?string
{
        $hashedToken = $this->hashToken(request()->header('Authorization'));

        if ($hashedToken === null) {
            return null;
        }

        $teamId = request()->header('X-COMPANY-ID') ?: 'none';

        return 'auth_token_'.$teamId.'_'.$hashedToken;
    }

    public function hashToken(?string $token) : ?string
    {
        if ($token === null || ! is_string($token) || $token === '') {
            return null;
        }

        if (str_contains($token, '|')) {
            [, $token] = explode('|', $token, 2);
        }

        return hash('sha256', hash('sha256', $token));
    }
}