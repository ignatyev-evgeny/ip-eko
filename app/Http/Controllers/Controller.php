<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class Controller
{

    public const PLACEMENT_HANDLER_URL = 'https://ip-eko.bitrix.expert';
    public const PLACEMENT_HANDLER_NAME = 'Баланс договора';


    public function bindPlacement(string $placement, string $auth, string $domain)
    {
        return $this->executeQuery(
            $domain,
            $auth,
            "placement.bind",
            "GET",
            [
                'auth' => $auth,
                'PLACEMENT' => $placement,
                'HANDLER' => self::PLACEMENT_HANDLER_URL,
                'LANG_ALL' => [
                    'ru' => [
                        'TITLE' => self::PLACEMENT_HANDLER_NAME,
                    ],
                ],
            ]
        );
    }

    public function unbindPlacement(string $placement, string $auth, string $domain): void {
        $this->executeQuery(
            $domain,
            $auth,
            "placement.unbind",
            "GET",
            [
                'auth' => $auth,
                'PLACEMENT' => $placement
            ]
        );
    }

    private function listPlacement(string $auth, string $domain)
    {
        return $this->executeQuery(
            $domain,
            $auth,
            "placement.list",
            "GET",
            [
                'auth' => $auth,
            ]
        );
    }

    public function getAvailablePlacements(string $auth, string $domain)
    {
        $pattern = '/^CRM_DYNAMIC_\d+_DETAIL_TAB$/';
        $placements = $this->listPlacement($auth, $domain);

        if(!empty($placements)) {
            return array_filter($placements, function($item) use ($pattern) {
                return preg_match($pattern, $item);
            });
        }

        return [];
    }

    public function executeQuery(
        string $domain,
        string $authID,
        string $endpoint,
        string $method,
        array $data,
        $pagination = false,
        $recursive = false,
    ) {
        $request = match ($method) {
            'GET' => Http::timeout(120)->get("https://$domain/rest/$endpoint", $data),
            'POST' => Http::timeout(120)->post("https://$domain/rest/$endpoint", $data),
        };

        if ($request->failed()) {

            $logData = [
                'request' => [
                    'domain' => $domain,
                    'authID' => $authID,
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'data' => $data,
                ],
                'response' => $request->json(),
            ];
            Log::channel('critical')->critical(json_encode($logData));

            return [];
        }

        if($pagination) {
            return $request->json();
        }

        if($recursive) {
            return $request->json()['result']['result'];
        }

        return $request->json()['result'];
    }

}
