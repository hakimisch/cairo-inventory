import React from 'react';

const typeStyles = {
    info: {
        background: '#eff6ff',
        border: '1px solid #bfdbfe',
        color: '#1e40af',
        icon: 'ℹ️',
    },
    success: {
        background: '#f0fdf4',
        border: '1px solid #bbf7d0',
        color: '#166534',
        icon: '✅',
    },
    warning: {
        background: '#fffbeb',
        border: '1px solid #fde68a',
        color: '#92400e',
        icon: '⚠️',
    },
    error: {
        background: '#fef2f2',
        border: '1px solid #fecaca',
        color: '#991b1b',
        icon: '❌',
    },
};

const styles = {
    banner: {
        display: 'flex',
        alignItems: 'flex-start',
        gap: '10px',
        padding: '12px 16px',
        borderRadius: '8px',
        marginBottom: '16px',
        fontSize: '14px',
        lineHeight: 1.5,
    },
    icon: {
        fontSize: '16px',
        flexShrink: 0,
        marginTop: '1px',
    },
    content: {
        flex: 1,
    },
    title: {
        fontWeight: 600,
        marginBottom: '2px',
    },
    dismiss: {
        background: 'none',
        border: 'none',
        cursor: 'pointer',
        fontSize: '16px',
        padding: '0 0 0 8px',
        opacity: 0.5,
        flexShrink: 0,
    },
};

export default function AlertBanner({ type = 'info', title, message, onDismiss, children }) {
    const config = typeStyles[type] || typeStyles.info;

    return (
        <div style={{ ...styles.banner, background: config.background, border: config.border, color: config.color }}>
            <span style={styles.icon}>{config.icon}</span>
            <div style={styles.content}>
                {title && <div style={styles.title}>{title}</div>}
                {message && <div>{message}</div>}
                {children}
            </div>
            {onDismiss && (
                <button style={styles.dismiss} onClick={onDismiss} aria-label="Dismiss">
                    ✕
                </button>
            )}
        </div>
    );
}
