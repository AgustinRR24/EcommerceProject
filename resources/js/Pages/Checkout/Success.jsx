import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Success({ payment_id, status, external_reference, order }) {
    useEffect(() => {
        // Si hay información del pago, podrías hacer alguna acción adicional aquí
        console.log('Payment processed:', { payment_id, status, external_reference });
    }, []);

    return (
        <>
            <Head title="Pago Exitoso" />

            <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                        {/* Icono de éxito */}
                        <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                            <svg className="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        {/* Título */}
                        <div className="text-center">
                            <h1 className="text-2xl font-bold text-gray-900 mb-2">
                                ¡Pago Exitoso!
                            </h1>
                            <p className="text-gray-600 mb-6">
                                Tu pedido ha sido procesado correctamente
                            </p>
                        </div>

                        {/* Información del pedido */}
                        <div className="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-3">
                                Detalles del Pedido
                            </h3>

                            <div className="space-y-2">
                                {external_reference && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Número de Orden:</span>
                                        <span className="font-medium text-gray-900">
                                            {external_reference}
                                        </span>
                                    </div>
                                )}

                                {payment_id && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">ID de Pago:</span>
                                        <span className="font-medium text-gray-900">
                                            {payment_id}
                                        </span>
                                    </div>
                                )}

                                {status && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Estado:</span>
                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {status === 'approved' ? 'Aprobado' : status}
                                        </span>
                                    </div>
                                )}

                                {order && (
                                    <>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Total Pagado:</span>
                                            <span className="font-medium text-gray-900">
                                                ${order.total}
                                            </span>
                                        </div>

                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Método de Pago:</span>
                                            <span className="font-medium text-gray-900">
                                                MercadoPago
                                            </span>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>

                        {/* Información adicional */}
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg className="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"></path>
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <h3 className="text-sm font-medium text-blue-800">
                                        ¿Qué sigue?
                                    </h3>
                                    <div className="mt-2 text-sm text-blue-700">
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Recibirás un email de confirmación</li>
                                            <li>Tu pedido será procesado en las próximas 24 horas</li>
                                            <li>Te notificaremos cuando sea enviado</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Botones de acción */}
                        <div className="space-y-3">
                            <a
                                href="/"
                                className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Volver al Inicio
                            </a>

                            <a
                                href="/orders"
                                className="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Ver mis Pedidos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}