<?php

namespace App\CloudflareService;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DNS
{
    use CfOutputTrait;

    public function __construct(
        public Connector $connector,
        public Collection|array $zoneRecord,
    ) {
        if (isset($this->zoneRecord['id'])) {
            $this->setEndpoint();
        }
    }

    /**
     * @param Collection|array $zoneRecord
     *
     * @return $this
     */
    final public function setZone(Collection|array $zoneRecord): static
    {
        $this->zoneRecord = $zoneRecord;
        $this->setEndpoint();

        return $this;
    }

    /**
     * Set the endpoint for the API call.
     *
     * @return $this
     */
    final public function setEndpoint(): static
    {
        $this->connector->setEndpoint("zones/{$this->zoneRecord['id']}/dns_records");

        return $this;
    }

    /**
     * Return the list of the dns records.
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return Collection
     */
    public function list(array $params = []): Collection
    {
        $this->emptyZoneFallback($params);

        $result = Http::cloudflare()
            ->get($this->connector->getEndpoint(), $params)
            ->throw()
            ->collect();

        return $this->sendOutput($result);
    }

    /**
     * Update the DNS Record.
     *
     * @param string $dnsName
     * @param string $ipAddress
     * @param bool $proxied
     *
     * @throws \Exception
     *
     * @return Collection
     */
    public function update(string $dnsName, string $ipAddress, bool $proxied = true): Collection
    {
        $record = $this->find(['name' => $dnsName]);

        if ($record->isEmpty()) {
            throw new \Exception("DNS record with name {$dnsName} not found.");
        }

        $endpoint = "/zones/{$this->zoneRecord['id']}/dns_records/{$record['id']}";

        $result = Http::cloudflare()
            ->put($endpoint, [
                'type' => 'A',
                'name' => $dnsName,
                'content' => $ipAddress,
                'proxied' => $proxied,
            ])
            ->collect();

        return $this->sendOutput($result);
    }

    /**
     * Find DNS record by given parameters.
     *
     * @param array $params
     * @param array $fields
     *
     * @throws \Exception
     *
     * @return Collection|null
     */
    public function find(array $params = [], array $fields = []): ?Collection
    {
        $result = $this->list($params);

        foreach ($params as $key => $val) {
            $result = $result->where($key, $val);
        }

        $firstResult = collect($result->first());

        if (!blank($fields)) {
            return $firstResult->only($fields);
        }

        return $firstResult->only(['id', 'zone_id', 'zone_name', 'name', 'type', 'content', 'proxied', 'ttl', 'modified_on']);
    }

    /**
     * @param array $params
     *
     * @throws \Exception
     *
     * @return void
     */
    private function emptyZoneFallback(array $params)
    {
        if (isset($this->zoneRecord['id'])) {
            return;
        }

        if (isset($params['id'])) {
            $this->setZone($params);
        } else {
            app()->make(\App\CloudflareService\Cloudflare::class)->zone()->find();
        }

        if (!isset($this->zoneRecord['id'])) {
            throw new \Exception('Zone not set.');
        }
    }
}
