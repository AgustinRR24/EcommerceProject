import { Link } from '@inertiajs/react';

export default function ProductCard({ product }) {
    const formatPrice = (price) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    };

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
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />

                {/* Discount Badge */}
                {discountPercentage > 0 && (
                    <div className="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        -{discountPercentage}%
                    </div>
                )}

                {/* Quick Actions */}
                <div className="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <button className="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white transition-colors">
                        <svg className="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Content */}
            <div className="p-6">
                {/* Category */}
                <p className="text-sm text-brand-600 font-medium mb-2">
                    {product.category?.name || 'Uncategorized'}
                </p>

                {/* Title */}
                <h3 className="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                    <Link href={`/products/${product.id}`}>
                        {product.name}
                    </Link>
                </h3>

                {/* Description */}
                <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                    {product.description || 'No description available.'}
                </p>

                {/* Brand */}
                {product.brand && (
                    <p className="text-xs text-gray-500 mb-3">
                        by {product.brand.name}
                    </p>
                )}

                {/* Price */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-2">
                        <span className="text-xl font-bold text-gray-900">
                            {formatPrice(product.discount_price || product.price)}
                        </span>
                        {product.discount_price && (
                            <span className="text-sm text-gray-500 line-through">
                                {formatPrice(product.price)}
                            </span>
                        )}
                    </div>

                    {/* Add to Cart Button */}
                    <button className="btn-primary text-sm px-3 py-2">
                        Add to Cart
                    </button>
                </div>

                {/* Stock Status */}
                <div className="mt-3">
                    {product.stock > 0 ? (
                        <span className="text-xs text-green-600 font-medium">
                            ✓ In Stock ({product.stock} available)
                        </span>
                    ) : (
                        <span className="text-xs text-red-500 font-medium">
                            ✗ Out of Stock
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}