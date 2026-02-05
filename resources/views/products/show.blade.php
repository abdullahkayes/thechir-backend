@extends('layouts.app')

@section('content')
@if(isset($product))
{{-- Ensure SEO tags rendered for this product --}}
{!! seo()->for($product) !!}
<div id="product-app" data-product='@json($product)'>
<!-- Vue will mount here and read data-product -->
</div>
@else
<div id="product-app" data-product='{}'>
<!-- Vue will mount here and read data-product -->
</div>
@endif
@endsection
