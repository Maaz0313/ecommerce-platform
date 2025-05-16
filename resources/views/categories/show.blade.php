@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $category->name }}</h1>
            @if ($category->description)
                <p class="lead">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3 mb-4">
                <x-product-card :product="$product" />
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No products found in this category.
                </div>
            </div>
        @endforelse
    </div>
    <div class="mt-4 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-0">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }}
                    results
                </p>
            </div>
            <ul class="pagination">
                @if ($products->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}">&laquo;</a></li>
                @endif

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($products->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        </div>
    </div>
@endsection
