import React from 'react';

const styles = {
    container: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '60px 20px',
        textAlign: 'center',
    },
    icon: {
        fontSize: '48px',
        marginBottom: '16px',
        opacity: 0.6,
    },
    title: {
        fontSize: '18px',
        fontWeight: 600,
        color: '#1e293b',
        marginBottom: '8px',
    },
    message: {
        fontSize: '14px',
        color: '#64748b',
        maxWidth: '400px',
        lineHeight: 1.5,
    },
};

export default function NoDataAvailable({ title, message }) {
    return (
        <div style={styles.container}>
            <div style={styles.icon}>📋</div>
            <div style={styles.title}>{title || 'No Data Available'}</div>
            <div style={styles.message}>
                {message || 'There are currently no records to display. New entries will appear here once they are created.'}
            </div>
        </div>
    );
}
