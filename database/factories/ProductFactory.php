<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Directory to save images: public/storage/images
        $imageDir = public_path('storage/images');

        // Create directory if not exists
        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        // Generate a fake image file (640x480), category 'product', return filename only
        $fakerImage = $this->faker->image($imageDir, 640, 480, 'product', false);

        return [
            'name'           => $this->faker->word(),
            'description'    => $this->faker->sentence(),
            'price'          => $this->faker->randomFloat(2, 10, 500),
            'discount_price' => $this->faker->randomFloat(2, 5, 250),
            'tax'            => $this->faker->randomFloat(2, 1, 20),
            'weight'         => $this->faker->randomFloat(2, 0.1, 10),
            'dimensions'     => $this->faker->numberBetween(10, 200) . 'x' . $this->faker->numberBetween(10, 200),
            'tags'           => $this->faker->word(),
            'warranty'       => $this->faker->word(),
            'specifications' => $this->faker->sentence(),
            'color'          => $this->faker->safeColorName(),
            'size'           => $this->faker->randomElement(['S', 'M', 'L']),
            'status'         => $this->faker->randomElement(['draft', 'published', 'archived']),
            'brand'          => $this->faker->company(),
            'stock'          => $this->faker->numberBetween(1, 100),
            'sku'            => strtoupper($this->faker->bothify('??###')),
            'is_active'      => $this->faker->boolean(),
            // Store relative path to image inside public/storage
            'image'          => 'images/' . $fakerImage,
            'category_id'    => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
