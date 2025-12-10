import { Link } from '@inertiajs/react';

export default function ProductCard({ product }) {
    const formatPrice = (price) => {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(price);
    };

    const IVA_RATE = 0.21;

    const currentPrice = product.discount_price || product.price;
    const priceWithoutIVA = currentPrice / (1 + IVA_RATE);
    const priceWithIVA = currentPrice;

    const discountPercentage = product.discount_price
        ? Math.round(((product.price - product.discount_price) / product.price) * 100)
        : 0;

    return (
        <div className="card group overflow-hidden">
            {/* Image Container */}
            <div className="relative aspect-square overflow-hidden bg-gray-100">
                <img
                    src={product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/400x400?text=Product'}
                    alt={product.name}
                    loading="lazy"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />

                {/* Discount Badge */}
                {discountPercentage > 0 && (
                    <div className="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        -{discountPercentage}%
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="p-6">
                {/* Category */}
                <p className="text-sm text-brand-600 font-medium mb-2">
                    {product.category?.name || 'Sin categoría'}
                </p>

                {/* Title */}
                <h3 className="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                    <Link href={`/products/${product.id}`}>
                        {product.name}
                    </Link>
                </h3>

                {/* Description */}
                <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                    {product.description || 'No hay descripción disponible.'}
                </p>

                {/* Brand */}
                {product.brand && (
                    <p className="text-xs text-gray-500 mb-3">
                        by {product.brand.name}
                    </p>
                )}

                {/* Price */}
                <div className="space-y-2">
                    <div className="flex items-center space-x-2">
                        <span className="text-xl font-bold text-gray-900">
                            {formatPrice(priceWithIVA)}
                        </span>
                        {product.discount_price && (
                            <span className="text-sm text-gray-500 line-through">
                                {formatPrice(product.price)}
                            </span>
                        )}
                    </div>

                    {/* Price without IVA */}
                    <div className="text-sm text-gray-600">
                        <span>Sin IVA: {formatPrice(priceWithoutIVA)}</span>
                        <span className="text-xs text-gray-500 ml-2">(IVA 21% incluido)</span>
                    </div>
                </div>

                {/* Stock Status */}
                <div className="mt-3">
                    {product.stock > 0 ? (
                        <span className="text-xs text-green-600 font-medium">
                            ✓ En Stock ({product.stock} disponibles)
                        </span>
                    ) : (
                        <span className="text-xs text-red-500 font-medium">
                            ✗ Agotado
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}