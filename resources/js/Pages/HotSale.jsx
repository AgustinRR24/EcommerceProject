import { useState } from 'react';
import { router } from '@inertiajs/react';
import Layout from '../Components/Layout';
import ProductCard from '../Components/ProductCard';

export default function HotSale({ products, filters = {} }) {
    const [localFilters, setLocalFilters] = useState({
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
        search: filters.search || '',
        sort: filters.sort || 'discount'
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
        router.get('/hotsale', cleanFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setLocalFilters({
            min_price: '',
            max_price: '',
            search: '',
            sort: 'discount'
        });
        router.get('/hotsale');
    };

    return (
        <Layout>
            {/* Hero Section */}
            <section
                style={{
                    backgroundImage: 'linear-gradient(135deg, rgba(220, 38, 38, 0.9) 0%, rgba(239, 68, 68, 0.9) 100%), url("https://images.unsplash.com/photo-1607083206869-4c7672e72a8a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80")',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    color: 'white',
                    padding: '60px 0',
                    position: 'relative'
                }}
            >
                <div className="container-main text-center">
                    <h1 className="text-4xl lg:text-6xl font-bold mb-4">
                        🔥 HOT SALE 🔥
                    </h1>
                    <p className="text-xl lg:text-2xl mb-6 text-red-100">
                        ¡Descuentos increíbles en productos seleccionados!
                    </p>
                    <p className="text-lg text-red-200">
                        Aprovecha estas ofertas limitadas antes de que se agoten
                    </p>
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
                                        className="text-sm text-red-600 hover:text-red-700 font-medium"
                                    >
                                        Limpiar
                                    </button>
                                </div>

                                {/* Search */}
                                <div className="mb-6">
                                    <h3 className="font-medium text-gray-900 mb-3">Buscar</h3>
                                    <input
                                        type="text"
                                        placeholder="Buscar en ofertas..."
                                        value={localFilters.search}
                                        onChange={(e) => handleFilterChange('search', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
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
                                            min="0"
                                            max="999999"
                                            step="0.01"
                                            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        />
                                        <input
                                            type="number"
                                            placeholder="Máx"
                                            value={localFilters.max_price}
                                            onChange={(e) => handleFilterChange('max_price', e.target.value)}
                                            min="0"
                                            max="999999"
                                            step="0.01"
                                            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                {/* Hot Sale Info */}
                                <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <h3 className="font-semibold text-red-800 mb-2">🔥 Ofertas Especiales</h3>
                                    <p className="text-sm text-red-700">
                                        Todos los productos en esta sección tienen descuentos especiales. ¡No te los pierdas!
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Products Grid */}
                        <div className="flex-1">
                            {/* Results Header */}
                            <div className="flex items-center justify-between mb-8">
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-900">Productos en Oferta</h2>
                                    <p className="text-gray-600 mt-1">
                                        {products.total} {products.total === 1 ? 'producto' : 'productos'} con descuento
                                    </p>
                                </div>

                                {/* Sort Dropdown */}
                                <div className="relative">
                                    <select
                                        value={localFilters.sort}
                                        onChange={(e) => handleFilterChange('sort', e.target.value)}
                                        className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    >
                                        <option value="discount">Ordenar por: Descuento</option>
                                        <option value="price_asc">Precio: Menor a Mayor</option>
                                        <option value="price_desc">Precio: Mayor a Menor</option>
                                        <option value="newest">Más Nuevos</option>
                                    </select>
                                </div>
                            </div>

                            {/* Products Grid */}
                            {products.data.length > 0 ? (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                        {products.data.map((product) => (
                                            <ProductCard key={product.id} product={product} showDiscount={true} />
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
                                                                ? 'bg-red-600 text-white'
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
                                    <div className="text-6xl mb-4">🔥</div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">No hay ofertas activas</h3>
                                    <p className="text-gray-600 mb-6">Por el momento no tenemos productos con descuento</p>
                                    <a href="/" className="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                        Ver Todos los Productos
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}