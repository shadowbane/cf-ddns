<?php

namespace App\CloudflareService;

use Illuminate\Support\Collection;

trait CfOutputTrait
{
    /**
     * @param array $errors
     *
     * @throws \Exception
     */
    public function throwsError(array $errors)
    {
        throw new \Exception("Error {$errors[0]['code']}: {$errors[0]['message']}");
    }

    /**
     * @param Collection $response
     *
     * @throws \Exception
     *
     * @return Collection
     */
    public function sendOutput(Collection $response): Collection
    {
        if ($response->has('errors') && count($response->get('errors')) > 0) {
            $this->throwsError($response->get('errors'));
        }

        return collect($response['result']);
    }
}
