<?php

namespace App\CloudflareService\Abstracts;

use App\CloudflareService\CfDataTrait;
use App\CloudflareService\Interfaces\ConnectorInterface;
use Illuminate\Support\Facades\Http;

abstract class AbstractCloudflareService implements ConnectorInterface
{
    use CfDataTrait;

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function setMacro(): void
    {
        $token = $this->token;
        $email = $this->email;

        if (blank($token) || blank($email)) {
            throw new \Exception('Token or email is not set');
        }

        Http::macro('cloudflare', function () use ($token, $email) {
            return Http::withHeaders([
                'X-Auth-Email' => $email,
                'Content-Type' => 'application/json',
            ])
                ->withToken($token)
                ->baseUrl('https://api.cloudflare.com/client/v4');
        });
    }

    /**
     * @param string|null $token
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setToken(?string $token): static
    {
        if (blank($token)) {
            $token = config('cloudflare.token');
            throw_if(blank($token), new \Exception("Token Not Supplied."));
        }

        $this->token = $token;

        return $this;
    }

    /**
     * @param  string|null  $email
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setEmail(?string $email): static
    {
        if (blank($email)) {
            $email = config('cloudflare.email');
            throw_if(blank($email), new \Exception("Email Not Supplied."));
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @param string|null $endpoint
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setEndpoint(?string $endpoint): static
    {
        if (blank($endpoint)) {
            throw_if(blank($endpoint), new \Exception("Endpoint Not Supplied."));
        }

        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param string|null $domain
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setDomain(?string $domain): static
    {
        if (blank($domain)) {
            $domain = config('cloudflare.domain');
            throw_if(blank($domain), new \Exception("Domain Not Supplied."));
        }

        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
