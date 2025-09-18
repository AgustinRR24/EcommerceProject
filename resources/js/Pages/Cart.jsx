import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';

export default function Cart({ cartItems, total, itemCount }) {
    const [isUpdating, setIsUpdating] = useState({});
    const [isRemoving, setIsRemoving] = useState({});

    const formatPrice = (price) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    };

    const updateQuantity = async (itemId, newQuantity) => {
        if (newQuantity < 1) return;

        setIsUpdating(prev => ({ ...prev, [itemId]: true }));

        try {
            const response = await fetch(`/cart/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: newQuantity })
            });

            if (response.ok) {
                // Recargar la página para actualizar los datos
                router.reload();
            } else {
                alert('Error al actualizar la cantidad');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar la cantidad');
        } finally {
            setIsUpdating(prev => ({ ...prev, [itemId]: false }));
        }
    };

    const removeItem = async (itemId) => {
        setIsRemoving(prev => ({ ...prev, [itemId]: true }));

        try {
            const response = await fetch(`/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                // Recargar la página para actualizar los datos
                router.reload();
            } else {
                alert('Error al eliminar el producto');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar el producto');
        } finally {
            setIsRemoving(prev => ({ ...prev, [itemId]: false }));
        }
    };

    const clearCart = async () => {
        if (!confirm('¿Estás seguro de que quieres vaciar el carrito?')) return;

        try {
            const response = await fetch('/cart', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                router.reload();
            } else {
                alert('Error al vaciar el carrito');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al vaciar el carrito');
        }
    };

    return (
        <Layout>
            <div style={{ maxWidth: '80rem', margin: '0 auto', padding: '2rem 1rem' }}>
                {/* Header */}
                <div style={{ marginBottom: '2rem' }}>
                    <h1 style={{ fontSize: '2rem', fontWeight: 'bold', color: '#1f2937', marginBottom: '0.5rem' }}>
                        Shopping Cart
                    </h1>
                    <p style={{ color: '#6b7280' }}>
                        {itemCount} {itemCount === 1 ? 'item' : 'items'} in your cart
                    </p>
                </div>

                {cartItems && cartItems.length > 0 ? (
                    <div style={{ display: 'grid', gap: '2rem', gridTemplateColumns: '1fr', '@media (min-width: 1024px)': { gridTemplateColumns: '2fr 1fr' } }}>
                        {/* Cart Items */}
                        <div>
                            <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', overflow: 'hidden' }}>
                                {cartItems.map((item) => (
                                    <div
                                        key={item.id}
                                        style={{
                                            padding: '1.5rem',
                                            borderBottom: '1px solid #f3f4f6',
                                            display: 'flex',
                                            gap: '1rem',
                                            alignItems: 'flex-start'
                                        }}
                                    >
                                        {/* Product Image */}
                                        <div style={{ flexShrink: 0 }}>
                                            <img
                                                src={item.product.image ? `/storage/${item.product.image}` : 'https://via.placeholder.com/100x100?text=Product'}
                                                alt={item.product.name}
                                                style={{
                                                    width: '100px',
                                                    height: '100px',
                                                    objectFit: 'cover',
                                                    borderRadius: '0.5rem',
                                                    border: '1px solid #e5e7eb'
                                                }}
                                            />
                                        </div>

                                        {/* Product Details */}
                                        <div style={{ flex: 1 }}>
                                            <Link
                                                href={`/products/${item.product.id}`}
                                                style={{
                                                    fontSize: '1.125rem',
                                                    fontWeight: '600',
                                                    color: '#1f2937',
                                                    textDecoration: 'none',
                                                    display: 'block',
                                                    marginBottom: '0.5rem'
                                                }}
                                            >
                                                {item.product.name}
                                            </Link>

                                            <p style={{ color: '#6b7280', fontSize: '0.875rem', marginBottom: '1rem' }}>
                                                {formatPrice(item.price)} each
                                            </p>

                                            {/* Quantity Controls */}
                                            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '1rem' }}>
                                                <div style={{ display: 'flex', alignItems: 'center', border: '1px solid #d1d5db', borderRadius: '0.375rem' }}>
                                                    <button
                                                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                                        disabled={isUpdating[item.id] || item.quantity <= 1}
                                                        style={{
                                                            padding: '0.5rem 0.75rem',
                                                            backgroundColor: 'transparent',
                                                            border: 'none',
                                                            color: '#6b7280',
                                                            cursor: 'pointer',
                                                            fontSize: '1rem'
                                                        }}
                                                    >
                                                        -
                                                    </button>
                                                    <span style={{
                                                        padding: '0.5rem 1rem',
                                                        borderLeft: '1px solid #d1d5db',
                                                        borderRight: '1px solid #d1d5db',
                                                        minWidth: '60px',
                                                        textAlign: 'center'
                                                    }}>
                                                        {item.quantity}
                                                    </span>
                                                    <button
                                                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                                        disabled={isUpdating[item.id]}
                                                        style={{
                                                            padding: '0.5rem 0.75rem',
                                                            backgroundColor: 'transparent',
                                                            border: 'none',
                                                            color: '#6b7280',
                                                            cursor: 'pointer',
                                                            fontSize: '1rem'
                                                        }}
                                                    >
                                                        +
                                                    </button>
                                                </div>

                                                <button
                                                    onClick={() => removeItem(item.id)}
                                                    disabled={isRemoving[item.id]}
                                                    style={{
                                                        color: '#dc2626',
                                                        fontSize: '0.875rem',
                                                        backgroundColor: 'transparent',
                                                        border: 'none',
                                                        cursor: 'pointer',
                                                        textDecoration: 'underline'
                                                    }}
                                                >
                                                    {isRemoving[item.id] ? 'Removing...' : 'Remove'}
                                                </button>
                                            </div>

                                            {/* Item Total */}
                                            <p style={{ fontWeight: '600', color: '#1f2937' }}>
                                                Total: {formatPrice(item.total)}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Clear Cart Button */}
                            <div style={{ marginTop: '1rem', textAlign: 'right' }}>
                                <button
                                    onClick={clearCart}
                                    style={{
                                        color: '#dc2626',
                                        fontSize: '0.875rem',
                                        backgroundColor: 'transparent',
                                        border: '1px solid #dc2626',
                                        borderRadius: '0.375rem',
                                        padding: '0.5rem 1rem',
                                        cursor: 'pointer'
                                    }}
                                >
                                    Clear Cart
                                </button>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div>
                            <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '1.5rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Order Summary
                                </h2>

                                <div style={{ marginBottom: '1rem', paddingBottom: '1rem', borderBottom: '1px solid #f3f4f6' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                        <span style={{ color: '#6b7280' }}>Subtotal ({itemCount} items)</span>
                                        <span style={{ color: '#1f2937' }}>{formatPrice(total)}</span>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                        <span style={{ color: '#6b7280' }}>Shipping</span>
                                        <span style={{ color: '#16a34a' }}>Free</span>
                                    </div>
                                </div>

                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '1.5rem' }}>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>Total</span>
                                    <span style={{ fontSize: '1.125rem', fontWeight: '600', color: '#1f2937' }}>{formatPrice(total)}</span>
                                </div>

                                <Link
                                    href="/checkout"
                                    style={{
                                        display: 'block',
                                        width: '100%',
                                        padding: '0.75rem 1rem',
                                        fontSize: '1rem',
                                        backgroundColor: '#2563eb',
                                        color: 'white',
                                        border: 'none',
                                        borderRadius: '0.5rem',
                                        cursor: 'pointer',
                                        textDecoration: 'none',
                                        textAlign: 'center',
                                        fontWeight: '500'
                                    }}
                                >
                                    Proceed to Checkout
                                </Link>

                                <Link
                                    href="/"
                                    style={{
                                        display: 'block',
                                        textAlign: 'center',
                                        marginTop: '1rem',
                                        color: '#6b7280',
                                        textDecoration: 'none',
                                        fontSize: '0.875rem'
                                    }}
                                >
                                    Continue Shopping
                                </Link>
                            </div>
                        </div>
                    </div>
                ) : (
                    /* Empty Cart */
                    <div style={{ textAlign: 'center', padding: '3rem 0' }}>
                        <div style={{ fontSize: '4rem', marginBottom: '1rem' }}>🛒</div>
                        <h2 style={{ fontSize: '1.5rem', fontWeight: '600', color: '#1f2937', marginBottom: '0.5rem' }}>
                            Your cart is empty
                        </h2>
                        <p style={{ color: '#6b7280', marginBottom: '2rem' }}>
                            Looks like you haven't added anything to your cart yet.
                        </p>
                        <Link
                            href="/"
                            style={{
                                display: 'inline-block',
                                backgroundColor: '#2563eb',
                                color: 'white',
                                padding: '0.75rem 1.5rem',
                                borderRadius: '0.5rem',
                                textDecoration: 'none',
                                fontWeight: '500'
                            }}
                        >
                            Start Shopping
                        </Link>
                    </div>
                )}
            </div>
        </Layout>
    );
}