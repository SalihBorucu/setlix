<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateGeoLite2Database extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geolite2:update {--license-key= : The MaxMind license key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and update the GeoLite2 Country database';

    private const DOWNLOAD_URL = 'https://download.maxmind.com/app/geoip_download';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $licenseKey = $this->option('license-key') ?? config('services.maxmind.license_key');

        if (!$licenseKey) {
            $this->error('MaxMind license key is required. Please provide it using --license-key option or set it in your environment.');
            return Command::FAILURE;
        }

        $this->info('Downloading GeoLite2 Country database...');

        try {
            // Build the download URL - use .mmdb format directly instead of tar.gz
            $url = self::DOWNLOAD_URL . '?' . http_build_query([
                'edition_id' => 'GeoLite2-Country',
                'license_key' => $licenseKey,
                'suffix' => 'tar.gz'
            ]);

            // Download the file
            $response = Http::withOptions([
                'verify' => true,
                'sink' => storage_path('app/geolite2/GeoLite2-Country.tar.gz')
            ])->get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to download database: ' . $response->body());
            }

            // Extract using tar command (available in most Docker containers)
            $tarFile = storage_path('app/geolite2/GeoLite2-Country.tar.gz');
            $extractPath = storage_path('app/geolite2/extract');
            
            // Create extract directory if it doesn't exist
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            // Extract the tar.gz file
            $command = "cd " . escapeshellarg($extractPath) . " && tar xzf " . escapeshellarg($tarFile);
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Failed to extract database file');
            }

            // Find and move the .mmdb file
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->files()->name('*.mmdb')->in($extractPath);
            $found = false;

            foreach ($finder as $file) {
                $targetPath = storage_path('app/geolite2/GeoLite2-Country.mmdb');
                copy($file->getRealPath(), $targetPath);
                $found = true;
                break;
            }

            if (!$found) {
                throw new \Exception('Could not find .mmdb file in the downloaded archive');
            }

            // Cleanup
            @unlink($tarFile);
            $this->cleanup($extractPath);

            $this->info('Database updated successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            Log::error('Failed to update GeoLite2 database: ' . $e->getMessage());
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function cleanup(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $command = "rm -rf " . escapeshellarg($dir);
        exec($command);
    }
}
