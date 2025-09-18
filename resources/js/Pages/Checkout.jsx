import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Checkout({ cartItems, subtotal, shipping, tax, total, user }) {
    const [isProcessing, setIsProcessing] = useState(false);
    const [formData, setFormData] = useState({
        // Información de envío
        shipping_name: user.name || '',
        shipping_email: user.email || '',
        shipping_phone: '',
        shipping_address: '',
        shipping_city: '',
        shipping_state: '',
        shipping_zip: '',

        // Información de pago
        payment_method: 'credit_card',
        card_number: '',
        card_expiry: '',
        card_cvv: '',
        card_name: ''
    });

    const formatPrice = (price) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsProcessing(true);

        try {
            const response = await fetch('/checkout/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                alert('¡Pedido procesado exitosamente!');
                router.visit('/');
            } else {
                alert('Error al procesar el pedido');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar el pedido');
        } finally {
            setIsProcessing(false);
        }
    };

    return (
        <Layout>
            <div style={{ maxWidth: '80rem', margin: '0 auto', padding: '2rem 1rem' }}>
                {/* Header */}
                <div style={{ marginBottom: '2rem' }}>
                    <h1 style={{ fontSize: '2rem', fontWeight: 'bold', color: '#1f2937', marginBottom: '0.5rem' }}>
                        Checkout
                    </h1>
                    <p style={{ color: '#6b7280' }}>
                        Complete your order information
                    </p>
                </div>

                <div style={{ display: 'grid', gap: '2rem', gridTemplateColumns: '1fr', '@media (min-width: 1024px)': { gridTemplateColumns: '2fr 1fr' } }}>
                    {/* Formulario de Checkout */}
                    <div>
                        <form onSubmit={handleSubmit} style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '2rem' }}>
                            {/* Información de Envío */}
                            <div style={{ marginBottom: '2rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Shipping Information
                                </h2>

                                <div style={{ display: 'grid', gap: '1rem', gridTemplateColumns: '1fr 1fr' }}>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Full Name
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
                                            Email
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
                                        Phone
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
                                        Address
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
                                            City
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
                                            State
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
                                            ZIP Code
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

                            {/* Información de Pago */}
                            <div style={{ marginBottom: '2rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Payment Information
                                </h2>

                                <div style={{ marginBottom: '1rem' }}>
                                    <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                        Card Number
                                    </label>
                                    <input
                                        type="text"
                                        name="card_number"
                                        value={formData.card_number}
                                        onChange={handleInputChange}
                                        placeholder="1234 5678 9012 3456"
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

                                <div style={{ display: 'grid', gap: '1rem', gridTemplateColumns: '1fr 1fr' }}>
                                    <div>
                                        <label style={{ display: 'block', fontSize: '0.875rem', fontWeight: '500', color: '#374151', marginBottom: '0.5rem' }}>
                                            Expiry Date
                                        </label>
                                        <input
                                            type="text"
                                            name="card_expiry"
                                            value={formData.card_expiry}
                                            onChange={handleInputChange}
                                            placeholder="MM/YY"
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
                                            CVV
                                        </label>
                                        <input
                                            type="text"
                                            name="card_cvv"
                                            value={formData.card_cvv}
                                            onChange={handleInputChange}
                                            placeholder="123"
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
                                        Cardholder Name
                                    </label>
                                    <input
                                        type="text"
                                        name="card_name"
                                        value={formData.card_name}
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
                                    Back to Cart
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
                                    {isProcessing ? 'Processing...' : `Place Order - ${formatPrice(total)}`}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Order Summary */}
                    <div>
                        <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '1.5rem' }}>
                            <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                Order Summary
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
                                                    Qty: {item.quantity}
                                                </p>
                                            </div>
                                        </div>
                                        <span style={{ fontSize: '0.875rem', fontWeight: '500', color: '#1f2937' }}>
                                            {formatPrice(item.total)}
                                        </span>
                                    </div>
                                ))}
                            </div>

                            {/* Totals */}
                            <div style={{ borderTop: '1px solid #f3f4f6', paddingTop: '1rem' }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280' }}>Subtotal</span>
                                    <span style={{ color: '#1f2937' }}>{formatPrice(subtotal)}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280' }}>Shipping</span>
                                    <span style={{ color: '#16a34a' }}>Free</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                    <span style={{ color: '#6b7280' }}>Tax</span>
                                    <span style={{ color: '#1f2937' }}>{formatPrice(tax)}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', paddingTop: '0.5rem', borderTop: '1px solid #f3f4f6' }}>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>Total</span>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>{formatPrice(total)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}