<?php

namespace App\CloudflareService;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Zone
{
    use CfOutputTrait;

    public DNS $dns;

    /**
     * @throws BindingResolutionException|\Throwable
     */
    public function __construct(
        public Connector $connector,
    ) {
        $this->connector->setEndpoint('zones');
        $this->dns = app()->make(DNS::class, [
            'zoneRecord' => [],
        ]);
    }

    /**
     * Return the list of the zones.
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return Collection
     */
    public function list(array $params = []): Collection
    {
        $result = Http::cloudflare()
            ->get($this->connector->getEndpoint(), $params)
            ->throw()
            ->collect();

        return $this->sendOutput($result);
    }

    /**
     * Find zone by given parameters.
     * If empty parameter is given, it will return the first zone.
     *
     * ToDo: Optimize this method.
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return DNS
     */
    public function find(array $params = []): DNS
    {
        $result = $this->list($params);

        foreach ($params as $key=>$val) {
            $result = $result->where($key, $val);
        }

        $firstResult = $result->first();

        if (blank($firstResult)) {
            $firstResult = collect();
        }

        $this->dns->setZone($firstResult);

        return $this->dns;
    }

    /**
     * @return DNS
     */
    public function dns(): DNS
    {
        return $this->dns;
    }
}
