import React, { useState, useEffect, useCallback } from 'react';
import { createPortal } from 'react-dom';

const typeStyles = {
    success: { background: '#166534', icon: '✅' },
    error:   { background: '#991b1b', icon: '❌' },
    warning: { background: '#92400e', icon: '⚠️' },
    info:    { background: '#1e40af', icon: 'ℹ️' },
};

const styles = {
    container: {
        position: 'fixed',
        top: '20px',
        right: '20px',
        zIndex: 9999,
        display: 'flex',
        flexDirection: 'column',
        gap: '8px',
        maxWidth: '400px',
    },
    toast: {
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        padding: '12px 16px',
        borderRadius: '8px',
        color: '#ffffff',
        fontSize: '14px',
        lineHeight: 1.4,
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        animation: 'slideIn 0.3s ease-out',
        cursor: 'pointer',
    },
    icon: {
        fontSize: '18px',
        flexShrink: 0,
    },
    content: {
        flex: 1,
    },
    title: {
        fontWeight: 600,
        marginBottom: '2px',
    },
    closeBtn: {
        background: 'none',
        border: 'none',
        color: '#ffffff',
        cursor: 'pointer',
        fontSize: '16px',
        padding: '0',
        opacity: 0.7,
        flexShrink: 0,
    },
};

// Toast context for global access
const ToastContext = React.createContext();

let toastId = 0;

export function ToastProvider({ children }) {
    const [toasts, setToasts] = useState([]);

    const addToast = useCallback(({ type = 'info', title, message, duration = 4000 }) => {
        const id = ++toastId;
        setToasts((prev) => [...prev, { id, type, title, message }]);

        if (duration > 0) {
            setTimeout(() => {
                setToasts((prev) => prev.filter((t) => t.id !== id));
            }, duration);
        }

        return id;
    }, []);

    const removeToast = useCallback((id) => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
    }, []);

    return (
        <ToastContext.Provider value={addToast}>
            {children}
            {typeof document !== 'undefined' && createPortal(
                <div style={styles.container}>
                    {toasts.map((toast) => (
                        <ToastItem key={toast.id} toast={toast} onClose={() => removeToast(toast.id)} />
                    ))}
                </div>,
                document.body
            )}
        </ToastContext.Provider>
    );
}

function ToastItem({ toast, onClose }) {
    const config = typeStyles[toast.type] || typeStyles.info;

    useEffect(() => {
        // Inject animation keyframes
        if (!document.getElementById('toast-anim')) {
            const style = document.createElement('style');
            style.id = 'toast-anim';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to   { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
    }, []);

    return (
        <div
            style={{ ...styles.toast, background: config.background }}
            onClick={onClose}
            role="alert"
        >
            <span style={styles.icon}>{config.icon}</span>
            <div style={styles.content}>
                {toast.title && <div style={styles.title}>{toast.title}</div>}
                {toast.message && <div>{toast.message}</div>}
            </div>
            <button style={styles.closeBtn} onClick={(e) => { e.stopPropagation(); onClose(); }} aria-label="Close">
                ✕
            </button>
        </div>
    );
}

// Hook to use toast
export function useToast() {
    const context = React.useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within a ToastProvider');
    }
    return context;
}
