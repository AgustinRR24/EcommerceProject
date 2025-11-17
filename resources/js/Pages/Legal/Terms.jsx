import Layout from '../../Components/Layout';

export default function Terms() {
    return (
        <Layout>
            <div className="py-12 bg-gray-50">
                <div className="container-main max-w-4xl">
                    <div className="bg-white rounded-lg shadow-sm p-8 md:p-12">
                        <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                            Términos y Condiciones
                        </h1>

                        <div className="prose prose-gray max-w-none">
                            <p className="text-gray-600 mb-6">
                                Última actualización: {new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </p>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">1. Aceptación de los Términos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Al acceder y utilizar este sitio web de comercio electrónico, usted acepta cumplir y estar sujeto a los siguientes términos y condiciones de uso. Si no está de acuerdo con alguna parte de estos términos, no debe utilizar nuestro sitio web.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">2. Uso del Sitio</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Este sitio web está destinado únicamente para uso personal y no comercial. Usted se compromete a:
                                </p>
                                <ul className="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                    <li>Proporcionar información precisa y actualizada al realizar compras</li>
                                    <li>No utilizar el sitio para fines ilegales o no autorizados</li>
                                    <li>No interferir con el funcionamiento del sitio</li>
                                    <li>Mantener la confidencialidad de su cuenta y contraseña</li>
                                </ul>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">3. Productos y Precios</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Hacemos todo lo posible para mostrar los productos con la mayor precisión posible. Sin embargo, no garantizamos que las descripciones, imágenes o contenido del sitio sean precisos, completos o libres de errores. Los precios están sujetos a cambios sin previo aviso. Nos reservamos el derecho de limitar las cantidades de productos ofrecidos y de corregir cualquier error de precio.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">4. Proceso de Compra</h2>
                                <p className="text-gray-700 leading-relaxed mb-3">
                                    Al realizar un pedido, usted está haciendo una oferta para comprar el producto. Nos reservamos el derecho de aceptar o rechazar cualquier pedido. La confirmación del pedido no constituye la aceptación de su oferta hasta que el producto sea enviado.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">5. Pagos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Aceptamos diversos métodos de pago según se indique en el proceso de compra. Todos los pagos deben realizarse en la moneda especificada. Utilizamos procesadores de pago seguros para proteger su información financiera.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">6. Envío y Entrega</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Los tiempos de envío son estimaciones y pueden variar. No nos hacemos responsables de retrasos causados por servicios de mensajería de terceros. El riesgo de pérdida y el título de los productos pasan a usted en el momento de la entrega al transportista.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">7. Devoluciones y Reembolsos</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Los productos pueden ser devueltos dentro de los 30 días posteriores a la recepción, siempre que estén en su estado original y con el embalaje intacto. Los gastos de envío de devolución corren por cuenta del cliente, a menos que el producto esté defectuoso o haya sido enviado incorrectamente.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">8. Propiedad Intelectual</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Todo el contenido del sitio, incluyendo textos, gráficos, logotipos, imágenes y software, es propiedad nuestra o de nuestros proveedores de contenido y está protegido por las leyes de propiedad intelectual.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">9. Limitación de Responsabilidad</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    En la máxima medida permitida por la ley, no seremos responsables de daños indirectos, incidentales, especiales, consecuentes o punitivos, ni de pérdidas de beneficios o ingresos derivados del uso de nuestro sitio web o productos.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">10. Modificaciones</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento. Los cambios entrarán en vigor inmediatamente después de su publicación en el sitio web. Su uso continuado del sitio después de dichos cambios constituye su aceptación de los nuevos términos.
                                </p>
                            </section>

                            <section className="mb-8">
                                <h2 className="text-2xl font-semibold text-gray-900 mb-4">11. Contacto</h2>
                                <p className="text-gray-700 leading-relaxed">
                                    Si tiene preguntas sobre estos Términos y Condiciones, puede contactarnos en:
                                </p>
                                <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p className="text-gray-700">Email: support@techstore.com</p>
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
