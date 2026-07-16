import { useState, useRef, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const RESULT_STYLES = {
    match:     { color: '#166534', bg: '#F0FDF4', icon: '◈', label: 'MATCH' },
    mismatch:  { color: '#DC2626', bg: '#FEF2F2', icon: '!', label: 'MISMATCH' },
    duplicate: { color: '#92400E', bg: '#FFFBEB', icon: '◈', label: 'DUPLICATE' },
    new:       { color: '#1E40AF', bg: '#EFF6FF', icon: '◈', label: 'NEW' },
};

export default function Index({ recentScans }) {
    const [mode, setMode] = useState('manual');
    const [scanResult, setScanResult] = useState(null);
    const [manualSn, setManualSn] = useState('');
    const [isScanning, setIsScanning] = useState(true);
    const [cameraError, setCameraError] = useState(null);
    const [scannerKey, setScannerKey] = useState(0);

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
            const lookupRes = await fetch(route('scanner.lookup'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({ serial_number: serialNumber }),
            });
            const lookupData = await lookupRes.json();

            let result = 'mismatch';
            let matchedItem = null;
            let itemStatus = null;

            if (lookupData.match_count > 0) {
                matchedItem = lookupData.matches[0];
                itemStatus = matchedItem.status;

                if (itemStatus === 'belum_diterima') {
                    result = 'match';
                } else if (itemStatus === 'diterima') {
                    result = 'match';
                } else if (itemStatus === 'verified') {
                    result = 'duplicate';
                }
            }

            setScanResult({
                result,
                serialNumber,
                message: lookupData.message || '',
                match: matchedItem,
                itemStatus,
                existingAsset: lookupData.existing_asset || null,
            });
        } catch (e) {
            setScanResult({ result: 'mismatch', serialNumber, message: e.message });
        }
    }

    function handleScanResult(detectedCodes) {
        const code = detectedCodes?.[0]?.rawValue;
        if (code) handleScan(code);
    }

    return (
        <AuthenticatedLayout>
            <Head title="Scanner" />

            <div style={{ maxWidth: 800, margin: '0 auto', padding: '24px 20px' }}>
                {/* Header */}
                <div style={{ marginBottom: 20 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: '#1F2937', margin: 0 }}>
                        Serial Number Scanner
                    </h1>
                    <p style={{ fontSize: 13, color: '#6B7280', marginTop: 4 }}>
                        Scan a barcode or type a serial number to look up an item.
                        Found items can be processed in the Verification Pipeline.
                    </p>
                </div>

                {/* Mode toggle */}
                <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
                    <button onClick={() => setMode('manual')}
                        style={{
                            padding: '8px 18px', borderRadius: 8, fontSize: 13, fontWeight: 600,
                            border: `2px solid ${mode === 'manual' ? '#5C001F' : '#E5E7EB'}`,
                            background: mode === 'manual' ? '#FDF0F3' : '#fff',
                            color: mode === 'manual' ? '#5C001F' : '#6B7280', cursor: 'pointer',
                        }}>
                        Manual Input
                    </button>
                    <button onClick={() => setMode('camera')}
                        style={{
                            padding: '8px 18px', borderRadius: 8, fontSize: 13, fontWeight: 600,
                            border: `2px solid ${mode === 'camera' ? '#5C001F' : '#E5E7EB'}`,
                            background: mode === 'camera' ? '#FDF0F3' : '#fff',
                            color: mode === 'camera' ? '#5C001F' : '#6B7280', cursor: 'pointer',
                        }}>
                        Camera Scan
                    </button>
                </div>

                {/* Manual input */}
                {mode === 'manual' && (
                    <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                        <input
                            type="text"
                            value={manualSn}
                            onChange={e => setManualSn(e.target.value)}
                            onKeyDown={e => e.key === 'Enter' && handleScan(manualSn)}
                            placeholder="Type serial number and press Enter..."
                            style={{
                                flex: 1, padding: '10px 14px', borderRadius: 8,
                                border: '1.5px solid #D1D5DB', fontSize: 14, outline: 'none',
                            }}
                        />
                        <button onClick={() => handleScan(manualSn)}
                            style={{
                                padding: '10px 22px', borderRadius: 8, border: 'none',
                                background: '#5C001F', color: '#fff', fontWeight: 700, fontSize: 14, cursor: 'pointer',
                            }}>
                            Lookup
                        </button>
                    </div>
                )}

                {/* Camera scanner */}
                {mode === 'camera' && (
                    <div style={{ marginBottom: 20 }}>
                        {cameraError ? (
                            <div style={{ padding: 16, background: '#FEF2F2', borderRadius: 8, border: '1px solid #FECACA', fontSize: 13, color: '#DC2626' }}>
                                {cameraError}
                            </div>
                        ) : Scanner ? (
                            <div key={scannerKey} style={{ maxWidth: 400, borderRadius: 8, overflow: 'hidden', border: '2px solid #E5E7EB' }}>
                                <Scanner
                                    onScan={handleScanResult}
                                    onError={e => { if (e?.message) setCameraError(e.message); }}
                                    styles={{ container: { borderRadius: 8 } }}
                                />
                            </div>
                        ) : (
                            <p style={{ fontSize: 13, color: '#6B7280' }}>Loading camera...</p>
                        )}
                        <p style={{ fontSize: 11, color: '#9CA3AF', marginTop: 6 }}>
                            Point your camera at a barcode or QR code containing the serial number.
                        </p>
                    </div>
                )}

                {/* Scan result */}
                {scanResult && (
                    <div style={{ marginBottom: 20 }}>
                        <div style={{
                            padding: 16, borderRadius: 10,
                            background: scanResult.result === 'mismatch' ? '#FEF2F2' : '#F0FDF4',
                            border: `1.5px solid ${scanResult.result === 'mismatch' ? '#FECACA' : '#A5D6A7'}`,
                        }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 12 }}>
                                <span style={{
                                    display: 'inline-flex', alignItems: 'center', gap: 4,
                                    padding: '3px 10px', borderRadius: 20, fontSize: 12, fontWeight: 700,
                                    color: RESULT_STYLES[scanResult.result]?.color || '#6B7280',
                                    background: RESULT_STYLES[scanResult.result]?.bg || '#F3F4F6',
                                }}>
                                    {RESULT_STYLES[scanResult.result]?.icon} {RESULT_STYLES[scanResult.result]?.label}
                                </span>
                                <span style={{ fontSize: 12, color: '#6B7280' }}>
                                    S/N: <strong style={{ fontFamily: 'monospace', color: '#374151' }}>{scanResult.serialNumber}</strong>
                                </span>
                            </div>

                            {scanResult.match && (
                                <div style={{ background: '#fff', borderRadius: 8, padding: 12, marginBottom: 12, border: '1px solid #E5E7EB', fontSize: 13 }}>
                                    <p style={{ fontWeight: 600, margin: '0 0 4px', color: '#374151' }}>
                                        {scanResult.itemStatus === 'belum_diterima' ? 'Found in master list:'
                                         : scanResult.itemStatus === 'diterima' ? 'Received — awaiting verification:'
                                         : 'Matched to:'}
                                    </p>
                                    <p style={{ margin: 0, color: '#5C001F' }}>
                                        {scanResult.match.item_no} — {scanResult.match.name}
                                    </p>
                                    <p style={{ margin: '4px 0 0', fontSize: 12, color: '#6B7280' }}>
                                        {scanResult.match.brand && `${scanResult.match.brand} `}
                                        {scanResult.match.supplier_name && `${scanResult.match.supplier_name} · `}
                                        {scanResult.match.category}
                                    </p>
                                </div>
                            )}

                            {scanResult.existingAsset && (
                                <div style={{ background: '#fff', borderRadius: 8, padding: 12, marginBottom: 12, border: '1px solid #E5E7EB', fontSize: 13 }}>
                                    <p style={{ fontWeight: 600, margin: '0 0 4px', color: '#374151' }}>Already exists as Asset:</p>
                                    <p style={{ margin: 0 }}>
                                        #{scanResult.existingAsset.asset_tag} — {scanResult.existingAsset.name} ({scanResult.existingAsset.status})
                                    </p>
                                </div>
                            )}

                            {/* Go to Verification button — instead of inline forms */}
                            {scanResult.match && scanResult.itemStatus !== 'verified' && (
                                <Link
                                    href={`/verification?scan=1&highlight=${scanResult.match.item_no}`}
                                    style={{
                                        display: 'inline-flex',
                                        padding: '8px 18px', borderRadius: 8, border: 'none',
                                        background: scanResult.itemStatus === 'belum_diterima' ? '#E65100' : '#1565C0',
                                        color: '#fff', fontWeight: 700, fontSize: 12, cursor: 'pointer',
                                        textDecoration: 'none', marginTop: 8,
                                    }}>
                                    Open in Verification Pipeline
                                </Link>
                            )}

                            {scanResult.result === 'mismatch' && (
                                <p style={{ fontSize: 12, color: '#6B7280', marginTop: 8 }}>
                                    This serial number was not found in the system. Try importing the item first via the Import page.
                                </p>
                            )}
                        </div>

                        {/* Scan Next */}
                        <button onClick={() => { setScanResult(null); setScannerKey(k => k + 1); setIsScanning(true); }}
                            style={{
                                marginTop: 12, padding: '8px 16px', background: '#5C001F', color: '#fff',
                                border: 'none', borderRadius: 6, fontSize: 12, fontWeight: 600, cursor: 'pointer',
                            }}>
                            Scan Next
                        </button>
                    </div>
                )}

                {/* Recent scans */}
                <div style={{ background: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden' }}>
                    <div style={{ padding: '12px 20px', borderBottom: '1px solid #E5E7EB' }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: 0, color: '#374151' }}>Recent Scans</h2>
                    </div>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                        <thead>
                            <tr style={{ background: '#F9FAFB' }}>
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
                                                color: st.color, background: st.bg,
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
