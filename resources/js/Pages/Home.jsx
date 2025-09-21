import Layout from '../Components/Layout';
import ProductCard from '../Components/ProductCard';

export default function Home({ topProducts = [] }) {
    const features = [
        {
            icon: '🏆',
            title: 'Calidad Garantizada',
            description: 'Productos de la más alta calidad respaldados por garantía completa.'
        },
        {
            icon: '🚚',
            title: 'Envío Rápido',
            description: 'Entrega express en 24-48 horas a todo el país.'
        },
        {
            icon: '💳',
            title: 'Compra a Crédito',
            description: 'Financiamiento flexible hasta 12 cuotas sin interés.'
        },
        {
            icon: '🎧',
            title: 'Atención Personalizada',
            description: 'Soporte técnico y asesoramiento especializado 24/7.'
        }
    ];

    return (
        <Layout>
            {/* Hero Section */}
            <section className="py-20">
                <div className="container-main">
                    <div
                        className="rounded-3xl p-8 lg:p-16 relative overflow-hidden"
                        style={{
                            backgroundImage: 'linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.8) 100%), url("https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80")',
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            backgroundRepeat: 'no-repeat'
                        }}
                    >
                        <div className="relative z-10">
                            {/* Text Content */}
                            <div className="max-w-2xl">
                                <h1 className="text-4xl lg:text-6xl font-bold text-white mb-6">
                                    Bienvenido a
                                    <span className="text-blue-200 block">Tech Store</span>
                                </h1>
                                <p className="text-xl text-gray-100 mb-8 leading-relaxed">
                                    Descubre la mejor tecnología con calidad premium, precios competitivos y el mejor servicio al cliente.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <a
                                        href="/products"
                                        className="bg-white text-brand-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-center"
                                    >
                                        Ver Productos
                                    </a>
                                    <a
                                        href="/hotsale"
                                        className="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-brand-600 transition-colors text-center"
                                    >
                                        Hot Sale
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-20 bg-gray-100">
                <div className="container-main">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            ¿Por qué elegirnos?
                        </h2>
                        <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                            Ofrecemos la mejor experiencia de compra con beneficios únicos para nuestros clientes.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        {features.map((feature, index) => (
                            <div
                                key={index}
                                className="bg-white rounded-2xl p-8 text-center hover:shadow-lg transition-all duration-300"
                            >
                                <div className="text-4xl mb-4">{feature.icon}</div>
                                <h3 className="text-xl font-semibold text-gray-900 mb-3">
                                    {feature.title}
                                </h3>
                                <p className="text-gray-600 leading-relaxed">
                                    {feature.description}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Top Products Section */}
            <section className="py-20 bg-white">
                <div className="container-main">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            Top 6 Productos Premium
                        </h2>
                        <p className="text-xl text-gray-700 max-w-2xl mx-auto">
                            Descubre nuestra selección de productos de gama alta con la mejor tecnología disponible
                        </p>
                    </div>

                    {topProducts.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {topProducts.map((product) => (
                                <ProductCard key={product.id} product={product} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-16">
                            <div className="text-6xl mb-4">📦</div>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">No hay productos disponibles</h3>
                            <p className="text-gray-600 mb-6">Próximamente agregaremos nuevos productos premium</p>
                            <a
                                href="/products"
                                className="bg-brand-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-brand-700 transition-colors"
                            >
                                Ver Todos los Productos
                            </a>
                        </div>
                    )}
                </div>
            </section>

            {/* CTA Section */}
            <section className="py-20 bg-gray-100">
                <div className="container-main text-center">
                    <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                        ¿Listo para encontrar tu próximo gadget?
                    </h2>
                    <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        Explora nuestra amplia selección de productos tecnológicos y encuentra exactamente lo que necesitas.
                    </p>
                    <a
                        href="/products"
                        className="bg-brand-600 text-black px-8 py-4 rounded-lg font-semibold hover:bg-brand-700 transition-colors inline-block"
                    >
                        Explorar Catálogo
                    </a>
                </div>
            </section>
        </Layout>
    );
}