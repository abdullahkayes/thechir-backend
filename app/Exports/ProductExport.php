<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Tag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::with(['rel_to_cat', 'rel_to_sub', 'rel_to_color', 'rel_to_size'])->get()->map(function ($product) {
            return [
                'SKU' => $product->sku,
                'Product Name' => $product->product_name,
                'Category' => $product->rel_to_cat->category_name ?? '',
                'Subcategory' => $product->rel_to_sub->subcategory_name ?? '',
                'Tags' => $this->getTagNames($product->tag_id),
                'Short Description' => $product->short_desp,
                'Long Description' => $product->long_desp,
                'Price' => $product->price,
                'Discount Price' => $product->discount_price,
                'Slug' => $product->slug,
                'Color' => $product->rel_to_color->color_name ?? '',
                'Size' => $product->rel_to_size->size_name ?? '',
                'Quantity' => $product->quantity,
                'Created At' => $product->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Product Name',
            'Category',
            'Subcategory',
            'Tags',
            'Short Description',
            'Long Description',
            'Price',
            'Discount Price',
            'Slug',
            'Color',
            'Size',
            'Quantity',
            'Created At',
        ];
    }

    private function getTagNames($tagIds)
    {
        if (!$tagIds) {
            return '';
        }
        $ids = explode(',', $tagIds);
        $tags = Tag::whereIn('id', $ids)->pluck('tag_name')->toArray();
        return implode(', ', $tags);
    }
}
