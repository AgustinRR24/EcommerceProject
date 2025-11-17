import Layout from '../Components/Layout';

export default function About() {
    const stats = [
        { number: '10K+', label: 'Clientes Satisfechos' },
        { number: '500+', label: 'Productos Disponibles' },
        { number: '5★', label: 'Calificación Promedio' },
        { number: '24/7', label: 'Soporte Técnico' }
    ];

    const team = [
        {
            name: 'María González',
            role: 'CEO & Fundadora',
            image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80',
            description: 'Visionaria en tecnología con más de 15 años de experiencia en el sector.'
        },
        {
            name: 'Carlos Ruiz',
            role: 'Director Técnico',
            image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
            description: 'Experto en productos tecnológicos y innovación digital.'
        },
        {
            name: 'Ana López',
            role: 'Gerente de Ventas',
            image: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
            description: 'Especialista en atención al cliente y estrategias comerciales.'
        }
    ];

    return (
        <Layout>
            {/* Hero Section */}
            <section className="py-20 bg-gradient-to-br from-brand-50 to-purple-50">
                <div className="container-main text-center">
                    <h1 className="text-4xl lg:text-6xl font-bold text-gray-900 mb-6">
                        Sobre Nosotros
                    </h1>
                    <p className="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Somos una empresa dedicada a ofrecer la mejor tecnología con un servicio excepcional.
                        Nuestra pasión es conectar a las personas con la innovación que transforma vidas.
                    </p>
                </div>
            </section>

            {/* Mission & Vision */}
            <section className="py-20 bg-white">
                <div className="container-main">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                                Nuestra Historia
                            </h2>
                            <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                                Fundada en 2020 con la visión de democratizar el acceso a la tecnología de última generación.
                                Comenzamos como una pequeña tienda local y hemos crecido hasta convertirnos en una de las
                                tiendas online de tecnología más confiables del país.
                            </p>
                            <p className="text-lg text-gray-600 leading-relaxed">
                                Nuestro compromiso es ofrecer productos auténticos, precios justos y un servicio al cliente
                                que supere las expectativas. Cada producto que vendemos pasa por rigurosos controles de
                                calidad para garantizar la mejor experiencia para nuestros clientes.
                            </p>
                        </div>
                        <div>
                            <img
                                src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                alt="Nuestro equipo"
                                className="rounded-2xl shadow-lg w-full h-96 object-cover"
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* Stats Section */}
            <section className="py-20 bg-white">
                <div className="container-main">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            Nuestros Números
                        </h2>
                        <p className="text-xl text-gray-600">
                            Cifras que respaldan nuestro compromiso con la excelencia
                        </p>
                    </div>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
                        {stats.map((stat, index) => (
                            <div key={index} className="text-center">
                                <div className="text-4xl lg:text-5xl font-bold text-gray-900 mb-2">
                                    {stat.number}
                                </div>
                                <div className="text-gray-600 font-medium">
                                    {stat.label}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Team Section */}
            <section className="py-20 bg-gray-50">
                <div className="container-main">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            Nuestro Equipo
                        </h2>
                        <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                            Conoce a las personas apasionadas que hacen posible nuestra misión cada día
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {team.map((member, index) => (
                            <div key={index} className="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-lg transition-shadow">
                                <img
                                    src={member.image}
                                    alt={member.name}
                                    className="w-24 h-24 rounded-full mx-auto mb-6 object-cover"
                                />
                                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                                    {member.name}
                                </h3>
                                <p className="text-brand-600 font-medium mb-4">
                                    {member.role}
                                </p>
                                <p className="text-gray-600 leading-relaxed">
                                    {member.description}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Values Section */}
            <section className="py-20 bg-white">
                <div className="container-main">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            Nuestros Valores
                        </h2>
                        <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                            Los principios que guían cada decisión que tomamos
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div className="text-center">
                            <div className="bg-brand-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl">🎯</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Excelencia</h3>
                            <p className="text-gray-600">
                                Buscamos la perfección en cada producto y servicio que ofrecemos.
                            </p>
                        </div>
                        <div className="text-center">
                            <div className="bg-brand-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl">🤝</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Confianza</h3>
                            <p className="text-gray-600">
                                Construimos relaciones duraderas basadas en la transparencia y honestidad.
                            </p>
                        </div>
                        <div className="text-center">
                            <div className="bg-brand-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl">🚀</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Innovación</h3>
                            <p className="text-gray-600">
                                Siempre a la vanguardia de las últimas tendencias tecnológicas.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* Location Section */}
            <section className="py-20 bg-gray-50">
                <div className="container-main">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                            Nuestra Ubicación
                        </h2>
                        <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                            Visítanos en nuestra tienda física o contáctanos para más información
                        </p>
                    </div>
                    <div className="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div className="aspect-w-16 aspect-h-9">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d196353.653626959!2d-65.6943301661656!3d-24.883576883401513!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x941bc14f9aaaaaab%3A0x69dac60239564277!2sUniversidad%20Cat%C3%B3lica%20de%20Salta!5e1!3m2!1ses!2sar!4v1758482752021!5m2!1ses!2sar"
                                width="100%"
                                height="450"
                                style={{border:0}}
                                allowFullScreen=""
                                loading="lazy"
                                referrerPolicy="no-referrer-when-downgrade"
                                className="w-full h-96"
                            ></iframe>
                        </div>
                        <div className="p-8">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h3 className="text-xl font-semibold text-gray-900 mb-4">Información de Contacto</h3>
                                    <div className="space-y-3">
                                        <div className="flex items-center">
                                            <span className="text-brand-600 mr-3">📍</span>
                                            <span className="text-gray-600">Universidad Católica de Salta, Salta, Argentina</span>
                                        </div>
                                        <div className="flex items-center">
                                            <span className="text-brand-600 mr-3">📞</span>
                                            <span className="text-gray-600">+54 (387) 123-4567</span>
                                        </div>
                                        <div className="flex items-center">
                                            <span className="text-brand-600 mr-3">✉️</span>
                                            <span className="text-gray-600">support@techstore.com</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-xl font-semibold text-gray-900 mb-4">Horarios de Atención</h3>
                                    <div className="space-y-2">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Lunes - Viernes:</span>
                                            <span className="text-gray-900 font-medium">9:00 - 18:00</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Sábados:</span>
                                            <span className="text-gray-900 font-medium">9:00 - 14:00</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Domingos:</span>
                                            <span className="text-gray-900 font-medium">Cerrado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Contact CTA */}
            <section className="py-20 bg-gray-900">
                <div className="container-main text-center">
                    <h2 className="text-3xl lg:text-4xl font-bold text-white mb-6">
                        ¿Tienes alguna pregunta?
                    </h2>
                    <p className="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                        Nuestro equipo está aquí para ayudarte. Contáctanos y te responderemos lo más pronto posible.
                    </p>
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <a
                            href="mailto:support@techstore.com"
                            className="bg-brand-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-brand-700 transition-colors"
                        >
                            Enviar Email
                        </a>
                        <a
                            href="tel:+543871234567"
                            className="border border-gray-600 text-gray-300 px-8 py-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors"
                        >
                            Llamar Ahora
                        </a>
                    </div>
                </div>
            </section>
        </Layout>
    );
}