import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Failure({ payment_id, status, external_reference }) {
    useEffect(() => {
        console.log('Payment failed:', { payment_id, status, external_reference });
    }, []);

    return (
        <>
            <Head title="Pago Rechazado" />

            <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                        {/* Icono de error */}
                        <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                            <svg className="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>

                        {/* Título */}
                        <div className="text-center">
                            <h1 className="text-2xl font-bold text-gray-900 mb-2">
                                Pago Rechazado
                            </h1>
                            <p className="text-gray-600 mb-6">
                                Lo sentimos, no pudimos procesar tu pago
                            </p>
                        </div>

                        {/* Información del pago */}
                        <div className="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-3">
                                Detalles
                            </h3>

                            <div className="space-y-2">
                                {external_reference && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Referencia:</span>
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
                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {status === 'rejected' ? 'Rechazado' : status}
                                        </span>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Información adicional */}
                        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg className="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"></path>
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <h3 className="text-sm font-medium text-yellow-800">
                                        Posibles causas
                                    </h3>
                                    <div className="mt-2 text-sm text-yellow-700">
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Fondos insuficientes</li>
                                            <li>Datos de tarjeta incorrectos</li>
                                            <li>Límite de compra excedido</li>
                                            <li>Tarjeta vencida o bloqueada</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Botones de acción */}
                        <div className="space-y-3">
                            <a
                                href="/checkout"
                                className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Intentar Nuevamente
                            </a>

                            <a
                                href="/cart"
                                className="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Volver al Carrito
                            </a>

                            <a
                                href="/"
                                className="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
