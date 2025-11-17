import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Pending({ payment_id, status, external_reference }) {
    useEffect(() => {
        console.log('Payment pending:', { payment_id, status, external_reference });
    }, []);

    return (
        <>
            <Head title="Pago Pendiente" />

            <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                        {/* Icono de pendiente */}
                        <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                            <svg className="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        {/* Título */}
                        <div className="text-center">
                            <h1 className="text-2xl font-bold text-gray-900 mb-2">
                                Pago Pendiente
                            </h1>
                            <p className="text-gray-600 mb-6">
                                Tu pago está siendo procesado
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
                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {status === 'pending' || status === 'in_process' ? 'Pendiente' : status}
                                        </span>
                                    </div>
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
                                        ¿Qué significa esto?
                                    </h3>
                                    <div className="mt-2 text-sm text-blue-700">
                                        <ul className="list-disc pl-5 space-y-1">
                                            <li>Tu pago está siendo verificado</li>
                                            <li>Puede tardar de minutos a horas en confirmarse</li>
                                            <li>Recibirás una notificación cuando se complete</li>
                                            <li>No es necesario volver a realizar el pago</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Métodos de pago comunes que quedan pendientes */}
                        <div className="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                            <h3 className="text-sm font-medium text-gray-800 mb-2">
                                Métodos de pago que pueden quedar pendientes:
                            </h3>
                            <div className="text-sm text-gray-600">
                                <ul className="list-disc pl-5 space-y-1">
                                    <li>Transferencia bancaria</li>
                                    <li>Pago en efectivo (Rapipago, Pago Fácil)</li>
                                    <li>Débito automático</li>
                                </ul>
                            </div>
                        </div>

                        {/* Botones de acción */}
                        <div className="space-y-3">
                            <a
                                href="/customer"
                                className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Ver mis Pedidos
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
