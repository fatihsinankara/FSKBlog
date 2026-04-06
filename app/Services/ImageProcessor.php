<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageProcessor
{
    protected ImageManager $manager;

    protected int $quality = 82;

    protected int $maxWidth = 1920;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Process an uploaded image: resize, convert to WebP, store it.
     *
     * @return string Relative path on the public disk
     */
    public function process(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        $image = $this->manager->read($file->getRealPath());

        // Resize if wider than max
        if ($image->width() > $this->maxWidth) {
            $image->scaleDown(width: $this->maxWidth);
        }

        $encoded = $image->toWebp($this->quality);

        $name = ($filename ?: pathinfo($file->hashName(), PATHINFO_FILENAME)).'.webp';
        $path = rtrim($directory, '/').'/'.$name;

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Process with a specific slug-based filename and optional suffix.
     */
    public function processWithSlug(UploadedFile $file, string $directory, string $slug, ?string $suffix = null): string
    {
        $filename = $suffix ? "{$slug}-{$suffix}" : $slug;

        // Avoid overwriting: add short hash if file exists
        if (Storage::disk('public')->exists(rtrim($directory, '/').'/'.$filename.'.webp')) {
            $filename .= '-'.substr(md5(microtime()), 0, 6);
        }

        return $this->process($file, $directory, $filename);
    }

    public function quality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function maxWidth(int $maxWidth): static
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }
}
