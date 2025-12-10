import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Layout from '../Components/Layout';
import Toast from '../Components/Toast';

export default function Cart({ cartItems, total, itemCount }) {
    const [isUpdating, setIsUpdating] = useState({});
    const [isRemoving, setIsRemoving] = useState({});
    const [toast, setToast] = useState(null);
    const [showClearDialog, setShowClearDialog] = useState(false);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(price);
    };

    const IVA_RATE = 0.21;

    const updateQuantity = (itemId, newQuantity) => {
        if (newQuantity < 1) return;

        setIsUpdating(prev => ({ ...prev, [itemId]: true }));

        router.patch(`/cart/${itemId}`,
            { quantity: newQuantity },
            {
                onSuccess: () => {
                    // Los datos se actualizan automáticamente con Inertia
                    window.dispatchEvent(new Event('cartUpdated'));
                },
                onError: (error) => {
                    console.error('Error:', error);
                    setToast({
                        message: 'Error al actualizar la cantidad',
                        type: 'error'
                    });
                },
                onFinish: () => {
                    setIsUpdating(prev => ({ ...prev, [itemId]: false }));
                }
            }
        );
    };

    const removeItem = (itemId) => {
        setIsRemoving(prev => ({ ...prev, [itemId]: true }));

        router.delete(`/cart/${itemId}`,
            {
                onSuccess: () => {
                    setToast({
                        message: 'Producto eliminado del carrito',
                        type: 'success'
                    });
                    window.dispatchEvent(new Event('cartUpdated'));
                },
                onError: (error) => {
                    console.error('Error:', error);
                    setToast({
                        message: 'Error al eliminar el producto',
                        type: 'error'
                    });
                },
                onFinish: () => {
                    setIsRemoving(prev => ({ ...prev, [itemId]: false }));
                }
            }
        );
    };

    const clearCart = () => {
        setShowClearDialog(true);
    };

    const confirmClearCart = () => {
        router.delete('/cart',
            {
                onSuccess: () => {
                    setToast({
                        message: 'Carrito vaciado correctamente',
                        type: 'success'
                    });
                    setShowClearDialog(false);
                    window.dispatchEvent(new Event('cartUpdated'));
                },
                onError: (error) => {
                    console.error('Error:', error);
                    setToast({
                        message: 'Error al vaciar el carrito',
                        type: 'error'
                    });
                    setShowClearDialog(false);
                }
            }
        );
    };

    return (
        <Layout>
            <div style={{ maxWidth: '80rem', margin: '0 auto', padding: '2rem 1rem' }}>
                {/* Header */}
                <div style={{ marginBottom: '2rem' }}>
                    <h1 style={{ fontSize: '2rem', fontWeight: 'bold', color: '#1f2937', marginBottom: '0.5rem' }}>
                        Carrito de Compras
                    </h1>
                    <p style={{ color: '#6b7280' }}>
                        {itemCount} {itemCount === 1 ? 'artículo' : 'artículos'} en tu carrito
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
                                                {formatPrice(item.price)} c/u
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
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '0.5rem',
                                                        color: '#dc2626',
                                                        fontSize: '0.875rem',
                                                        backgroundColor: '#fee2e2',
                                                        border: '1px solid #fecaca',
                                                        borderRadius: '0.375rem',
                                                        padding: '0.5rem 0.75rem',
                                                        cursor: 'pointer',
                                                        transition: 'all 0.2s ease',
                                                        fontWeight: '500'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.backgroundColor = '#fecaca';
                                                        e.target.style.borderColor = '#f87171';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.backgroundColor = '#fee2e2';
                                                        e.target.style.borderColor = '#fecaca';
                                                    }}
                                                >
                                                    <svg
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        strokeWidth="2"
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                    >
                                                        <polyline points="3,6 5,6 21,6"></polyline>
                                                        <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1 2-2h4a2,2 0 0,1 2,2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                    {isRemoving[item.id] ? 'Eliminando...' : 'Eliminar'}
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
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '0.5rem',
                                        color: '#dc2626',
                                        fontSize: '0.875rem',
                                        backgroundColor: '#fee2e2',
                                        border: '1px solid #fecaca',
                                        borderRadius: '0.375rem',
                                        padding: '0.5rem 1rem',
                                        cursor: 'pointer',
                                        transition: 'all 0.2s ease',
                                        fontWeight: '500'
                                    }}
                                    onMouseEnter={(e) => {
                                        e.target.style.backgroundColor = '#fecaca';
                                        e.target.style.borderColor = '#f87171';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.target.style.backgroundColor = '#fee2e2';
                                        e.target.style.borderColor = '#fecaca';
                                    }}
                                >
                                    <svg
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        strokeWidth="2"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                    >
                                        <polyline points="3,6 5,6 21,6"></polyline>
                                        <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1 2-2h4a2,2 0 0,1 2,2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Vaciar Carrito
                                </button>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div>
                            <div style={{ backgroundColor: 'white', borderRadius: '0.5rem', border: '1px solid #e5e7eb', padding: '1.5rem' }}>
                                <h2 style={{ fontSize: '1.25rem', fontWeight: '600', color: '#1f2937', marginBottom: '1rem' }}>
                                    Resumen del Pedido
                                </h2>

                                <div style={{ marginBottom: '1rem', paddingBottom: '1rem', borderBottom: '1px solid #f3f4f6' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                        <span style={{ color: '#6b7280' }}>Subtotal ({itemCount} {itemCount === 1 ? 'artículo' : 'artículos'})</span>
                                        <span style={{ color: '#1f2937' }}>{formatPrice(total)}</span>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                        <span style={{ color: '#6b7280', fontSize: '0.875rem' }}>Sin IVA: {formatPrice(total / (1 + IVA_RATE))}</span>
                                        <span style={{ color: '#9ca3af', fontSize: '0.75rem' }}>(IVA 21% incluido)</span>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
                                        <span style={{ color: '#6b7280' }}>Envío</span>
                                        <span style={{ color: '#16a34a' }}>Gratis</span>
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
                                    Proceder al Pago
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
                                    Continuar Comprando
                                </Link>
                            </div>
                        </div>
                    </div>
                ) : (
                    /* Empty Cart */
                    <div style={{ textAlign: 'center', padding: '3rem 0' }}>
                        <div style={{ fontSize: '4rem', marginBottom: '1rem' }}>🛒</div>
                        <h2 style={{ fontSize: '1.5rem', fontWeight: '600', color: '#1f2937', marginBottom: '0.5rem' }}>
                            Tu carrito está vacío
                        </h2>
                        <p style={{ color: '#6b7280', marginBottom: '2rem' }}>
                            Parece que aún no has agregado nada a tu carrito.
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
                            Comenzar a Comprar
                        </Link>
                    </div>
                )}

            {/* Toast Notification */}
            {toast && (
                <Toast
                    message={toast.message}
                    type={toast.type}
                    onClose={() => setToast(null)}
                />
            )}

            {/* Clear Cart Confirmation Modal */}
            {showClearDialog && (
                <div style={{
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    zIndex: 50,
                    padding: '1rem'
                }}>
                    <div style={{
                        backgroundColor: 'white',
                        borderRadius: '1rem',
                        padding: '2rem',
                        maxWidth: '400px',
                        width: '100%',
                        boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                        animation: 'slideIn 0.3s ease-out'
                    }}>
                        {/* Icon */}
                        <div style={{
                            display: 'flex',
                            justifyContent: 'center',
                            marginBottom: '1rem'
                        }}>
                            <div style={{
                                backgroundColor: '#fee2e2',
                                borderRadius: '50%',
                                padding: '1rem',
                                width: '4rem',
                                height: '4rem',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                <svg
                                    width="32"
                                    height="32"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#dc2626"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                >
                                    <path d="M3 6h18l-2 13H5L3 6z"></path>
                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                        </div>

                        {/* Title */}
                        <h3 style={{
                            fontSize: '1.5rem',
                            fontWeight: '600',
                            color: '#1f2937',
                            textAlign: 'center',
                            marginBottom: '0.5rem'
                        }}>
                            Vaciar Carrito
                        </h3>

                        {/* Message */}
                        <p style={{
                            color: '#6b7280',
                            textAlign: 'center',
                            marginBottom: '2rem',
                            lineHeight: '1.5'
                        }}>
                            ¿Estás seguro de que quieres eliminar todos los productos de tu carrito? Esta acción no se puede deshacer.
                        </p>

                        {/* Buttons */}
                        <div style={{
                            display: 'flex',
                            gap: '0.75rem',
                            flexDirection: 'column'
                        }}>
                            <button
                                onClick={confirmClearCart}
                                style={{
                                    backgroundColor: '#dc2626',
                                    color: 'white',
                                    border: 'none',
                                    borderRadius: '0.5rem',
                                    padding: '0.75rem 1rem',
                                    fontSize: '1rem',
                                    fontWeight: '500',
                                    cursor: 'pointer',
                                    transition: 'background-color 0.2s ease'
                                }}
                                onMouseEnter={(e) => {
                                    e.target.style.backgroundColor = '#b91c1c';
                                }}
                                onMouseLeave={(e) => {
                                    e.target.style.backgroundColor = '#dc2626';
                                }}
                            >
                                Sí, vaciar carrito
                            </button>
                            <button
                                onClick={() => setShowClearDialog(false)}
                                style={{
                                    backgroundColor: '#f3f4f6',
                                    color: '#374151',
                                    border: 'none',
                                    borderRadius: '0.5rem',
                                    padding: '0.75rem 1rem',
                                    fontSize: '1rem',
                                    fontWeight: '500',
                                    cursor: 'pointer',
                                    transition: 'background-color 0.2s ease'
                                }}
                                onMouseEnter={(e) => {
                                    e.target.style.backgroundColor = '#e5e7eb';
                                }}
                                onMouseLeave={(e) => {
                                    e.target.style.backgroundColor = '#f3f4f6';
                                }}
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            )}
            </div>

            <style>{`
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: scale(0.9) translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }
            `}</style>
        </Layout>
    );
}