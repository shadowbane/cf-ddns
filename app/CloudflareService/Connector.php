<?php

namespace App\CloudflareService;

use App\CloudflareService\Abstracts\AbstractCloudflareService;

class Connector extends AbstractCloudflareService
{
    /**
     * @param string $email
     * @param string $token
     * @param string $domain
     *
     * @throws \Exception
     */
    public function __construct(
        string $email = '',
        string $token = '',
        string $domain = '',
    ) {
        $this->initialize(email: $email, token: $token, domain: $domain);
        $this->setMacro();
    }
}
