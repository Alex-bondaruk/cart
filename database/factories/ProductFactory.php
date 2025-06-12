<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $disk = Storage::disk('public');
        $imagesDir = 'products';

        if (!$disk->exists($imagesDir)) {
            $disk->makeDirectory($imagesDir);
        }

        $colors = ['ff6b6b', '4ecdc4', 'a8e6cf', 'ffd93d', 'ff8b94'];
        $bgColor = $this->faker->randomElement($colors);

        $width = 640;
        $height = 480;
        $im = imagecreatetruecolor($width, $height);

        $rgb = sscanf($bgColor, "%02x%02x%02x");
        $bgColorAllocated = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        $textColor = imagecolorallocate($im, 255, 255, 255);

        imagefilledrectangle($im, 0, 0, $width, $height, $bgColorAllocated);

        $text = 'Product ' . $this->faker->numberBetween(1, 100);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        imagestring($im, $font, $x, $y, $text, $textColor);

        $filename = uniqid() . '.jpg';
        $fullPath = $imagesDir . '/' . $filename;

        ob_start();
        imagejpeg($im, null, 90);
        $imageData = ob_get_clean();
        imagedestroy($im);

        $disk->put($fullPath, $imageData);

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $fullPath,
        ];
    }
}