import { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

export default function Layout({ children }) {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [cartCount, setCartCount] = useState(0);

    useEffect(() => {
        // Función para obtener el contador del carrito
        const fetchCartCount = async () => {
            try {
                const response = await fetch('/cart/count');
                const data = await response.json();
                setCartCount(data.count || 0);
            } catch (error) {
                console.error('Error fetching cart count:', error);
                setCartCount(0);
            }
        };

        // Función para actualizar precios del carrito
        const updateCartPrices = async () => {
            try {
                await fetch('/cart/update-prices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
            } catch (error) {
                console.error('Error updating cart prices:', error);
            }
        };

        // Actualizar precios primero, luego obtener contador
        updateCartPrices().then(() => fetchCartCount());

        // Actualizar el contador cada vez que la página cambia (por si se agrega algo al carrito)
        const interval = setInterval(fetchCartCount, 2000);

        return () => clearInterval(interval);
    }, []);

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <header style={{ backgroundColor: '#1E1E1E' }} className="shadow-sm border-b border-gray-700">
                <div className="container-main">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <div className="flex items-center">
                            <Link href="/" className="text-2xl font-bold text-white">
                                Store
                            </Link>
                        </div>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex items-center space-x-8">
                            <Link
                                href="/"
                                className="text-gray-300 hover:text-white font-medium transition-colors"
                            >
                                Home
                            </Link>
                            <Link
                                href="/products"
                                className="text-gray-300 hover:text-white font-medium transition-colors"
                            >
                                Products
                            </Link>
                            <Link
                                href="/hotsale"
                                className="text-gray-300 hover:text-white font-medium transition-colors"
                            >
                                Hot Sale
                            </Link>
                            <Link
                                href="/about"
                                className="text-gray-300 hover:text-white font-medium transition-colors"
                            >
                                About
                            </Link>
                        </nav>

                        {/* Profile, Cart & Mobile Menu */}
                        <div className="flex items-center space-x-4">
                            {/* Profile Icon */}
                            <Link href="/customer" className="p-2 text-gray-300 hover:text-white transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>

                            {/* Cart Icon */}
                            <Link href="/cart" className="relative p-2 text-gray-300 hover:text-white transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h12" />
                                </svg>
                                <span className="absolute -top-1 -right-1 bg-brand-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {cartCount}
                                </span>
                            </Link>

                            {/* Mobile Menu Button */}
                            <button
                                className="md:hidden p-2 text-gray-300"
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {/* Mobile Navigation */}
                    {isMenuOpen && (
                        <div className="md:hidden py-4 border-t border-gray-800">
                            <div className="flex flex-col space-y-3">
                                <Link href="/" className="text-gray-300 hover:text-white font-medium">
                                    Home
                                </Link>
                                <Link href="/products" className="text-gray-300 hover:text-white font-medium">
                                    Products
                                </Link>
                                <Link href="/hotsale" className="text-gray-300 hover:text-white font-medium">
                                    Hot Sale
                                </Link>
                                <Link href="/about" className="text-gray-300 hover:text-white font-medium">
                                    About
                                </Link>
                                <Link href="/customer" className="text-gray-300 hover:text-white font-medium">
                                    Profile
                                </Link>
                            </div>
                        </div>
                    )}
                </div>
            </header>

            {/* Main Content */}
            <main>
                {children}
            </main>

            {/* Footer */}
            <footer style={{ backgroundColor: '#1E1E1E' }} className="border-t border-gray-700 mt-20">
                <div className="container-main py-12">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                        <div className="col-span-1 md:col-span-2">
                            <h3 className="text-lg font-semibold text-white mb-4">Tech Store</h3>
                            <p className="text-gray-400 mb-4">
                                Tu destino de e-commerce moderno para productos de calidad con un servicio excepcional.
                            </p>
                        </div>
                        <div>
                            <h4 className="font-semibold text-white mb-4">Enlaces Rápidos</h4>
                            <ul className="space-y-2">
                                <li><Link href="/" className="text-gray-400 hover:text-white transition-colors">Home</Link></li>
                                <li><Link href="/products" className="text-gray-400 hover:text-white transition-colors">Products</Link></li>
                                <li><Link href="/hotsale" className="text-gray-400 hover:text-white transition-colors">Hot Sale</Link></li>
                                <li><Link href="/about" className="text-gray-400 hover:text-white transition-colors">About</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-white mb-4">Información Legal</h4>
                            <ul className="space-y-2">
                                <li><Link href="/terms" className="text-gray-400 hover:text-white transition-colors">Términos y Condiciones</Link></li>
                                <li><Link href="/privacy" className="text-gray-400 hover:text-white transition-colors">Políticas de Privacidad</Link></li>
                                <li><Link href="/legal" className="text-gray-400 hover:text-white transition-colors">Aviso Legal</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-white mb-4">Contacto</h4>
                            <ul className="space-y-2 text-gray-400">
                                <li>support@techstore.com</li>
                                <li>+1 (555) 123-4567</li>
                            </ul>
                        </div>
                    </div>
                    <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                        <p>&copy; 2024 Tech Store. Todos los derechos reservados.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}