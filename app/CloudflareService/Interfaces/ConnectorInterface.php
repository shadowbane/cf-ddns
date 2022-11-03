<?php

namespace App\CloudflareService\Interfaces;

interface ConnectorInterface
{
    /**
     * @return void
     */
    public function setMacro(): void;

    /**
     * @param string|null $token
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setToken(?string $token): static;

    /**
     * @param string|null $email
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setEmail(?string $email): static;

    /**
     * @param string|null $endpoint
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setEndpoint(?string $endpoint): static;

    /**
     * @param string|null $domain
     *
     * @throws \Throwable
     *
     * @return static
     */
    public function setDomain(?string $domain): static;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getDomain(): string;
}
