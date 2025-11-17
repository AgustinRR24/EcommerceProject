import Layout from '../../Components/Layout';

export default function LegalNotice() {
    return (
        <Layout>
            <div className="py-12 bg-gray-50">
                <div className="container-main max-w-4xl">
                    <div className="bg-white rounded-lg shadow-sm p-8 md:p-12">
                        <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                            Aviso Legal
                        </h1>

                        <div className="prose prose-gray max-w-none">
                            <p className="text-gray-600 mb-6">
                                Última actualización: {new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </p>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">1. Datos Identificativos</h2>
                                <div className="bg-gray-50 p-6 rounded-lg space-y-2">
                                    <p className="text-gray-700"><strong>Denominación social:</strong> Tech Store S.A.</p>
                                    <p className="text-gray-700"><strong>Domicilio social:</strong> Universidad Católica de Salta, Salta, Argentina</p>
                                    <p className="text-gray-700"><strong>Email:</strong> legal@techstore.com</p>
                                    <p className="text-gray-700"><strong>Teléfono:</strong> +54 (387) 123-4567</p>
                                    <p className="text-gray-700"><strong>CUIT:</strong> 30-12345678-9</p>
                                </div>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">2. Objeto</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    El presente aviso legal regula el uso del sitio web techstore.com (en adelante, el "Sitio Web"), del que es titular Tech Store S.A. La navegación por el Sitio Web atribuye la condición de usuario del mismo e implica la aceptación plena y sin reservas de todas y cada una de las disposiciones incluidas en este Aviso Legal.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">3. Condiciones de Acceso y Utilización</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    El acceso y uso del Sitio Web es gratuito. El usuario se compromete a hacer un uso correcto del Sitio Web de conformidad con las leyes aplicables y se obliga a:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>No utilizar el Sitio Web con fines ilícitos o contrarios a lo establecido en el presente Aviso Legal</li>
                                    <li>No realizar actividades publicitarias o de explotación comercial no autorizadas</li>
                                    <li>No dañar, deshabilitar o sobrecargar el Sitio Web</li>
                                    <li>No introducir virus, código malicioso o tecnología similar</li>
                                    <li>No intentar acceder a áreas restringidas del sitio o a sistemas informáticos</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">4. Propiedad Intelectual e Industrial</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Todos los contenidos del Sitio Web (incluyendo, sin limitación, bases de datos, imágenes, dibujos, gráficos, archivos de texto, audio, vídeo y software) son propiedad de Tech Store S.A. o de terceros que han autorizado su uso, y están protegidos por las normativas nacionales e internacionales de propiedad intelectual e industrial.
                                </p>
                                <p className="text-gray-700 leading-relaxed mt-3">
                                    Queda expresamente prohibida la reproducción, distribución, comunicación pública, transformación o cualquier otra forma de explotación de los contenidos sin la autorización expresa y por escrito de Tech Store S.A.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">5. Responsabilidad</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Tech Store S.A. no se hace responsable de:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>La continuidad y disponibilidad de los contenidos del Sitio Web</li>
                                    <li>La ausencia de errores en dichos contenidos ni la corrección de cualquier defecto</li>
                                    <li>Los daños producidos por el uso inadecuado del Sitio Web por parte de los usuarios</li>
                                    <li>Los contenidos de las páginas web de terceros a las que se pueda acceder mediante enlaces</li>
                                    <li>La presencia de virus o elementos en las páginas web de terceros que puedan causar alteraciones</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">6. Enlaces</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    El Sitio Web puede contener enlaces a otros sitios web. Tech Store S.A. no ejerce control alguno sobre dichos sitios web y no se hace responsable de sus contenidos. El establecimiento de cualquier tipo de enlace por parte del Sitio Web a otro sitio web ajeno no implica que exista algún tipo de relación, colaboración o dependencia entre Tech Store S.A. y el responsable del sitio web ajeno.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">7. Protección de Datos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Para conocer cómo tratamos sus datos personales, consulte nuestra <a href="/privacy" className="text-brand-600 hover:text-brand-700 underline">Política de Privacidad</a>.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">8. Cookies</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    El Sitio Web utiliza cookies propias y de terceros para mejorar la experiencia de navegación, realizar análisis de uso del sitio y mostrar publicidad relacionada con las preferencias del usuario. Para más información, consulte nuestra Política de Cookies.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">9. Modificaciones</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Tech Store S.A. se reserva el derecho de efectuar sin previo aviso las modificaciones que considere oportunas en el Sitio Web, pudiendo cambiar, suprimir o añadir tanto los contenidos y servicios que se presten a través del mismo como la forma en la que estos aparezcan presentados o localizados.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">10. Legislación Aplicable y Jurisdicción</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    La relación entre Tech Store S.A. y el usuario se regirá por la normativa argentina vigente. Para la resolución de cualquier controversia derivada del acceso o uso del Sitio Web, las partes se someten a los juzgados y tribunales de la Ciudad de Salta, Argentina.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">11. Contacto</h2>
                                <p className="text-gray-700 leading-relaxed mb-4">
                                    Para cualquier consulta relacionada con este Aviso Legal, puede contactarnos en:
                                </p>
                                <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p className="text-gray-700">Email: legal@techstore.com</p>
                                    <p className="text-gray-700">Teléfono: +54 (387) 123-4567</p>
                                    <p className="text-gray-700">Dirección: Universidad Católica de Salta, Salta, Argentina</p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
