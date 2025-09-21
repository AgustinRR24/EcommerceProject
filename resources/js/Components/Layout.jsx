import { useState } from 'react';
import { Link } from '@inertiajs/react';

export default function Layout({ children }) {
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <header className="bg-white shadow-sm border-b border-gray-100">
                <div className="container-main">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <div className="flex items-center">
                            <Link href="/" className="text-2xl font-bold text-brand-700">
                                Store
                            </Link>
                        </div>

                        {/* Desktop Navigation */}
                        <nav className="hidden md:flex items-center space-x-8">
                            <Link
                                href="/"
                                className="text-gray-700 hover:text-brand-600 font-medium transition-colors"
                            >
                                Products
                            </Link>
                            <Link
                                href="/categories"
                                className="text-gray-700 hover:text-brand-600 font-medium transition-colors"
                            >
                                Categories
                            </Link>
                            <Link
                                href="/brands"
                                className="text-gray-700 hover:text-brand-600 font-medium transition-colors"
                            >
                                Brands
                            </Link>
                        </nav>

                        {/* Profile, Cart & Mobile Menu */}
                        <div className="flex items-center space-x-4">
                            {/* Profile Icon */}
                            <Link href="/customer" className="p-2 text-gray-700 hover:text-brand-600 transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>

                            {/* Cart Icon */}
                            <Link href="/cart" className="relative p-2 text-gray-700 hover:text-brand-600 transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h12" />
                                </svg>
                                <span className="absolute -top-1 -right-1 bg-brand-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    0
                                </span>
                            </Link>

                            {/* Mobile Menu Button */}
                            <button
                                className="md:hidden p-2 text-gray-700"
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
                        <div className="md:hidden py-4 border-t border-gray-100">
                            <div className="flex flex-col space-y-3">
                                <Link href="/" className="text-gray-700 hover:text-brand-600 font-medium">
                                    Products
                                </Link>
                                <Link href="/categories" className="text-gray-700 hover:text-brand-600 font-medium">
                                    Categories
                                </Link>
                                <Link href="/brands" className="text-gray-700 hover:text-brand-600 font-medium">
                                    Brands
                                </Link>
                                <Link href="/customer" className="text-gray-700 hover:text-brand-600 font-medium">
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
            <footer className="bg-white border-t border-gray-100 mt-20">
                <div className="container-main py-12">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div className="col-span-1 md:col-span-2">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Store</h3>
                            <p className="text-gray-600 mb-4">
                                Your modern e-commerce destination for quality products with exceptional service.
                            </p>
                        </div>
                        <div>
                            <h4 className="font-semibold text-gray-900 mb-4">Quick Links</h4>
                            <ul className="space-y-2">
                                <li><Link href="/" className="text-gray-600 hover:text-brand-600">Products</Link></li>
                                <li><Link href="/categories" className="text-gray-600 hover:text-brand-600">Categories</Link></li>
                                <li><Link href="/brands" className="text-gray-600 hover:text-brand-600">Brands</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-gray-900 mb-4">Contact</h4>
                            <ul className="space-y-2 text-gray-600">
                                <li>support@store.com</li>
                                <li>+1 (555) 123-4567</li>
                            </ul>
                        </div>
                    </div>
                    <div className="border-t border-gray-100 mt-8 pt-8 text-center text-gray-600">
                        <p>&copy; 2024 Store. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}