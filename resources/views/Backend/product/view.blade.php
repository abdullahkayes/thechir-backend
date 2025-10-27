@extends('layouts.admin')
@section('content')
@php
    // Normalize to a single product variable $p without changing controller
    $p = null;

    if (isset($product)) {
        $p = $product;
    } elseif (isset($products)) {
        // If it's a collection with one item, use that.
        if ($products instanceof \Illuminate\Support\Collection) {
            if ($products->count() === 1) {
                $p = $products->first();
            } else {
                // try to match route param id or request id, otherwise fallback to first
                $routeId = request()->route('id') ?? request('id') ?? null;
                $p = $routeId ? $products->firstWhere('id', $routeId) : null;
                $p = $p ?? $products->first();
            }
        } else {
            // if $products is a single model instance
            $p = $products;
        }
    }
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h2>Product Details</h2>
            </div>
            <div class="card-body">
                @if($p)
                <table class="table table-bordered">
                    <tr>
                        <td>Product SKU :</td>
                        <td>{{ $p->sku }}</td>
                    </tr>
                    <tr>
                        <td>Product Name :</td>
                        <td>{{ $p->product_name }}</td>
                    </tr>
                    <tr>
                        <td>Product Category :</td>
                        <td>{{ optional($p->rel_to_cat)->category_name }}</td>
                    </tr>
                    {{-- <tr>
                        <td>Product Subcategory</td>
                        <td>{{ optional($p->rel_to_sub)->subcategory_name }}</td>
                    </tr> --}}
                    <tr>
                        <td>Tags :</td>
                        <td>
                            @php
                                $tagIds = $p->tag_id ? array_filter(explode(',', $p->tag_id)) : [];
                            @endphp

                            @foreach($tagIds as $tagId)
                                @php $tagModel = \App\Models\Tag::find($tagId); @endphp
                                @if($tagModel)
                                    <span class="mx-1 badge badge-primary">{{ $tagModel->tag_name }}</span>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>Product Slug :</td>
                        <td>{{ $p->slug }}</td>
                    </tr>
                    <tr>
                        <td>Short Description :</td>
                        <td>{{ $p->short_desp }}</td>
                    </tr>
                    <tr>
                        <td>Long Description :</td>
                        <td>{!! $p->long_desp !!}</td>
                    </tr>
                    <tr>
                        <td>Product Preview :</td>
                        <td>
                            @if($p->preview)
                                <img src="{{ asset($p->preview) }}" alt="{{ $p->product_name }}" style="max-width:200px;">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Product Gallery :</td>
                        <td>
                            @foreach ($p->rel_to_gal ?? [] as $gallary)
                                @if($gallary->gallary)
                                    <img src="{{ asset($gallary->gallary) }}" alt="" style="max-width:120px; margin-right:6px;">
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
                @else
                    <div class="alert alert-info">Product not found.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
