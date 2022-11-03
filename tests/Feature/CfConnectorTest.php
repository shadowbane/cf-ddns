<?php

use App\CloudflareService\Connector;

it('can be initialized directly', function () {
    $email = fakeEmail();
    $token = fakeToken();
    $domain = fakeDomain();

    $cfConnector = new Connector($email, $token, $domain);

    $this->assertInstanceOf(Connector::class, $cfConnector);
});

it('can be initialized via config', function () {
    config(['cloudflare.email' => fakeEmail()]);
    config(['cloudflare.token' => fakeToken()]);
    config(['cloudflare.domain' => fakeDomain()]);

    $cfConnector = app()->make(Connector::class);

    $this->assertInstanceOf(Connector::class, $cfConnector);
});

test('singleton is working', function () {
    config(['cloudflare.email' => fakeEmail()]);
    config(['cloudflare.token' => fakeToken()]);
    config(['cloudflare.domain' => fakeDomain()]);

    $firstInstance = app()->make(Connector::class);
    $secondInstance = app()->make(Connector::class);

    $this->assertSame($firstInstance, $secondInstance);
});

it('can get and set email correctly', function () {
    $email = fakeEmail();
    $token = fakeToken();
    $domain = fakeDomain();

    $cfConnector = new Connector($email, $token, $domain);

    $this->assertSame($email, $cfConnector->getEmail());
});

it('can get and set token correctly', function () {
    $email = fakeEmail();
    $token = fakeToken();
    $domain = fakeDomain();

    $cfConnector = new Connector($email, $token, $domain);

    $this->assertSame($token, $cfConnector->getToken());
});

it('can get and set domain correctly', function () {
    $email = fakeEmail();
    $token = fakeToken();
    $domain = fakeDomain();

    $cfConnector = new Connector($email, $token, $domain);

    $this->assertSame($domain, $cfConnector->getDomain());
});

it('throws error when empty email provided', function () {
    config([
        'cloudflare.email' => null,
    ]);

    $email = '';
    $token = fakeToken();
    $domain = fakeDomain();

    new Connector($email, $token, $domain);
})->throws(\Exception::class)->expectErrorMessage('Email Not Supplied');

it('throws error when empty token provided', function () {
    config([
        'cloudflare.token' => null,
    ]);

    $email = fakeEmail();
    $token = '';
    $domain = fakeDomain();

    new Connector($email, $token, $domain);

    $this->expectErrorMessage('Token Not Supplied');
})->throws(\Exception::class)->expectErrorMessage('Token Not Supplied');

it('throws error when empty domain provided', function () {
    config([
        'cloudflare.domain' => null,
    ]);

    $email = fakeEmail();
    $token = fakeToken();
    $domain = '';

    new Connector($email, $token, $domain);

    $this->expectErrorMessage('Domain Not Supplied');
})->throws(\Exception::class)->expectErrorMessage('Domain Not Supplied');

it('throws error when empty endpoint provided', function () {
    config([
        'cloudflare.endpoint' => null,
    ]);

    $email = fakeEmail();
    $token = fakeToken();
    $domain = fakeDomain();

    $connector = new Connector($email, $token, $domain);
    $connector->setEndpoint(null);

    $this->expectErrorMessage('Endpoint Not Supplied');
})->throws(\Exception::class)->expectErrorMessage('Endpoint Not Supplied');
