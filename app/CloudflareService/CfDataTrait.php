<?php

namespace App\CloudflareService;

use Exception;
use Throwable;

trait CfDataTrait
{
    protected string $email = '';
    protected string $token = '';
    protected string $endpoint = '';
    protected string $domain = '';

    /**
     * @param  string|null  $email
     * @param  string|null  $token
     * @param  string|null  $domain
     *
     * @throws Exception
     * @throws Throwable
     *
     * @return void
     */
    protected function initialize(?string $email, ?string $token, ?string $domain): void
    {
        $this->setEmail($email);
        $this->setToken($token);
        $this->setDomain($domain);
    }
}
