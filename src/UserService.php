<?php
namespace Nodesol\Apiguard;

use Illuminate\Support\Facades\Http;

class UserService {
    public function getUser(): array
    {
        $url = config("apiguard.api_url");

        $data = config("apiguard.post_data");
        $headers = [
            'Accept' => 'application/json',
        ];
        foreach(config("apiguard.forwarded_headers") as $header) {
            $headers[$header] = request()->header($header);
        }
        \Log::info($data);
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
}