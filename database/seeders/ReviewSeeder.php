<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'name' => 'Sarah Johnson',
                'rating' => 5,
                'text' => 'Absolutely love this product! The quality is outstanding and it exceeded my expectations. Highly recommend to anyone looking for premium skincare.',
                'product_image' => '/storage/reviews/ceraVe-lotion.jpg',
                'product_name' => 'CeraVe Moisturizing Lotion',
            ],
            [
                'name' => 'Michael Chen',
                'rating' => 5,
                'text' => 'Great customer service and fast shipping. The product works exactly as described. Will definitely purchase again.',
                'product_image' => '/storage/reviews/apple-watch.jpg',
                'product_name' => 'Apple Watch Series 10',
            ],
            [
                'name' => 'Emily Rodriguez',
                'rating' => 4,
                'text' => 'Very satisfied with my purchase. Good value for money and the quality is excellent. Minor issues with packaging but product itself is perfect.',
                'product_image' => '/storage/reviews/clinton-perfume.jpg',
                'product_name' => 'Clinton Pierce Perfume',
            ],
            [
                'name' => 'David Thompson',
                'rating' => 5,
                'text' => 'This is my second time buying from this store. Consistent quality and excellent service. The product arrived well-packaged and on time.',
                'product_image' => '/storage/reviews/face-cream.jpg',
                'product_name' => 'Hydrating Face Cream',
            ],
            [
                'name' => 'Lisa Park',
                'rating' => 4,
                'text' => 'Beautiful product with great scent. A bit pricey but worth it for the quality. Fast delivery and good packaging.',
                'product_image' => '/storage/reviews/body-lotion.jpg',
                'product_name' => 'Nourishing Body Lotion',
            ],
            [
                'name' => 'James Wilson',
                'rating' => 5,
                'text' => 'Outstanding product! Been using it for weeks now and the results are amazing. Will definitely recommend to friends and family.',
                'product_image' => '/storage/reviews/skincare-set.jpg',
                'product_name' => 'Complete Skincare Set',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
