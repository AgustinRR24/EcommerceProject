import Layout from '../../Components/Layout';

export default function Privacy() {
    return (
        <Layout>
            <div className="py-12 bg-gray-50">
                <div className="container-main max-w-4xl">
                    <div className="bg-white rounded-lg shadow-sm p-8 md:p-12">
                        <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                            Políticas de Privacidad
                        </h1>

                        <div className="prose prose-gray max-w-none">
                            <p className="text-gray-600 mb-6">
                                Última actualización: {new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </p>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">1. Introducción</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    En Tech Store, nos comprometemos a proteger su privacidad y garantizar la seguridad de su información personal. Esta Política de Privacidad explica cómo recopilamos, utilizamos, divulgamos y protegemos su información cuando visita nuestro sitio web y realiza compras.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">2. Información que Recopilamos</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Recopilamos diferentes tipos de información con diversos fines:
                                </p>
                                <div className="space-y-4">
                                    <div>
                                        <h3 className="text-lg font-semibold text-gray-800 mb-2">2.1 Información Personal</h3>
                                        <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                            <li>Nombre y apellidos</li>
                                            <li>Dirección de correo electrónico</li>
                                            <li>Número de teléfono</li>
                                            <li>Dirección de envío y facturación</li>
                                            <li>Información de pago (procesada de forma segura)</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-semibold text-gray-800 mb-2">2.2 Información de Uso</h3>
                                        <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                            <li>Dirección IP</li>
                                            <li>Tipo de navegador</li>
                                            <li>Páginas visitadas</li>
                                            <li>Tiempo de visita</li>
                                            <li>Cookies y tecnologías similares</li>
                                        </ul>
                                    </div>
                                </div>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">3. Cómo Utilizamos su Información</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Utilizamos la información recopilada para los siguientes propósitos:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>Procesar y completar sus pedidos</li>
                                    <li>Gestionar su cuenta de usuario</li>
                                    <li>Enviar confirmaciones de pedido y actualizaciones de envío</li>
                                    <li>Responder a sus consultas y proporcionar soporte al cliente</li>
                                    <li>Personalizar su experiencia de compra</li>
                                    <li>Enviar ofertas promocionales (con su consentimiento)</li>
                                    <li>Mejorar nuestro sitio web y servicios</li>
                                    <li>Prevenir fraudes y garantizar la seguridad</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">4. Compartir Información</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    No vendemos ni alquilamos su información personal a terceros. Podemos compartir su información con:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>Proveedores de servicios de pago (para procesar transacciones)</li>
                                    <li>Empresas de envío (para entregar sus pedidos)</li>
                                    <li>Proveedores de servicios técnicos (para mantener el sitio web)</li>
                                    <li>Autoridades legales (cuando sea requerido por ley)</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">5. Cookies</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Utilizamos cookies y tecnologías similares para mejorar su experiencia de navegación, analizar el tráfico del sitio y personalizar el contenido. Puede configurar su navegador para rechazar cookies, aunque esto puede limitar algunas funcionalidades del sitio.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">6. Seguridad de Datos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Implementamos medidas de seguridad técnicas y organizativas apropiadas para proteger su información personal contra acceso no autorizado, alteración, divulgación o destrucción. Esto incluye el uso de cifrado SSL para todas las transacciones y el almacenamiento seguro de datos.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">7. Sus Derechos</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Usted tiene derecho a:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>Acceder a su información personal</li>
                                    <li>Corregir información inexacta</li>
                                    <li>Solicitar la eliminación de su información</li>
                                    <li>Oponerse al procesamiento de su información</li>
                                    <li>Solicitar la portabilidad de sus datos</li>
                                    <li>Retirar su consentimiento en cualquier momento</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">8. Retención de Datos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Conservamos su información personal solo durante el tiempo necesario para cumplir con los fines para los que fue recopilada, incluyendo requisitos legales, contables o de informes.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">9. Menores de Edad</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Nuestro sitio web no está dirigido a menores de 18 años. No recopilamos intencionalmente información personal de menores. Si descubrimos que hemos recopilado información de un menor, la eliminaremos de inmediato.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">10. Cambios a esta Política</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Podemos actualizar esta Política de Privacidad periódicamente. Le notificaremos cualquier cambio publicando la nueva política en esta página y actualizando la fecha de "última actualización".
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">11. Contacto</h2>
                                <p className="text-gray-700 leading-relaxed mb-4">
                                    Si tiene preguntas sobre esta Política de Privacidad o desea ejercer sus derechos, puede contactarnos en:
                                </p>
                                <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p className="text-gray-700">Email: privacy@techstore.com</p>
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
