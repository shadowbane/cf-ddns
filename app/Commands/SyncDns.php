<?php

namespace App\Commands;

use App\CloudflareService\Cloudflare;
use App\IfconfigService\Ifconfig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class SyncDns extends Command
{
    public Cloudflare $cfApp;
    public Collection|array $cfRecord;
    public string $domainName = '';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'syncdns';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Synchronize current IP Address with Cloudflare DNS';

    /**
     * Execute the console command.
     *
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->info('Synchronizing IP Address with Cloudflare DNS');
            $this->setup();
            $this->runService();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            Log::critical($e->getMessage());
        }
    }

    /**
     * @throws BindingResolutionException
     * @throws \Exception
     *
     * @return void
     */
    public function setup(): void
    {
        $this->domainName = config('cloudflare.record').'.'.config('cloudflare.domain');
        $this->cfApp = app()->make(\App\CloudflareService\Cloudflare::class);

        // we are loading the domain name record to $this->cfRecord, so it won't be loaded for second time.
        // this is useful for endless loop mode, as we're preserving bandwidth and time.
        $this->cfRecord = $this->cfApp->zone()->dns()->find(['name' => $this->domainName]);
        Log::debug("Got Cloudflare DNS record for {$this->domainName}: {$this->cfRecord->toJson()}");
    }

    /**
     * Run the service.
     *
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    final public function runService(): void
    {
        $ip = app()->make(Ifconfig::class)->get();
        $ipAddr = $ip['ipAddress'];

        $records = $this->cfApp->zone()->dns()->find(['name' => $this->domainName]);
        $this->info("Got Cloudflare DNS record for {$this->domainName}: {$records->toJson()}".PHP_EOL);

        // update the record if it's not same
        if ($this->cfRecord['content'] !== $ipAddr) {
            Log::info("Updating Cloudflare DNS Record for {$this->domainName} to {$ipAddr}");
            $this->cfRecord = $this->cfApp->zone()->dns()->update(
                dnsName: $this->domainName,
                ipAddress: $ipAddr,
//                proxied: false,
            );
        }

        Log::info('Sync completed');

//        Log::debug('Memory Usage: '.(memory_get_usage() / 1024 / 1024).' MB || Peak Memory Usage: '.(memory_get_peak_usage() / 1024 / 1024).' MB');
    }

    /**
     * Define the command's schedule.
     *
     * @param  Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)
            ->everyMinute();
    }
}
