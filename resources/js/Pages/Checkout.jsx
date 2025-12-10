import { useState, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Checkout({ cartItems, subtotal, shipping, tax, total, user }) {
    const [isProcessing, setIsProcessing] = useState(false);
    const [showWallet, setShowWallet] = useState(false);
    const [preferenceId, setPreferenceId] = useState(null);
    const [publicKey, setPublicKey] = useState(null);
    const [formData, setFormData] = useState({
        // Información de envío
        shipping_name: user.name || '',
        shipping_email: user.email || '',
        shipping_phone: '',
        shipping_address: '',
        shipping_city: '',
        shipping_state: '',
        shipping_zip: '',
    });
    const [promoCode, setPromoCode] = useState('');
    const [appliedPromo, setAppliedPromo] = useState(null);
    const [isValidatingPromo, setIsValidatingPromo] = useState(false);
    const [promoMessage, setPromoMessage] = useState('');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(price);
    };

    const IVA_RATE = 0.21;

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const validatePromoCode = async () => {
        if (!promoCode.trim()) return;

        setIsValidatingPromo(true);
        setPromoMessage('');

        try {
            const response = await fetch('/checkout/validate-promo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ code: promoCode })
            });

            const data = await response.json();

            if (data.success) {
                setAppliedPromo(data.promo_code);
                setPromoMessage(data.message);
                setPromoCode('');
            } else {
                setPromoMessage(data.message);
                setAppliedPromo(null);
            }
        } catch (error) {
            console.error('Error validating promo code:', error);
            setPromoMessage('Error al validar el código promocional');
        } finally {
            setIsValidatingPromo(false);
        }
    };

    const removePromoCode = () => {
        setAppliedPromo(null);
        setPromoMessage('');
        setPromoCode('');
    };

    // Calcular descuento
    const discountAmount = appliedPromo ? (subtotal * appliedPromo.discount_percentage) / 100 : 0;
    const finalTotal = total - discountAmount;

    // Calcular precios sin IVA
    const subtotalWithoutIVA = subtotal / (1 + IVA_RATE);
    const discountWithoutIVA = discountAmount / (1 + IVA_RATE);
    const finalTotalWithoutIVA = finalTotal / (1 + IVA_RATE);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsProcessing(true);

        try {
            const checkoutData = {
                ...formData,
                promo_code_id: appliedPromo?.id || null,
                final_total: finalTotal
            };

            console.log('Sending checkout request with data:', checkoutData);

            const response = await fetch('/checkout/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(checkoutData)
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                console.log('Setting wallet data:', {
                    preference_id: data.preference_id,
                    public_key: data.public_key
                });

                // Guardar los datos para inicializar el wallet
                setPreferenceId(data.preference_id);
                setPublicKey(data.public_key);
                setShowWallet(true);
            } else {
                console.error('Checkout error:', data);
                alert('Error al procesar el pedido: ' + (data.error || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar el pedido');
        } finally {
            setIsProcessing(false);
        }
    };

    // Inicializar MercadoPago Wallet cuando tengamos los datos
    useEffect(() => {
        console.log('MercadoPago useEffect:', {
            showWallet,
            preferenceId,
            publicKey,
            hasMercadoPago: !!window.MercadoPago
        });

        if (showWallet && preferenceId && publicKey) {
            if (!window.MercadoPago) {
                console.error('MercadoPago SDK not loaded');
                alert('Error: MercadoPago SDK no está cargado. Por favor recarga la página.');
                return;
            }

            const mp = new window.MercadoPago(publicKey);
            const bricksBuilder = mp.bricks();

            const renderWalletBrick = async () => {
                try {
                    console.log('Creating wallet brick with preferenceId:', preferenceId);

                    // Limpiar el container antes de crear el brick
                    const container = document.getElementById("walletBrick_container");
                    if (container) {
                        container.innerHTML = '';
                    } else {
                        console.error('Container walletBrick_container not found');
                        return;
                    }

                    await bricksBuilder.create("wallet", "walletBrick_container", {
                        initialization: {
                            preferenceId: preferenceId,
                        },
                        customization: {
                            texts: {
                                valueProp: 'smart_option',
                            },
                        },
                    });
                    console.log('Wallet brick created successfully');
                } catch (error) {
                    console.error('Error creating wallet brick:', error);
                    alert('Error al cargar el botón de pago. Por favor intenta nuevamente.');
                }
            };

            // Pequeño delay para asegurar que el DOM está listo
            setTimeout(() => {
                renderWalletBrick();
            }, 100);
        }
    }, [showWallet, preferenceId, publicKey]);

    return (
        <Layout>
            <div style={{ maxWidth: '80rem', margin: '0 auto', padding: '2rem 1rem' }}>
                {/* Header */}
                <div style={{ marginBottom: '2rem' }}>
                    <h1 style={{ fontSize: '2rem', fontWeight: 'bold', color: '#1f2937', marginBottom: '0.5rem' }}>
                        Finalizar Compra
                    </h1>
                    <p style={{ color: '#6b7280' }}>
                        Completa la información de tu pedido
                    </p>
                </div>

                <div style={{
                    display: 'grid',
                    gap: '2rem',
                    gridTemplateColumns: window.innerWidth >= 1024 ? '2fr 1fr' : '1fr'
                }}>
                    {/* Formulario de Checkout */}
                    <div>
                        {!showWallet ? (
                            <form onSubmit={handleSubmit} style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '2rem' }}>
                            {/* Información de Envío */}
                            <div style={{ marginBottom: '2rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Información de Envío
                                </h2>

                                <div style={{ display: 'grid', gap: '1rem', gridTemplateColumns: '1fr 1fr' }}>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Nombre Completo
                                        </label>
                                        <input
                                            type="text"
                                            name="shipping_name"
                                            value={formData.shipping_name}
                                            onChange={handleInputChange}
                                            required
                                            style={{
                                                width: '100%',
                                                padding: '0.75rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                outline: 'none'
                                            }}
                                        />
                                    </div>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Correo Electrónico
                                        </label>
                                        <input
                                            type="email"
                                            name="shipping_email"
                                            value={formData.shipping_email}
                                            onChange={handleInputChange}
                                            required
                                            style={{
                                                width: '100%',
                                                padding: '0.75rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                outline: 'none'
                                            }}
                                        />
                                    </div>
                                </div>

                                <div style={{ marginTop: '1rem' }}>
                                    <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                        Teléfono
                                    </label>
                                    <input
                                        type="tel"
                                        name="shipping_phone"
                                        value={formData.shipping_phone}
                                        onChange={handleInputChange}
                                        required
                                        style={{
                                            width: '100%',
                                            padding: '0.75rem',
                                            border: '1px solid #d1d5db',
                                            borderRadius: '0.375rem',
                                            outline: 'none'
                                        }}
                                    />
                                </div>

                                <div style={{ marginTop: '1rem' }}>
                                    <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                        Dirección
                                    </label>
                                    <input
                                        type="text"
                                        name="shipping_address"
                                        value={formData.shipping_address}
                                        onChange={handleInputChange}
                                        required
                                        style={{
                                            width: '100%',
                                            padding: '0.75rem',
                                            border: '1px solid #d1d5db',
                                            borderRadius: '0.375rem',
                                            outline: 'none'
                                        }}
                                    />
                                </div>

                                <div style={{ display: 'grid', gap: '1rem', gridTemplateColumns: '1fr 1fr 1fr', marginTop: '1rem' }}>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Ciudad
                                        </label>
                                        <input
                                            type="text"
                                            name="shipping_city"
                                            value={formData.shipping_city}
                                            onChange={handleInputChange}
                                            required
                                            style={{
                                                width: '100%',
                                                padding: '0.75rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                outline: 'none'
                                            }}
                                        />
                                    </div>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Provincia
                                        </label>
                                        <input
                                            type="text"
                                            name="shipping_state"
                                            value={formData.shipping_state}
                                            onChange={handleInputChange}
                                            required
                                            style={{
                                                width: '100%',
                                                padding: '0.75rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                outline: 'none'
                                            }}
                                        />
                                    </div>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Código Postal
                                        </label>
                                        <input
                                            type="text"
                                            name="shipping_zip"
                                            value={formData.shipping_zip}
                                            onChange={handleInputChange}
                                            required
                                            style={{
                                                width: '100%',
                                                padding: '0.75rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                outline: 'none'
                                            }}
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Nota sobre el Pago */}
                            <div style={{ marginBottom: '2rem', padding: '1rem', backgroundColor: '#f0f9ff', borderRadius: '0.5rem', border: '1px solid #bfdbfe' }}>
                                <h2 style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1e40af', marginBottom: '0.5rem' }}>
                                    💳 Pago con MercadoPago
                                </h2>
                                <p style={{ color: '#1e40af', fontSize: '0.875rem' }}>
                                    Serás redirigido a MercadoPago para completar tu pago de forma segura.
                                    Puedes pagar con tarjetas de crédito, débito, transferencias bancarias y más.
                                </p>
                            </div>

                            {/* Botones */}
                            <div style={{ display: 'flex', gap: '1rem', justifyContent: 'space-between' }}>
                                <Link
                                    href="/cart"
                                    style={{
                                        backgroundColor: '#f3f4f6',
                                        color: '#374151',
                                        padding: '0.75rem 1.5rem',
                                        borderRadius: '0.375rem',
                                        textDecoration: 'none',
                                        fontWeight: '500'
                                    }}
                                >
                                    Volver al Carrito
                                </Link>
                                <button
                                    type="submit"
                                    disabled={isProcessing}
                                    style={{
                                        backgroundColor: '#2563eb',
                                        color: 'white',
                                        padding: '0.75rem 2rem',
                                        borderRadius: '0.375rem',
                                        border: 'none',
                                        fontWeight: '500',
                                        cursor: isProcessing ? 'not-allowed' : 'pointer',
                                        opacity: isProcessing ? 0.5 : 1
                                    }}
                                >
                                    {isProcessing ? 'Procesando...' : 'Continuar al Pago'}
                                </button>
                            </div>
                        </form>
                        ) : (
                            <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '2rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Completa tu Pago
                                </h2>
                                <p style={{ color: '#6b7280', marginBottom: '1.5rem' }}>
                                    Haz clic en el botón de abajo para completar tu pago con MercadoPago
                                </p>

                                {/* Container para el botón de MercadoPago */}
                                <div id="walletBrick_container"></div>

                                <button
                                    onClick={() => setShowWallet(false)}
                                    style={{
                                        marginTop: '1rem',
                                        backgroundColor: '#f3f4f6',
                                        color: '#374151',
                                        padding: '0.5rem 1rem',
                                        borderRadius: '0.375rem',
                                        border: 'none',
                                        cursor: 'pointer',
                                        fontSize: '0.875rem'
                                    }}
                                >
                                    ← Volver a Información de Envío
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Order Summary */}
                    <div>
                        <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '1.5rem' }}>
                            <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                Resumen del Pedido
                            </h2>

                            {/* Items */}
                            <div style={{ marginBottom: '1rem' }}>
                                {cartItems.map((item) => (
                                    <div key={item.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                                            <img
                                                src={item.product.image ? `/storage/${item.product.image}` : 'https://via.placeholder.com/50x50?text=Product'}
                                                alt={item.product.name}
                                                style={{ width: '50px', height: '50px', objectFit: 'cover', borderRadius: '0.25rem' }}
                                            />
                                            <div>
                                                <p style={{ fontSize: '0.875rem', fontWeight: '500', color: '#1f2937' }}>
                                                    {item.product.name}
                                                </p>
                                                <p style={{ fontSize: '0.75rem', color: '#6b7280' }}>
                                                    Cant: {item.quantity}
                                                </p>
                                            </div>
                                        </div>
                                        <span style={{ fontSize: '0.875rem', fontWeight: '500', color: '#1f2937' }}>
                                            {formatPrice(item.total)}
                                        </span>
                                    </div>
                                ))}
                            </div>

                            {/* Promo Code Section */}
                            <div style={{ borderTop: '1px solid #f3f4f6', paddingTop: '1rem', marginBottom: '1rem' }}>
                                <h3 style={{ fontSize: '1rem', fontWeight: '600', color: '#1f2937', marginBottom: '0.75rem' }}>
                                    Código Promocional
                                </h3>

                                {!appliedPromo ? (
                                    <div style={{ display: 'flex', gap: '0.5rem' }}>
                                        <input
                                            type="text"
                                            value={promoCode}
                                            onChange={(e) => setPromoCode(e.target.value.toUpperCase())}
                                            onKeyPress={(e) => e.key === 'Enter' && validatePromoCode()}
                                            placeholder="Ingresa el código"
                                            style={{
                                                flex: 1,
                                                padding: '0.5rem',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.375rem',
                                                fontSize: '0.875rem',
                                                outline: 'none'
                                            }}
                                        />
                                        <button
                                            onClick={validatePromoCode}
                                            disabled={isValidatingPromo || !promoCode.trim()}
                                            style={{
                                                backgroundColor: '#2563eb',
                                                color: 'white',
                                                border: 'none',
                                                borderRadius: '0.375rem',
                                                padding: '0.5rem 1rem',
                                                fontSize: '0.875rem',
                                                cursor: 'pointer',
                                                opacity: (isValidatingPromo || !promoCode.trim()) ? 0.5 : 1
                                            }}
                                        >
                                            {isValidatingPromo ? 'Validando...' : 'Aplicar'}
                                        </button>
                                    </div>
                                ) : (
                                    <div style={{
                                        backgroundColor: '#dcfce7',
                                        border: '1px solid #bbf7d0',
                                        borderRadius: '0.375rem',
                                        padding: '0.75rem',
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center'
                                    }}>
                                        <div>
                                            <span style={{ color: '#15803d', fontWeight: '500', fontSize: '0.875rem' }}>
                                                {appliedPromo.code} - {appliedPromo.discount_percentage}% OFF
                                            </span>
                                        </div>
                                        <button
                                            onClick={removePromoCode}
                                            style={{
                                                color: '#dc2626',
                                                backgroundColor: 'transparent',
                                                border: 'none',
                                                cursor: 'pointer',
                                                fontSize: '0.75rem',
                                                textDecoration: 'underline'
                                            }}
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                )}

                                {promoMessage && (
                                    <p style={{
                                        fontSize: '0.75rem',
                                        marginTop: '0.5rem',
                                        color: appliedPromo ? '#15803d' : '#dc2626'
                                    }}>
                                        {promoMessage}
                                    </p>
                                )}
                            </div>

                            {/* Totals */}
                            <div style={{ borderTop: '1px solid #f3f4f6', paddingTop: '1rem' }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280' }}>Subtotal</span>
                                    <span style={{ color: '#1f2937' }}>{formatPrice(subtotal)}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280', fontSize: '0.875rem' }}>Sin IVA: {formatPrice(subtotalWithoutIVA)}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280' }}>Envío</span>
                                    <span style={{ color: '#16a34a' }}>Gratis</span>
                                </div>
                                {appliedPromo && (
                                    <>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                            <span style={{ color: '#15803d' }}>Descuento ({appliedPromo.discount_percentage}%)</span>
                                            <span style={{ color: '#15803d' }}>-{formatPrice(discountAmount)}</span>
                                        </div>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                            <span style={{ color: '#15803d', fontSize: '0.875rem' }}>Sin IVA: -{formatPrice(discountWithoutIVA)}</span>
                                        </div>
                                    </>
                                )}
                                <div style={{ display: 'flex', justifyContent: 'space-between', paddingTop: '0.5rem', borderTop: '1px solid #f3f4f6' }}>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>Total</span>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>{formatPrice(finalTotal)}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: '0.25rem' }}>
                                    <span style={{ fontSize: '0.875rem', color: '#6b7280' }}>Sin IVA: {formatPrice(finalTotalWithoutIVA)}</span>
                                    <span style={{ fontSize: '0.75rem', color: '#9ca3af' }}>(IVA 21% incluido)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}