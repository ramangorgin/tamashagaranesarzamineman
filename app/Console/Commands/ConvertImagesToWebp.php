<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use WebPConvert\WebPConvert;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:webp {--dir=public/images} {--quality=75} {--ext=jpg,jpeg,png} {--force}';

    protected $description = 'Convert images in a directory (recursively) to WebP using rosell-dk/webp-convert';

    public function handle(): int
    {
        $dirOpt = $this->option('dir');
        $baseDir = str_starts_with($dirOpt, 'public/') ? public_path(substr($dirOpt, 7)) : base_path(trim($dirOpt, '/\\'));
        if (!is_dir($baseDir)) {
            $this->error("Directory not found: {$baseDir}");
            return self::FAILURE;
        }

        $quality = (int) $this->option('quality');
        $force = (bool) $this->option('force');
        $exts = collect(explode(',', (string) $this->option('ext')))
            ->map(fn($e) => strtolower(trim($e)))
            ->filter();

        $this->info('Scanning for images...');
        $files = collect(File::allFiles($baseDir))
            ->filter(function ($f) use ($exts) {
                return $exts->contains(strtolower($f->getExtension()));
            })
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No matching images found.');
            return self::SUCCESS;
        }

        $this->info('Converting to WebP...');
        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        $converted = 0; $skipped = 0; $failed = 0;
        foreach ($files as $file) {
            $source = $file->getRealPath();
            $dest = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source);

            if (!$force && file_exists($dest)) {
                $skipped++; $bar->advance();
                continue;
            }

            try {
                WebPConvert::convert($source, $dest, [
                    'quality' => $quality,
                    'max-quality' => $quality,
                    // Let the library pick the best available converter; you can limit if needed:
                    // 'converters' => ['cwebp','imagick','gd']
                ]);
                $converted++;            
            } catch (\Throwable $e) {
                $failed++;
                $this->output->writeln("\nFailed: {$file->getRelativePathname()} - {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Converted: {$converted}, Skipped: {$skipped}, Failed: {$failed}");
        $this->line('Tip: Update your Blade to use <picture> with WebP first.');
        return self::SUCCESS;
    }
}
