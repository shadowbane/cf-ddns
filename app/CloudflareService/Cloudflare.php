<?php

namespace App\CloudflareService;

class Cloudflare
{
    public Zone $zone;

    public function __construct()
    {
        $this->zone = app()->make(Zone::class);
    }

    public function zone(): Zone
    {
        return $this->zone;
    }
}
