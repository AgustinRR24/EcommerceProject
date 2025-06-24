<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Detalle de {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h1>{{ $product->name }}</h1>
        <p><strong>Categoría:</strong> {{ $product->category->name ?? 'Sin categoría' }}</p>
        <p><strong>Marca:</strong> {{ $product->brand->name ?? 'Sin marca' }}</p>
        <p><strong>Precio:</strong> ${{ number_format($product->price, 2) }}</p>
        <p>{{ $product->description }}</p>

        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 400px;" />
        @elseif($product->productPhotos->count() > 0)
            <img src="{{ asset('storage/' . $product->productPhotos->first()->url_photo) }}" alt="{{ $product->name }}" style="max-width: 400px;" />
        @else
            <img src="https://via.placeholder.com/400x200?text=Sin+imagen" alt="Sin imagen" />
        @endif

        <br /><br />
        <a href="{{ route('landing') }}" class="btn btn-secondary">Volver a la tienda</a>
    </div>
</body>
</html>
