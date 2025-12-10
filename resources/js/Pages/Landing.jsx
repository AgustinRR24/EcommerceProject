import { useState } from 'react';
import { router } from '@inertiajs/react';
import Layout from '../Components/Layout';
import ProductCard from '../Components/ProductCard';

export default function Landing({ products, categories, brands, filters = {} }) {
    const [localFilters, setLocalFilters] = useState({
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
        categories: filters.categories || [],
        brands: filters.brands || [],
        search: filters.search || ''
    });

    const handleFilterChange = (key, value) => {
        const newFilters = { ...localFilters, [key]: value };
        setLocalFilters(newFilters);

        // Remove empty values
        const cleanFilters = Object.entries(newFilters).reduce((acc, [key, val]) => {
            if (val && val.length > 0) {
                acc[key] = val;
            }
            return acc;
        }, {});

        // Update URL with filters
        router.get('/products', cleanFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const toggleFilter = (type, id) => {
        const current = localFilters[type];
        const newValue = current.includes(id)
            ? current.filter(item => item !== id)
            : [...current, id];

        handleFilterChange(type, newValue);
    };

    const clearFilters = () => {
        setLocalFilters({
            min_price: '',
            max_price: '',
            categories: [],
            brands: [],
            search: ''
        });
        router.get('/products');
    };

    return (
        <Layout>
            {/* Hero Section */}
            <section
                style={{
                    backgroundImage: 'linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%), url("https://images.unsplash.com/photo-1484704849700-f032a568e944?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80")',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    color: 'white',
                    padding: '80px 0',
                    position: 'relative',
                    minHeight: '500px'
                }}
            >
                {/* Overlay for better text readability */}
                <div
                    style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        backgroundColor: 'rgba(0, 0, 0, 0.4)'
                    }}
                ></div>

                <div
                    style={{
                        maxWidth: '80rem',
                        margin: '0 auto',
                        padding: '0 1rem',
                        position: 'relative',
                        zIndex: 10
                    }}
                >
                    <div style={{ maxWidth: '48rem' }}>
                        <h1
                            style={{
                                fontSize: '3.5rem',
                                fontWeight: 'bold',
                                marginBottom: '1.5rem',
                                textShadow: '0 4px 8px rgba(0, 0, 0, 0.7)'
                            }}
                        >
                            Tech Store Premium
                        </h1>
                        <p
                            style={{
                                fontSize: '1.25rem',
                                marginBottom: '2rem',
                                textShadow: '0 2px 4px rgba(0, 0, 0, 0.5)',
                                color: '#f3f4f6'
                            }}
                        >
                            La mejor tecnología al alcance de tus manos. Descubre smartphones, laptops, gadgets y mucho más.
                        </p>

                        {/* Search Bar */}
                        <div style={{ position: 'relative', maxWidth: '28rem' }}>
                            <style>{`
                                .hero-search::placeholder {
                                    color: rgba(255, 255, 255, 0.7);
                                }
                            `}</style>
                            <input
                                type="text"
                                placeholder="Buscar productos..."
                                value={localFilters.search}
                                onChange={(e) => handleFilterChange('search', e.target.value)}
                                className="hero-search"
                                style={{
                                    width: '100%',
                                    padding: '12px 16px 12px 48px',
                                    borderRadius: '8px',
                                    color: '#ffffff',
                                    backgroundColor: 'rgba(255, 255, 255, 0.2)',
                                    border: '2px solid #ffffff',
                                    outline: 'none',
                                    fontSize: '16px'
                                }}
                            />
                            <svg
                                style={{
                                    position: 'absolute',
                                    left: '16px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    width: '20px',
                                    height: '20px',
                                    color: '#ffffff'
                                }}
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            {/* Main Content */}
            <section className="py-12">
                <div className="container-main">
                    <div className="flex flex-col lg:flex-row gap-8">
                        {/* Sidebar Filters */}
                        <div className="lg:w-80 flex-shrink-0">
                            <div className="card p-6 sticky top-8">
                                <div className="flex items-center justify-between mb-6">
                                    <h2 className="text-lg font-semibold text-gray-900">Filtros</h2>
                                    <button
                                        onClick={clearFilters}
                                        className="text-sm text-brand-600 hover:text-brand-700 font-medium"
                                    >
                                        Limpiar todo
                                    </button>
                                </div>

                                {/* Price Range */}
                                <div className="mb-6">
                                    <h3 className="font-medium text-gray-900 mb-3">Rango de Precio</h3>
                                    <div className="grid grid-cols-2 gap-3">
                                        <input
                                            type="number"
                                            placeholder="Mín"
                                            value={localFilters.min_price}
                                            onChange={(e) => handleFilterChange('min_price', e.target.value)}
                                            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                        />
                                        <input
                                            type="number"
                                            placeholder="Máx"
                                            value={localFilters.max_price}
                                            onChange={(e) => handleFilterChange('max_price', e.target.value)}
                                            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                {/* Categories */}
                                <div className="mb-6">
                                    <h3 className="font-medium text-gray-900 mb-3">Categorías</h3>
                                    <div className="space-y-2 max-h-48 overflow-y-auto">
                                        {categories.map((category) => (
                                            <label key={category.id} className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={localFilters.categories.includes(category.id)}
                                                    onChange={() => toggleFilter('categories', category.id)}
                                                    className="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500"
                                                />
                                                <span className="ml-3 text-gray-700">{category.name}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                {/* Brands */}
                                <div>
                                    <h3 className="font-medium text-gray-900 mb-3">Marcas</h3>
                                    <div className="space-y-2 max-h-48 overflow-y-auto">
                                        {brands.map((brand) => (
                                            <label key={brand.id} className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={localFilters.brands.includes(brand.id)}
                                                    onChange={() => toggleFilter('brands', brand.id)}
                                                    className="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500"
                                                />
                                                <span className="ml-3 text-gray-700">{brand.name}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Products Grid */}
                        <div className="flex-1">
                            {/* Results Header */}
                            <div className="flex items-center justify-between mb-8">
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-900">Productos</h2>
                                    <p className="text-gray-600 mt-1">
                                        {products.total} {products.total === 1 ? 'producto' : 'productos'} encontrados
                                    </p>
                                </div>

                                {/* Sort Dropdown */}
                                <div className="relative">
                                    <select className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                        <option>Ordenar por: Destacados</option>
                                        <option>Precio: Menor a Mayor</option>
                                        <option>Precio: Mayor a Menor</option>
                                        <option>Más Recientes</option>
                                        <option>Mejor Valorados</option>
                                    </select>
                                </div>
                            </div>

                            {/* Products Grid */}
                            {products.data.length > 0 ? (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                        {products.data.map((product) => (
                                            <ProductCard key={product.id} product={product} />
                                        ))}
                                    </div>

                                    {/* Pagination */}
                                    {products.last_page > 1 && (
                                        <div className="mt-12 flex justify-center">
                                            <div className="flex items-center space-x-2">
                                                {products.links.map((link, index) => (
                                                    <button
                                                        key={index}
                                                        onClick={() => link.url && router.get(link.url)}
                                                        disabled={!link.url}
                                                        className={`
                                                            px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                                            ${link.active
                                                                ? 'bg-brand-600 text-white'
                                                                : link.url
                                                                    ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                            }
                                                        `}
                                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                                    />
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </>
                            ) : (
                                <div className="text-center py-16">
                                    <svg className="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">No se encontraron productos</h3>
                                    <p className="text-gray-600 mb-6">Intenta ajustar tus filtros o términos de búsqueda</p>
                                    <button onClick={clearFilters} className="btn-primary">
                                        Limpiar todos los filtros
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}