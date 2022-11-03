<?php

namespace App\IfconfigService;

use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;

class Ifconfig
{
    /**
     * @return array
     */
    #[ArrayShape(['ipAddress' => 'string', 'cached' => 'bool'])]
    public function query(): array
    {
        $baseUrl = 'https://ifconfig.co/json';
        $jsonResult = Http::timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get($baseUrl)
            ->json();

        cache()->put('ip', $jsonResult['ip'], now()->addMinutes(5));

        return [
            'ipAddress' => $jsonResult['ip'],
            'cached' => false,
        ];
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    #[ArrayShape(['ipAddress' => 'string', 'cached' => 'bool'])]
    public function get(): array
    {
        if (cache()->has('ip')) {
            $ip = cache()->get('ip');

            return [
                'ipAddress' => $ip,
                'cached' => true,
            ];
        }

        return $this->query();
    }
}
