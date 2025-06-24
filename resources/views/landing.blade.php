<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda - Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: box-shadow 0.2s;
        }
        .product-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .product-img {
            height: 200px;
            object-fit: cover;
        }
        .sidebar {
            min-width: 250px;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Ecommerce</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="mb-4 text-center">Nuestros Productos</h1>
        <div class="row">
            <!-- Sidebar de filtros -->
            <div class="col-md-3 mb-4 sidebar">
                <form method="GET" action="{{ route('landing') }}">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Precio</label>
                        <div class="d-flex">
                            <input type="number" name="min_price" class="form-control me-2" placeholder="Mín" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" class="form-control" placeholder="Máx" value="{{ request('max_price') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categorías</label>
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}"
                                    {{ (collect(request('categories'))->contains($category->id)) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Marcas</label>
                        @foreach($brands as $brand)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                    {{ (collect(request('brands'))->contains($brand->id)) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ $brand->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    <a href="{{ route('landing') }}" class="btn btn-link w-100 mt-2">Limpiar filtros</a>
                </form>
            </div>
            <!-- Productos -->
            <div class="col-md-9">
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card product-card h-100">
                                <img src="{{ $product->image ?? 'https://via.placeholder.com/400x200?text=Producto' }}" class="card-img-top product-img" alt="{{ $product->name }}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-muted mb-2">{{ $product->category->name ?? 'Sin categoría' }}</p>
                                    <p class="card-text">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
                                    <div class="mt-auto">
                                        <span class="fw-bold h5">${{ number_format($product->price, 2) }}</span>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm float-end">Ver más</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($products->isEmpty())
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No hay productos disponibles.
                            </div>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
