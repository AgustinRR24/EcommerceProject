import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';
import Toast from '../Components/Toast';

export default function ProductShow({ product }) {
    const [selectedImage, setSelectedImage] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const [isAddingToCart, setIsAddingToCart] = useState(false);
    const [toast, setToast] = useState(null);

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

    const addToCart = () => {
        setIsAddingToCart(true);

        router.post('/cart/add',
            {
                product_id: product.id,
                quantity: quantity
            },
            {
                onSuccess: () => {
                    setToast({
                        message: '¡Producto agregado al carrito! 🛒',
                        type: 'success'
                    });
                },
                onError: (error) => {
                    console.error('Error:', error);
                    setToast({
                        message: 'Error al agregar al carrito',
                        type: 'error'
                    });
                },
                onFinish: () => {
                    setIsAddingToCart(false);
                }
            }
        );
    };

    // Combine main image with additional photos
    const allImages = [
        product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/600x600?text=Product',
        ...(product.product_photos?.map(photo => `/storage/${photo.url_photo}`) || [])
    ];

    return (
        <Layout>
            <div className="container-main py-8">
                {/* Breadcrumb */}
                <nav className="flex items-center space-x-2 text-sm text-gray-600 mb-8">
                    <Link href="/" className="hover:text-brand-600">Home</Link>
                    <span>/</span>
                    <Link href="/" className="hover:text-brand-600">Products</Link>
                    <span>/</span>
                    <span className="text-gray-900">{product.name}</span>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* Product Images */}
                    <div className="space-y-4">
                        {/* Main Image */}
                        <div className="relative aspect-square bg-gray-100 rounded-xl overflow-hidden">
                            <img
                                src={allImages[selectedImage]}
                                alt={product.name}
                                className="w-full h-full object-cover"
                            />
                            {discountPercentage > 0 && (
                                <div className="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                                    -{discountPercentage}%
                                </div>
                            )}
                        </div>

                        {/* Thumbnail Images */}
                        {allImages.length > 1 && (
                            <div className="grid grid-cols-4 gap-4">
                                {allImages.map((image, index) => (
                                    <button
                                        key={index}
                                        onClick={() => setSelectedImage(index)}
                                        className={`
                                            aspect-square rounded-lg overflow-hidden border-2 transition-colors
                                            ${selectedImage === index ? 'border-brand-500' : 'border-gray-200 hover:border-gray-300'}
                                        `}
                                    >
                                        <img
                                            src={image}
                                            alt={`${product.name} ${index + 1}`}
                                            className="w-full h-full object-cover"
                                        />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Product Info */}
                    <div className="space-y-6">
                        {/* Category & Brand */}
                        <div className="flex items-center space-x-4 text-sm">
                            {product.category && (
                                <span className="bg-brand-100 text-brand-700 px-3 py-1 rounded-full font-medium">
                                    {product.category.name}
                                </span>
                            )}
                            {product.brand && (
                                <span className="text-gray-600">
                                    by <span className="font-medium">{product.brand.name}</span>
                                </span>
                            )}
                        </div>

                        {/* Title */}
                        <h1 className="text-3xl lg:text-4xl font-bold text-gray-900">
                            {product.name}
                        </h1>

                        {/* Price */}
                        <div className="space-y-2">
                            <div className="flex items-center space-x-4">
                                <span className="text-3xl font-bold text-gray-900">
                                    {formatPrice(priceWithIVA)}
                                </span>
                                {product.discount_price && (
                                    <span className="text-xl text-gray-500 line-through">
                                        {formatPrice(product.price)}
                                    </span>
                                )}
                            </div>

                            {/* Price without IVA */}
                            <div className="text-lg text-gray-600">
                                <span>Sin IVA: {formatPrice(priceWithoutIVA)}</span>
                                <span className="text-sm text-gray-500 ml-2">(IVA 21% incluido)</span>
                            </div>
                        </div>

                        {/* Stock Status */}
                        <div className="flex items-center space-x-2">
                            {product.stock > 0 ? (
                                <>
                                    <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span className="text-green-600 font-medium">
                                        In Stock ({product.stock} available)
                                    </span>
                                </>
                            ) : (
                                <>
                                    <div className="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <span className="text-red-500 font-medium">Out of Stock</span>
                                </>
                            )}
                        </div>

                        {/* Description */}
                        <div className="prose prose-gray max-w-none">
                            <p className="text-gray-600 text-lg leading-relaxed">
                                {product.description || 'No description available for this product.'}
                            </p>
                        </div>

                        {/* SKU */}
                        {product.sku && (
                            <div className="text-sm text-gray-500">
                                SKU: <span className="font-mono">{product.sku}</span>
                            </div>
                        )}

                        {/* Add to Cart Section */}
                        {product.stock > 0 && (
                            <div className="space-y-4">
                                {/* Quantity Selector */}
                                <div className="flex items-center space-x-4">
                                    <label className="font-medium text-gray-900">Quantity:</label>
                                    <div className="flex items-center border border-gray-300 rounded-lg">
                                        <button
                                            onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                            className="px-3 py-2 hover:bg-gray-50 transition-colors"
                                        >
                                            -
                                        </button>
                                        <span className="px-4 py-2 border-x border-gray-300 min-w-[60px] text-center">
                                            {quantity}
                                        </span>
                                        <button
                                            onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
                                            className="px-3 py-2 hover:bg-gray-50 transition-colors"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>

                                {/* Action Buttons */}
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <button
                                        onClick={addToCart}
                                        disabled={isAddingToCart}
                                        className="btn-primary flex-1 text-lg py-3 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {isAddingToCart ? 'Adding...' : 'Add to Cart'}
                                    </button>
                                    <button className="btn-secondary flex-shrink-0 px-6 py-3">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* Features */}
                        <div className="border-t border-gray-200 pt-6">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div className="flex items-center space-x-2">
                                    <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Free shipping on orders over $50</span>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>30-day return policy</span>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Secure payment</span>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Customer support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Toast Notification */}
            {toast && (
                <Toast
                    message={toast.message}
                    type={toast.type}
                    onClose={() => setToast(null)}
                />
            )}
        </Layout>
    );
}