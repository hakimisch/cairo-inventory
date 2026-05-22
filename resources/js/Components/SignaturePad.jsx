import React, { useRef, useState, useEffect, useCallback } from 'react';

const COLORS = {
    maroon: '#5C001F',
    gold: '#F8A617',
    darkGold: '#D49112',
    lightBg: '#FFF8F0',
    border: '#D0A070',
    text: '#3D0014',
    muted: '#8B5A3C',
};

const styles = {
    wrapper: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: '8px',
    },
    label: {
        fontSize: '13px',
        fontWeight: 600,
        color: COLORS.maroon,
        marginBottom: '2px',
    },
    canvas: {
        border: `1.5px solid ${COLORS.border}`,
        borderRadius: '6px',
        backgroundColor: COLORS.lightBg,
        cursor: 'crosshair',
        display: 'block',
    },
    buttonRow: {
        display: 'flex',
        gap: '10px',
        marginTop: '6px',
    },
    btn: {
        padding: '6px 18px',
        fontSize: '12px',
        fontWeight: 600,
        border: 'none',
        borderRadius: '4px',
        cursor: 'pointer',
        transition: 'opacity 0.2s',
    },
    btnClear: {
        backgroundColor: '#f1f1f1',
        color: COLORS.text,
        border: `1px solid ${COLORS.border}`,
    },
    btnSave: {
        backgroundColor: COLORS.maroon,
        color: '#fff',
    },
    btnDisabled: {
        opacity: 0.5,
        cursor: 'not-allowed',
    },
};

export default function SignaturePad({
    onSave,
    width = 400,
    height = 150,
    label = 'Tandatangan',
    initialData = null,
}) {
    const canvasRef = useRef(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [hasDrawn, setHasDrawn] = useState(false);
    const [hasInitial, setHasInitial] = useState(false);

    // ── Load initial signature ──────────────────────────────────
    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = COLORS.lightBg;
        ctx.fillRect(0, 0, width, height);

        if (initialData) {
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(img, 0, 0, width, height);
                setHasInitial(true);
            };
            img.src = initialData;
        } else {
            setHasInitial(false);
        }
    }, [initialData, width, height]);

    // ── Coordinates ─────────────────────────────────────────────
    const getPos = useCallback(
        (e) => {
            const canvas = canvasRef.current;
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (width / rect.width),
                y: (clientY - rect.top) * (height / rect.height),
            };
        },
        [width, height],
    );

    // ── Drawing handlers ────────────────────────────────────────
    const startDraw = useCallback(
        (e) => {
            e.preventDefault();
            const canvas = canvasRef.current;
            const ctx = canvas.getContext('2d');
            const pos = getPos(e);

            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            setIsDrawing(true);
            setHasDrawn(true);
            setHasInitial(false);
        },
        [getPos],
    );

    const draw = useCallback(
        (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const canvas = canvasRef.current;
            const ctx = canvas.getContext('2d');
            const pos = getPos(e);

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = COLORS.text;
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        },
        [isDrawing, getPos],
    );

    const endDraw = useCallback(() => {
        setIsDrawing(false);
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        ctx.beginPath();
    }, []);

    // ── Clear ───────────────────────────────────────────────────
    const handleClear = useCallback(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = COLORS.lightBg;
        ctx.fillRect(0, 0, width, height);
        setHasDrawn(false);
        setHasInitial(false);
    }, [width, height]);

    // ── Save ────────────────────────────────────────────────────
    const handleSave = useCallback(() => {
        if (!hasDrawn && !hasInitial) return;
        const canvas = canvasRef.current;
        const dataUrl = canvas.toDataURL('image/png');
        if (onSave) onSave(dataUrl);
    }, [hasDrawn, hasInitial, onSave]);

    return (
        <div style={styles.wrapper}>
            {label && <div style={styles.label}>{label}</div>}

            <canvas
                ref={canvasRef}
                width={width}
                height={height}
                style={styles.canvas}
                onMouseDown={startDraw}
                onMouseMove={draw}
                onMouseUp={endDraw}
                onMouseLeave={endDraw}
                onTouchStart={startDraw}
                onTouchMove={draw}
                onTouchEnd={endDraw}
            />

            <div style={styles.buttonRow}>
                <button
                    type="button"
                    style={{ ...styles.btn, ...styles.btnClear }}
                    onClick={handleClear}
                >
                    Clear
                </button>
                <button
                    type="button"
                    style={{
                        ...styles.btn,
                        ...styles.btnSave,
                        ...(!hasDrawn && !hasInitial ? styles.btnDisabled : {}),
                    }}
                    disabled={!hasDrawn && !hasInitial}
                    onClick={handleSave}
                >
                    Save
                </button>
            </div>
        </div>
    );
}
