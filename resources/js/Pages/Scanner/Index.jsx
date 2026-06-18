import { useState, useRef, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const RESULT_STYLES = {
    match:     { color: '#166534', bg: '#F0FDF4', icon: '✅', label: 'MATCH' },
    mismatch:  { color: '#DC2626', bg: '#FEF2F2', icon: '❌', label: 'MISMATCH' },
    duplicate: { color: '#92400E', bg: '#FFFBEB', icon: '⚠', label: 'DUPLICATE' },
    new:       { color: '#1E40AF', bg: '#EFF6FF', icon: '🔵', label: 'NEW' },
};

export default function Index({ recentScans }) {
    const [mode, setMode] = useState('camera'); // 'camera' | 'manual'
    const [scanResult, setScanResult] = useState(null);
    const [manualSn, setManualSn] = useState('');
    const [isScanning, setIsScanning] = useState(true);
    const [cameraError, setCameraError] = useState(null);
    const [scannerKey, setScannerKey] = useState(0);
    const videoRef = useRef(null);
    const streamRef = useRef(null);

    // Dynamically import the scanner component
    const [Scanner, setScanner] = useState(null);
    useEffect(() => {
        import('@yudiel/react-qr-scanner').then(mod => {
            setScanner(() => mod.Scanner);
        }).catch(() => {
            setCameraError('Failed to load scanner library.');
        });
    }, []);

    async function handleScan(serialNumber) {
        if (!serialNumber || !isScanning) return;
        setIsScanning(false);

        try {
            // 1. Look up the serial number
            const lookupRes = await fetch(route('scanner.lookup'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({ serial_number: serialNumber }),
            });
            const lookupData = await lookupRes.json();

            // 2. Determine result
            let result = 'mismatch';
            let matchedItem = null;
            if (lookupData.match_count > 0) {
                const pendingMatches = lookupData.matches.filter(m => m.status === 'pending');
                const receivedMatches = lookupData.matches.filter(m => m.status === 'received');
                if (pendingMatches.length > 0) {
                    result = 'match';
                    matchedItem = pendingMatches[0];
                } else if (receivedMatches.length > 0) {
                    result = 'duplicate';
                    matchedItem = receivedMatches[0];
                } else {
                    result = 'match';
                    matchedItem = lookupData.matches[0];
                }
            } else if (lookupData.existing_asset) {
                result = 'duplicate';
            }

            // 3. Record the scan
            const scanRes = await fetch(route('scanner.scan'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({
                    serial_number: serialNumber,
                    result: result,
                    do_line_item_id: matchedItem?.id || null,
                    asset_id: lookupData.existing_asset?.id || null,
                }),
            });
            const scanData = await scanRes.json();

            // 4. Show result
            setScanResult({
                ...scanData.scan,
                match: matchedItem,
                existingAsset: lookupData.existing_asset,
                message: scanData.message,
            });
        } catch (err) {
            setScanResult({
                serial_number: serialNumber,
                result: 'mismatch',
                message: 'Error during lookup. Try again.',
                error: true,
            });
        }

        // Re-enable scanning after 3 seconds
        setTimeout(() => {
            setIsScanning(true);
            setScannerKey(k => k + 1);
        }, 3000);
    }

    function handleManualSubmit(e) {
        e.preventDefault();
        if (!manualSn.trim()) return;
        handleScan(manualSn.trim());
        setManualSn('');
    }

    function handleScanResult(result) {
        if (!isScanning) return;
        const code = result?.[0]?.rawValue || result?.rawValue || '';
        if (code) {
            handleScan(code);
        }
    }

    function handleScanError(err) {
        console.error('Scanner error:', err);
        setCameraError('Camera access error. Try manual entry.');
        setIsScanning(false);
    }

    function retryCamera() {
        setCameraError(null);
        setIsScanning(true);
        setScannerKey(k => k + 1);
    }

    return (
        <AuthenticatedLayout>
            <Head title="Serial Number Scanner" />
            <div style={{ padding: 24, maxWidth: 800, margin: '0 auto' }}>
                {/* ── Header ──────────────────────────────────────────────── */}
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
                    <div>
                        <h1 style={{ fontSize: 22, fontWeight: 700, margin: 0 }}>◈ Serial Number Scanner</h1>
                        <p style={{ color: '#6B7280', fontSize: 13, margin: '4px 0 0' }}>
                            Scan barcode/QR or enter serial number manually to verify against Delivery Orders
                        </p>
                    </div>
                    <Link
                        href={route('scanner.history')}
                        style={{
                            padding: '8px 16px', backgroundColor: '#fff', color: '#374151',
                            border: '1px solid #D1D5DB', borderRadius: 6, fontSize: 13, textDecoration: 'none',
                        }}
                    >
                        Scan History →
                    </Link>
                </div>

                {/* ── Mode Toggle ──────────────────────────────────────────── */}
                <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                    <button onClick={() => setMode('camera')}
                        style={{
                            padding: '8px 16px', borderRadius: 6, fontSize: 13, fontWeight: 600,
                            border: mode === 'camera' ? '2px solid #5C001F' : '1px solid #D1D5DB',
                            backgroundColor: mode === 'camera' ? '#FDF2F4' : '#fff',
                            color: mode === 'camera' ? '#5C001F' : '#374151',
                            cursor: 'pointer',
                        }}>
                        ◉ Camera Scanner
                    </button>
                    <button onClick={() => setMode('manual')}
                        style={{
                            padding: '8px 16px', borderRadius: 6, fontSize: 13, fontWeight: 600,
                            border: mode === 'manual' ? '2px solid #5C001F' : '1px solid #D1D5DB',
                            backgroundColor: mode === 'manual' ? '#FDF2F4' : '#fff',
                            color: mode === 'manual' ? '#5C001F' : '#374151',
                            cursor: 'pointer',
                        }}>
                        ⌨ Manual Entry
                    </button>
                </div>

                {/* ── Scanner / Manual Input ────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 20 }}>
                    {mode === 'camera' ? (
                        <div>
                            <div style={{
                                width: '100%', maxWidth: 400, height: 300, margin: '0 auto',
                                backgroundColor: '#111827', borderRadius: 12, overflow: 'hidden',
                                position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center',
                            }}>
                                {cameraError ? (
                                    <div style={{ textAlign: 'center', color: '#fff', padding: 20 }}>
                                        <p style={{ fontSize: 14, margin: '0 0 12px' }}>{cameraError}</p>
                                        <button onClick={retryCamera}
                                            style={{
                                                padding: '8px 20px', backgroundColor: '#5C001F', color: '#fff',
                                                border: 'none', borderRadius: 6, fontSize: 13, cursor: 'pointer',
                                            }}>
                                            Retry Camera
                                        </button>
                                    </div>
                                ) : !Scanner ? (
                                    <div style={{ textAlign: 'center', color: '#fff' }}>
                                        <div style={{ fontSize: 24, marginBottom: 8 }}>⟳</div>
                                        <p style={{ fontSize: 13, margin: 0 }}>Loading camera…</p>
                                    </div>
                                ) : (
                                    <Scanner
                                        key={scannerKey}
                                        onResult={handleScanResult}
                                        onError={handleScanError}
                                        constraints={{
                                            facingMode: 'environment',
                                            width: { ideal: 400 },
                                            height: { ideal: 300 },
                                        }}
                                        formats={[
                                            'qr_code', 'micro_qr_code',
                                            'code_128', 'code_39', 'code_93',
                                            'ean_13', 'ean_8', 'upc_a', 'upc_e',
                                            'data_matrix', 'pdf417',
                                            'aztec',
                                        ]}
                                        styles={{
                                            container: { width: '100%', height: '100%' },
                                            video: { objectFit: 'cover' },
                                        }}
                                    />
                                )}
                            </div>

                            {/* Scanning status */}
                            <div style={{ textAlign: 'center', marginTop: 12 }}>
                                <span style={{
                                    display: 'inline-block', width: 8, height: 8, borderRadius: '50%',
                                    backgroundColor: isScanning ? '#166534' : '#9CA3AF',
                                    marginRight: 6, verticalAlign: 'middle',
                                }} />
                                <span style={{ fontSize: 13, color: '#6B7280' }}>
                                    {isScanning ? 'Waiting for barcode…' : 'Processing scan…'}
                                </span>
                            </div>
                        </div>
                    ) : (
                        <form onSubmit={handleManualSubmit} style={{ maxWidth: 500, margin: '0 auto' }}>
                            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: '#374151', marginBottom: 4 }}>
                                Enter Serial Number
                            </label>
                            <div style={{ display: 'flex', gap: 8 }}>
                                <input
                                    type="text"
                                    value={manualSn}
                                    onChange={e => setManualSn(e.target.value)}
                                    placeholder="Type or paste serial number…"
                                    autoFocus
                                    style={{
                                        flex: 1, padding: '10px 14px', borderRadius: 8, border: '1px solid #D1D5DB',
                                        fontSize: 14, fontFamily: 'monospace',
                                    }}
                                />
                                <button type="submit" disabled={!manualSn.trim() || !isScanning}
                                    style={{
                                        padding: '10px 20px', backgroundColor: '#5C001F', color: '#fff',
                                        border: 'none', borderRadius: 8, fontSize: 13, fontWeight: 600,
                                        cursor: manualSn.trim() && isScanning ? 'pointer' : 'not-allowed',
                                        opacity: manualSn.trim() && isScanning ? 1 : 0.5,
                                    }}>
                                    Look Up
                                </button>
                            </div>
                        </form>
                    )}
                </div>

                {/* ── Scan Result ──────────────────────────────────────────── */}
                {scanResult && (
                    <div style={{
                        backgroundColor: RESULT_STYLES[scanResult.result]?.bg || '#F9FAFB',
                        borderRadius: 12, border: `1px solid ${RESULT_STYLES[scanResult.result]?.color || '#E5E7EB'}`,
                        padding: 20, marginBottom: 20,
                    }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 12 }}>
                            <span style={{ fontSize: 24 }}>
                                {RESULT_STYLES[scanResult.result]?.icon || '◈'}
                            </span>
                            <div>
                                <span style={{
                                    display: 'inline-block', padding: '2px 10px', borderRadius: 20,
                                    fontSize: 11, fontWeight: 700,
                                    color: RESULT_STYLES[scanResult.result]?.color,
                                    backgroundColor: RESULT_STYLES[scanResult.result]?.bg,
                                    border: `1px solid ${RESULT_STYLES[scanResult.result]?.color}`,
                                }}>
                                    {RESULT_STYLES[scanResult.result]?.label || 'UNKNOWN'}
                                </span>
                                <p style={{ fontSize: 14, fontFamily: 'monospace', fontWeight: 600, margin: '6px 0 0', color: '#111827' }}>
                                    {scanResult.serial_number}
                                </p>
                            </div>
                        </div>

                        <p style={{ fontSize: 13, color: '#374151', margin: '0 0 12px' }}>{scanResult.message}</p>

                        {scanResult.match && (
                            <div style={{
                                backgroundColor: '#fff', borderRadius: 8, padding: 12,
                                border: '1px solid #E5E7EB', fontSize: 13,
                            }}>
                                <p style={{ fontWeight: 600, margin: '0 0 4px', color: '#374151' }}>Matched to:</p>
                                <p style={{ margin: 0, color: '#5C001F' }}>
                                    <Link href={route('delivery-orders.show', scanResult.match.do_id)}
                                          style={{ color: '#5C001F', textDecoration: 'none' }}>
                                        {scanResult.match.do_no}
                                    </Link>
                                    {' '}— {scanResult.match.description}
                                </p>
                                <p style={{ margin: '4px 0 0', fontSize: 12, color: '#6B7280' }}>
                                    {scanResult.match.brand && `${scanResult.match.brand} `}
                                    {scanResult.match.model && `${scanResult.match.model} · `}
                                    {scanResult.match.category}
                                </p>
                            </div>
                        )}

                        {scanResult.existingAsset && (
                            <div style={{
                                backgroundColor: '#fff', borderRadius: 8, padding: 12, marginTop: 8,
                                border: '1px solid #E5E7EB', fontSize: 13,
                            }}>
                                <p style={{ fontWeight: 600, margin: '0 0 4px', color: '#374151' }}>Already exists as Asset:</p>
                                <p style={{ margin: 0 }}>
                                    #{scanResult.existingAsset.asset_tag} — {scanResult.existingAsset.name}
                                    {' '}({scanResult.existingAsset.status})
                                </p>
                            </div>
                        )}

                        {scanResult.result === 'match' && scanResult.match && (
                            <button onClick={async () => {
                                try {
                                    const res = await fetch(route('scanner.scan'), {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                                        body: JSON.stringify({
                                            serial_number: scanResult.serial_number,
                                            result: 'match',
                                            do_line_item_id: scanResult.match.id,
                                            create_asset: true,
                                        }),
                                    });
                                    const data = await res.json();
                                    setScanResult(prev => ({ ...prev, assetCreated: true, assetTag: data?.asset_tag }));
                                } catch (e) {
                                    setScanResult(prev => ({ ...prev, assetError: 'Failed to create asset.' }));
                                }
                            }}
                                style={{
                                    marginTop: 8, padding: '8px 16px', backgroundColor: '#059669', color: '#fff',
                                    border: 'none', borderRadius: 6, fontSize: 12, fontWeight: 600, cursor: 'pointer',
                                    display: 'block',
                                }}>
                                {scanResult.assetCreated ? '✅ Asset Created' : 'Create Asset from this item'}
                            </button>
                        )}
                        {scanResult.assetCreated && (
                            <p style={{ margin: '6px 0 0', fontSize: 12, color: '#166534' }}>
                                Asset tag: {scanResult.assetTag}
                            </p>
                        )}
                        {scanResult.assetError && (
                            <p style={{ margin: '6px 0 0', fontSize: 12, color: '#DC2626' }}>
                                {scanResult.assetError}
                            </p>
                        )}

                        {isScanning && (
                            <button onClick={() => { setScanResult(null); setScannerKey(k => k + 1); }}
                                style={{
                                    marginTop: 12, padding: '8px 16px', backgroundColor: '#5C001F', color: '#fff',
                                    border: 'none', borderRadius: 6, fontSize: 12, fontWeight: 600, cursor: 'pointer',
                                }}>
                                Scan Next
                            </button>
                        )}
                    </div>
                )}

                {/* ── Recent Scans ─────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden' }}>
                    <div style={{ padding: '12px 20px', borderBottom: '1px solid #E5E7EB' }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: 0, color: '#374151' }}>Recent Scans</h2>
                    </div>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                        <thead>
                            <tr style={{ backgroundColor: '#F9FAFB' }}>
                                <th style={thStyle}>Serial No.</th>
                                <th style={thStyle}>Result</th>
                                <th style={thStyle}>Scanned By</th>
                                <th style={thStyle}>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {recentScans.length === 0 && (
                                <tr>
                                    <td colSpan={4} style={{ textAlign: 'center', padding: 30, color: '#9CA3AF' }}>
                                        No scans yet. Point your camera at a barcode or enter a serial number.
                                    </td>
                                </tr>
                            )}
                            {recentScans.map(s => {
                                const st = RESULT_STYLES[s.result] || RESULT_STYLES.mismatch;
                                return (
                                    <tr key={s.id} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                        <td style={{ ...tdStyle, fontFamily: 'monospace' }}>{s.serial_number}</td>
                                        <td style={tdStyle}>
                                            <span style={{
                                                display: 'inline-flex', alignItems: 'center', gap: 3,
                                                padding: '2px 8px', borderRadius: 20, fontSize: 11, fontWeight: 600,
                                                color: st.color, backgroundColor: st.bg,
                                            }}>
                                                {st.icon} {st.label}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>{s.scanner_name}</td>
                                        <td style={{ ...tdStyle, color: '#6B7280' }}>
                                            {new Date(s.scanned_at).toLocaleTimeString()}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '8px 14px',
    textAlign: 'left',
    fontWeight: 600,
    fontSize: 11,
    color: '#6B7280',
    textTransform: 'uppercase',
    letterSpacing: '0.05em',
};
const tdStyle = {
    padding: '8px 14px',
    verticalAlign: 'middle',
};
