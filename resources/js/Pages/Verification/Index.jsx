import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    maroon2: '#7A0029',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray200: '#D5CFC9',
    gray300: '#B0A89F',
    gray500: '#8A8480',
    gray600: '#625E5B',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

const STATUS_STYLES = {
    belum_diterima: { bg: '#FFF3E0', color: '#E65100',    border: '#FFCC80', label: 'Belum Diterima' },
    diterima:       { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Diterima' },
    verified:       { bg: '#E3F2FD', color: '#1565C0',    border: '#90CAF9', label: 'Verified' },
    pending:        { bg: '#FEF3D6', color: '#C9840A',    border: '#F5D890', label: 'Pending' },
    active:         { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Aktif' },
    disposed:       { bg: '#F3E0E5', color: '#5C001F',    border: '#E8C0CB', label: 'Dilupus' },
};

const SOURCE_STYLES = {
    Master: { bg: '#F3E5F5', color: '#7B1FA2', border: '#CE93D8', label: 'Master' },
    DO:     { bg: '#FFF3E0', color: '#E65100', border: '#FFCC80', label: 'DO' },
    Asset:  { bg: '#E8F5E9', color: '#2E7D32', border: '#A5D6A7', label: 'Aset' },
};

function StatusBadge({ status }) {
    const s = STATUS_STYLES[status] || { bg: UTM.gray100, color: UTM.gray500, border: UTM.gray200, label: status };
    return (
        <span style={{
            display: 'inline-block', padding: '2px 8px', borderRadius: 999,
            fontSize: 11, fontWeight: 700, whiteSpace: 'nowrap',
            background: s.bg, color: s.color, border: `1px solid ${s.border}`,
        }}>{s.label}</span>
    );
}

function SourceBadge({ source }) {
    const s = SOURCE_STYLES[source] || SOURCE_STYLES.Master;
    return (
        <span style={{
            display: 'inline-block', padding: '2px 8px', borderRadius: 4,
            fontSize: 11, fontWeight: 700, whiteSpace: 'nowrap',
            background: s.bg, color: s.color, border: `1px solid ${s.border}`,
        }}>{s.label}</span>
    );
}

function StatCard({ label, count, color, sub }) {
    return (
        <div style={{
            background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`,
            padding: '14px 20px', textAlign: 'center', minWidth: 110, flex: 1,
        }}>
            <div style={{ fontSize: 28, fontWeight: 800, color: color ?? UTM.maroon }}>{count}</div>
            <div style={{ fontSize: 11, fontWeight: 600, color: UTM.gray500, marginTop: 2 }}>{label}</div>
            {sub && <div style={{ fontSize: 10, color: UTM.gray300, marginTop: 2 }}>{sub}</div>}
        </div>
    );
}

function PriceTag({ val }) {
    if (val === null || val === undefined) return <span style={{ color: UTM.gray300 }}>—</span>;
    const num = typeof val === 'string' ? parseFloat(val) : val;
    return <span>RM {num.toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>;
}

function SpecTooltip({ spec }) {
    if (!spec) return <span style={{ color: UTM.gray300 }}>—</span>;
    const preview = spec.length > 80 ? spec.slice(0, 80) + '...' : spec;
    return <span title={spec} style={{ cursor: 'help', color: UTM.gray500, fontSize: 12 }}>{preview}</span>;
}

function fmtDT(iso) {
    if (!iso) return '—';
    const d = new Date(iso);
    return d.toLocaleString('ms-MY', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
}

// ─── Camera Scanner — works on HTTPS/localhost, reports errors gracefully ─────
function CameraScanner({ onScan, onReady, onError }) {
    const [Scanner, setScanner] = useState(null);
    const [loadError, setLoadError] = useState(null);

    React.useEffect(() => {
        import('@yudiel/react-qr-scanner').then(mod => {
            setScanner(() => mod.Scanner);
            onReady?.();
        }).catch((e) => {
            setLoadError(e?.message || 'Failed to load scanner library');
            onError?.();
        });
    }, []);

    if (loadError) {
        return <p style={{ fontSize: 12, color: '#DC2626', marginBottom: 8 }}>! {loadError}</p>;
    }
    if (!Scanner) {
        return <p style={{ fontSize: 12, color: UTM.gray500, marginBottom: 8 }}>Loading camera...</p>;
    }
    return (
        <div style={{ maxWidth: 400, marginBottom: 12 }}>
            <Scanner
                onScan={result => {
                    if (result?.[0]?.rawValue) onScan(result[0].rawValue);
                }}
                onError={e => {
                    console.warn('Scanner error:', e);
                    onError?.();
                }}
                styles={{ container: { borderRadius: 8, overflow: 'hidden' } }}
            />
        </div>
    );
}

// ─── Scan Panel — manual S/N lookup + camera (HTTPS only) ───────────────────
function ScanPanel({ onScanResult, onClose }) {
    const [mode, setMode] = useState('manual');
    const [manualSn, setManualSn] = useState('');
    const [lookupResult, setLookupResult] = useState(null);
    const [scanning, setScanning] = useState(false);
    const [error, setError] = useState(null);
    const [cameraAvailable, setCameraAvailable] = useState(null); // null=unknown, true/false

    const doLookup = async (serialNumber) => {
        if (!serialNumber) return;
        setScanning(true);
        setError(null);
        setLookupResult(null);
        try {
            const res = await fetch(route('scanner.lookup'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({ serial_number: serialNumber }),
            });
            const data = await res.json();
            setLookupResult(data);
            if (data.matches?.[0]) {
                onScanResult(data.matches[0], data);
            } else {
                onScanResult(null, data);
            }
        } catch (e) {
            setError(e.message);
        } finally {
            setScanning(false);
        }
    };

    return (
        <div style={{
            background: '#FFF', borderRadius: 10, border: `2px solid ${UTM.gold}`,
            padding: 16, marginBottom: 16, position: 'relative',
        }}>
            {/* Close button */}
            <button onClick={onClose}
                style={{
                    position: 'absolute', top: 8, right: 10,
                    background: 'none', border: 'none', fontSize: 18, cursor: 'pointer',
                    color: UTM.gray500, lineHeight: 1,
                }}>x</button>

            <p style={{ fontSize: 14, fontWeight: 800, color: UTM.maroon, margin: '0 0 10px' }}>
                Scan S/N to Locate Item
            </p>

            {/* Mode toggle */}
            <div style={{ display: 'flex', gap: 6, marginBottom: 12 }}>
                <button onClick={() => setMode('manual')}
                    style={{
                        padding: '5px 12px', borderRadius: 6, fontSize: 12, fontWeight: 600,
                        border: `1.5px solid ${mode === 'manual' ? UTM.maroon : UTM.gray100}`,
                        background: mode === 'manual' ? '#FDF0F3' : UTM.white,
                        color: mode === 'manual' ? UTM.maroon : UTM.gray500, cursor: 'pointer',
                    }}>Manual Input</button>
                <button onClick={() => setMode('camera')}
                    style={{
                        padding: '5px 12px', borderRadius: 6, fontSize: 12, fontWeight: 600,
                        border: `1.5px solid ${mode === 'camera' ? UTM.maroon : UTM.gray100}`,
                        background: mode === 'camera' ? '#FDF0F3' : UTM.white,
                        color: mode === 'camera' ? UTM.maroon : UTM.gray500, cursor: 'pointer',
                    }}>Camera</button>
            </div>

            {mode === 'camera' ? (
                <CameraScanner onScan={(sn) => doLookup(sn)} onReady={() => setCameraAvailable(true)} onError={() => setCameraAvailable(false)} />
            ) : null}

            {/* Camera unavailable fallback — show manual input inline */}
            {mode === 'camera' && cameraAvailable === false && (
                <div style={{ padding: 10, marginBottom: 10, background: '#FEF3D6', borderRadius: 8, fontSize: 12, color: '#92400E', border: '1px solid #F5D890' }}>
                    Camera requires HTTPS or localhost. Use manual input below instead.
                </div>
            )}

            {/* Manual input — always available */}
            {mode === 'manual' || cameraAvailable === false ? (
                <div style={{ display: 'flex', gap: 8 }}>
                    <input type="text" value={manualSn}
                        onChange={e => setManualSn(e.target.value)}
                        onKeyDown={e => e.key === 'Enter' && doLookup(manualSn)}
                        placeholder="Type S/N and press Enter..."
                        style={{
                            flex: 1, padding: '9px 12px', borderRadius: 8,
                            border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none',
                        }}
                    />
                    <button onClick={() => doLookup(manualSn)} disabled={scanning}
                        style={{
                            padding: '9px 18px', borderRadius: 8, border: 'none',
                            background: UTM.maroon, color: UTM.white, fontWeight: 700,
                            fontSize: 13, cursor: 'pointer', opacity: scanning ? 0.6 : 1,
                        }}>
                        {scanning ? 'Searching...' : 'Lookup'}
                    </button>
                </div>
            ) : null}

            {error && (
                <p style={{ fontSize: 12, color: '#DC2626', marginTop: 8 }}>! {error}</p>
            )}

            {lookupResult && (
                <div style={{
                    marginTop: 10, padding: 10,
                    background: lookupResult.match_count > 0 ? '#F0FDF4' : '#FEF2F2',
                    borderRadius: 6, fontSize: 13, border: `1px solid ${lookupResult.match_count > 0 ? '#A5D6A7' : '#FECACA'}`,
                }}>
                    {lookupResult.match_count > 0 ? (
                        <div>
                            <span style={{ color: '#166534', fontWeight: 700 }}>Found:</span>{' '}
                            {lookupResult.matches[0]?.item_no} — {lookupResult.matches[0]?.name}
                            <p style={{ fontSize: 11, color: UTM.gray500, marginTop: 4 }}>
                                Scanned S/N highlighted in table below. Click Receive or Verify to proceed.
                            </p>
                        </div>
                    ) : (
                        <span style={{ color: '#DC2626' }}>No matching item found for "{lookupResult.serial_number}"</span>
                    )}
                </div>
            )}
        </div>
    );
}

// ─── Verify Modal (unchanged from current) ────────────────────────────────────
function VerifyModal({ item, onClose }) {
    const { errors: pageErrors } = usePage().props;
    const initialSerials = (item.serial_numbers ?? '')
        .split(',')
        .map(s => s.trim())
        .filter(s => s.length > 0);

    const [serialRows, setSerialRows] = useState(
        initialSerials.length > 0
            ? initialSerials.map(s => ({ serial: s, selected: true }))
            : [{ serial: '', selected: true }]
    );
    const [commonLocation, setCommonLocation] = useState('');
    const [custodianName, setCustodianName] = useState('');
    const [receivedDate, setReceivedDate] = useState(new Date().toISOString().slice(0, 10));
    const [warrantyExpiry, setWarrantyExpiry] = useState('');
    const [budgetVot, setBudgetVot] = useState('');
    const [voucherNo, setVoucherNo] = useState('');
    const [processing, setProcessing] = useState(false);

    const hasExplicitSerials = initialSerials.length > 0;
    const selectedCount = serialRows.filter(r => r.selected && r.serial.trim()).length;

    const handleVerify = (e) => {
        e.preventDefault();
        if (selectedCount === 0) return;
        setProcessing(true);
        const selectedSerials = serialRows
            .filter(r => r.selected && r.serial.trim())
            .map(r => r.serial.trim());
        router.post(`/verification/${item.id}/verify`, {
            serial_numbers: selectedSerials.join(', '),
            location: commonLocation,
            custodian_name: custodianName,
            budget_vot: budgetVot,
            voucher_no: voucherNo,
            received_date: receivedDate,
            warranty_expiry: warrantyExpiry,
        }, {
            preserveState: true,
            onSuccess: () => onClose(),
            onError: () => setProcessing(false),
        });
    };

    const toggleRow = (idx) => setSerialRows(prev =>
        prev.map((r, i) => i === idx ? { ...r, selected: !r.selected } : r)
    );
    const removeRow = (idx) => setSerialRows(prev => prev.filter((_, i) => i !== idx));
    const addRow = () => setSerialRows(prev => [...prev, { serial: '', selected: true }]);

    const assetTypeLabel = item.unit_price >= 2000 ? 'PA-2 (Fixed Asset, \u2265 RM 2k)' : 'PA-3 (Inventory, < RM 2k)';

    return (
        <div style={{
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000,
        }} onClick={onClose}>
            <div style={{
                background: UTM.white, borderRadius: 12, padding: 28, maxWidth: 640, width: '90%',
                boxShadow: '0 8px 32px rgba(0,0,0,0.12)', maxHeight: '90vh', overflowY: 'auto',
            }} onClick={e => e.stopPropagation()}>
                <h2 style={{ fontSize: 18, fontWeight: 700, color: UTM.gray900, margin: '0 0 4px' }}>Verify Item</h2>
                <p style={{ fontSize: 13, color: UTM.gray500, margin: '0 0 2px' }}>
                    {item.item_no} — {item.name}
                </p>
                <p style={{ fontSize: 12, color: UTM.gray500, margin: '0 0 16px' }}>
                    Type: {assetTypeLabel} &middot; Qty: {item.quantity} &middot; Price: RM {item.unit_price?.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}
                </p>

                {/* Backend rejection, shown inside the modal so the overlay can't hide it */}
                {pageErrors?.item && (
                    <div style={{ padding: '8px 12px', background: '#FFEBEE', border: '1px solid #FECACA', borderRadius: 6, fontSize: 12, color: '#C62828', marginBottom: 12 }}>
                        {pageErrors.item}
                    </div>
                )}

                <form onSubmit={handleVerify}>
                    {hasExplicitSerials ? (
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Serial Numbers &mdash; one asset will be created for each selected S/N</label>
                            {item.quantity === 1 && initialSerials.length > 1 && (
                                <div style={{
                                    padding: '8px 12px', background: '#FFFBEB', border: '1px solid #FDE68A',
                                    borderRadius: 6, fontSize: 12, color: '#92400E', marginBottom: 8,
                                }}>
                                    ⚠ Item qty=1 with {initialSerials.length} S/Ns &mdash; these appear to be internal component serials.
                                    Only 1 asset will be created with all selected S/Ns stored together.
                                </div>
                            )}
                            <div style={{ border: `1.5px solid ${UTM.gray100}`, borderRadius: 8, overflow: 'hidden' }}>
                                <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                                    <thead>
                                        <tr style={{ background: UTM.gray50, borderBottom: `1.5px solid ${UTM.gray100}` }}>
                                            <th style={{ padding: '8px 10px', textAlign: 'left', fontSize: 11, fontWeight: 700, color: UTM.gray500, width: 40 }}>#</th>
                                            <th style={{ padding: '8px 10px', textAlign: 'left', fontSize: 11, fontWeight: 700, color: UTM.gray500 }}>Serial Number</th>
                                            <th style={{ padding: '8px 10px', textAlign: 'center', fontSize: 11, fontWeight: 700, color: UTM.gray500, width: 60 }}>Include</th>
                                            <th style={{ padding: '8px 10px', textAlign: 'center', fontSize: 11, fontWeight: 700, color: UTM.gray500, width: 50 }}></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {serialRows.map((row, i) => (
                                            <tr key={i} style={{ borderBottom: `1px solid ${UTM.gray50}`, opacity: row.selected ? 1 : 0.4 }}>
                                                <td style={{ padding: '8px 10px', color: UTM.gray500 }}>{i + 1}</td>
                                                <td style={{ padding: '8px 10px' }}>
                                                    {hasExplicitSerials ? (
                                                        <span style={{ fontFamily: 'monospace', fontSize: 12 }}>{row.serial}</span>
                                                    ) : (
                                                        <input type="text" value={row.serial}
                                                            onChange={e => {
                                                                const updated = [...serialRows];
                                                                updated[i] = { ...updated[i], serial: e.target.value };
                                                                setSerialRows(updated);
                                                            }}
                                                            placeholder="Type S/N..."
                                                            style={{ width: '100%', padding: '5px 8px', borderRadius: 4, border: `1px solid ${UTM.gray100}`, fontSize: 12, outline: 'none', boxSizing: 'border-box' }}
                                                        />
                                                    )}
                                                </td>
                                                <td style={{ padding: '8px 10px', textAlign: 'center' }}>
                                                    <input type="checkbox" checked={row.selected} onChange={() => toggleRow(i)} />
                                                </td>
                                                <td style={{ padding: '8px 10px', textAlign: 'center' }}>
                                                    {!hasExplicitSerials && (
                                                        <button type="button" onClick={() => removeRow(i)}
                                                            style={{ background: 'none', border: 'none', color: '#DC2626', cursor: 'pointer', fontSize: 14 }}>x</button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            {!hasExplicitSerials && (
                                <button type="button" onClick={addRow}
                                    style={{ marginTop: 8, padding: '5px 12px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, fontSize: 12, cursor: 'pointer' }}>
                                    + Add S/N Row
                                </button>
                            )}
                        </div>
                    ) : (
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Serial Number <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                            <input type="text"
                                value={serialRows[0]?.serial ?? ''}
                                onChange={e => setSerialRows([{ serial: e.target.value, selected: true }])}
                                placeholder="e.g. SN-2025-001"
                                style={{ width: '100%', padding: '9px 12px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none', boxSizing: 'border-box' }}
                            />
                        </div>
                    )}

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                        <div>
                            <label style={labelStyle}>Location *</label>
                            <input type="text" value={commonLocation}
                                onChange={e => setCommonLocation(e.target.value)}
                                placeholder="e.g. Robotics Lab"
                                style={inputStyle} required />
                        </div>
                        <div>
                            <label style={labelStyle}>Custodian Name *</label>
                            <input type="text" value={custodianName}
                                onChange={e => setCustodianName(e.target.value)}
                                placeholder="e.g. Dr. Siti"
                                style={inputStyle} required />
                        </div>
                        <div>
                            <label style={labelStyle}>Received Date</label>
                            <input type="date" value={receivedDate}
                                onChange={e => setReceivedDate(e.target.value)}
                                style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Warranty Expiry <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                            <input type="date" value={warrantyExpiry}
                                onChange={e => setWarrantyExpiry(e.target.value)}
                                style={inputStyle} />
                        </div>
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                        <div>
                            <label style={labelStyle}>Vot Peruntukan (Budget) <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                            <input type="text" value={budgetVot}
                                onChange={e => setBudgetVot(e.target.value)}
                                placeholder="e.g. D001-2025-001"
                                style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>No. Baucer (Voucher) <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                            <input type="text" value={voucherNo}
                                onChange={e => setVoucherNo(e.target.value)}
                                placeholder="e.g. BK-2025-001"
                                style={inputStyle} />
                        </div>
                    </div>

                    <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
                        <button type="button" onClick={onClose}
                            style={{ padding: '9px 18px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, fontSize: 13, fontWeight: 600, cursor: 'pointer', color: UTM.gray600 }}>
                            Cancel
                        </button>
                        <button type="submit" disabled={processing || selectedCount === 0}
                            style={{
                                padding: '9px 18px', borderRadius: 8, border: 'none',
                                background: '#1565C0', color: UTM.white, fontWeight: 700, fontSize: 13,
                                cursor: 'pointer', opacity: (processing || selectedCount === 0) ? 0.6 : 1,
                            }}>
                            {processing ? 'Verifying...' : `Verify${selectedCount > 1 ? ` (${selectedCount} assets)` : ''}`}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

// ─── Receive Modal (unchanged) ────────────────────────────────────────────────
function ReceiveModal({ item, onClose }) {
    const { errors: pageErrors } = usePage().props;
    const [receiverName, setReceiverName] = useState('');
    const [receiverPosition, setReceiverPosition] = useState('');
    const [techOfficerName, setTechOfficerName] = useState('');
    const [techOfficerPosition, setTechOfficerPosition] = useState('');
    const [receivedDate, setReceivedDate] = useState(new Date().toISOString().slice(0, 10));
    const [doNo, setDoNo] = useState('');
    const [purchaseOrderNo, setPurchaseOrderNo] = useState('');
    const [qtyReceived, setQtyReceived] = useState(item.quantity ?? 1);
    const [invoiceNo, setInvoiceNo] = useState('');
    const [damageDesc, setDamageDesc] = useState('');
    const [notes, setNotes] = useState('');
    const [processing, setProcessing] = useState(false);

    const handleReceive = (e) => {
        e.preventDefault();
        setProcessing(true);
        router.post(`/verification/${item.id}/receive`, {
            received_date: receivedDate,
            do_no: doNo,
            purchase_order_no: purchaseOrderNo,
            quantity_received: qtyReceived,
            receiver_name: receiverName,
            receiver_position: receiverPosition,
            technical_officer_name: techOfficerName,
            technical_officer_position: techOfficerPosition,
            damage_description: damageDesc,
            notes,
            invoice_no: invoiceNo,
        }, {
            preserveState: true,
            onSuccess: () => onClose(),
            onError: () => setProcessing(false),
        });
    };

    return (
        <div style={{
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000,
        }} onClick={onClose}>
            <div style={{
                background: UTM.white, borderRadius: 12, padding: 28, maxWidth: 580, width: '90%',
                boxShadow: '0 8px 32px rgba(0,0,0,0.12)', maxHeight: '90vh', overflowY: 'auto',
            }} onClick={e => e.stopPropagation()}>
                <h2 style={{ fontSize: 18, fontWeight: 700, color: UTM.gray900, margin: '0 0 4px' }}>Receive Item</h2>
                <p style={{ fontSize: 13, color: UTM.gray500, margin: '0 0 16px' }}>
                    {item.item_no} — {item.name} &middot; Qty: {item.quantity} &middot; RM {item.unit_price?.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}
                </p>

                {/* Backend rejection, shown inside the modal so the overlay can't hide it */}
                {pageErrors?.item && (
                    <div style={{ padding: '8px 12px', background: '#FFEBEE', border: '1px solid #FECACA', borderRadius: 6, fontSize: 12, color: '#C62828', marginBottom: 12 }}>
                        {pageErrors.item}
                    </div>
                )}

                <form onSubmit={handleReceive}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                        <div>
                            <label style={labelStyle}>Receiver Name *</label>
                            <input type="text" value={receiverName} onChange={e => setReceiverName(e.target.value)} required
                                placeholder="e.g. Ahmad" style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Receiver Position</label>
                            <input type="text" value={receiverPosition} onChange={e => setReceiverPosition(e.target.value)}
                                placeholder="e.g. Pembantu Stor" style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Technical Officer Name</label>
                            <input type="text" value={techOfficerName} onChange={e => setTechOfficerName(e.target.value)}
                                placeholder="Juruteknik" style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Tech Officer Position</label>
                            <input type="text" value={techOfficerPosition} onChange={e => setTechOfficerPosition(e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Received Date *</label>
                            <input type="date" value={receivedDate} onChange={e => setReceivedDate(e.target.value)} required style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>DO No.</label>
                            <input type="text" value={doNo} onChange={e => setDoNo(e.target.value)}
                                placeholder="e.g. UTMCARD/2025/005" style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>PO No.</label>
                            <input type="text" value={purchaseOrderNo} onChange={e => setPurchaseOrderNo(e.target.value)}
                                placeholder="e.g. PPTK170300122025000272" style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Qty Received</label>
                            <input type="number" min="1" max={item.quantity ?? 999} value={qtyReceived}
                                onChange={e => setQtyReceived(parseInt(e.target.value) || 1)} style={inputStyle} />
                            {qtyReceived < (item.quantity ?? 1) && (
                                <p style={{ fontSize: 10, color: '#E65100', margin: '2px 0 0' }}>
                                    ⚠ Partial — {item.quantity - qtyReceived} unit lagi belum diterima
                                </p>
                            )}
                        </div>
                        <div>
                            <label style={labelStyle}>Invoice No.</label>
                            <input type="text" value={invoiceNo} onChange={e => setInvoiceNo(e.target.value)} style={inputStyle} />
                        </div>
                    </div>

                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Damage Description <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                        <textarea value={damageDesc} onChange={e => setDamageDesc(e.target.value)}
                            rows={2} style={{ ...textareaStyle, resize: 'vertical' }} />
                    </div>
                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Notes <span style={{ fontWeight: 400, color: UTM.gray300 }}>(optional)</span></label>
                        <textarea value={notes} onChange={e => setNotes(e.target.value)}
                            rows={2} style={{ ...textareaStyle, resize: 'vertical' }} />
                    </div>

                    <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
                        <button type="button" onClick={onClose}
                            style={{ padding: '9px 18px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, fontSize: 13, fontWeight: 600, cursor: 'pointer', color: UTM.gray600 }}>
                            Cancel
                        </button>
                        <button type="submit" disabled={processing}
                            style={{
                                padding: '9px 18px', borderRadius: 8, border: 'none',
                                background: '#E65100', color: UTM.white, fontWeight: 700, fontSize: 13,
                                cursor: 'pointer', opacity: processing ? 0.6 : 1,
                            }}>
                            {processing ? 'Receiving...' : 'Confirm Receive'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

// ─── Tab Switcher ──────────────────────────────────────────────────────────────
function TabBar({ tabs, activeTab, onTabChange }) {
    return (
        <div style={{ display: 'flex', gap: 0, marginBottom: 20, borderBottom: `2px solid ${UTM.gray100}` }}>
            {tabs.map(tab => {
                const isActive = activeTab === tab.key;
                return (
                    <button key={tab.key} onClick={() => onTabChange(tab.key)}
                        style={{
                            padding: '10px 20px', fontSize: 13, fontWeight: isActive ? 800 : 600,
                            color: isActive ? UTM.maroon : UTM.gray500,
                            background: 'none', border: 'none',
                            borderBottom: isActive ? `3px solid ${UTM.maroon}` : '3px solid transparent',
                            cursor: 'pointer', transition: 'all 0.12s',
                        }}>
                        {tab.label}
                        {tab.count !== undefined && (
                            <span style={{ marginLeft: 6, fontSize: 11, opacity: 0.7 }}>({tab.count})</span>
                        )}
                    </button>
                );
            })}
        </div>
    );
}

// ─── Pipeline View ─────────────────────────────────────────────────────────────
function PipelineView({ items, stats, categories, filters, can_verify, can_delete, can_force_delete, trashed }) {
    // Validation errors from the last receive/verify attempt. Inertia shares them
    // automatically as the `errors` prop (Inertia\Middleware::share).
    // The banner below used to read `filters.item_error`, which the controller
    // never sends — pipeline rejections were silently swallowed.
    const { errors: pageErrors } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [catFilter, setCatFilter] = useState(filters.category ?? '');
    const [statusFilter, setStatusFilter] = useState(filters.status ?? '');
    const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
    const [dateTo, setDateTo] = useState(filters.date_to ?? '');
    const [verifyItem, setVerifyItem] = useState(null);
    const [receiveItem, setReceiveItem] = useState(null);
    const [expandedId, setExpandedId] = useState(null);
    const [showScan, setShowScan] = useState(!!filters.scan);
    const [showTrash, setShowTrash] = useState(false);
    const [scanHighlight, setScanHighlight] = useState(filters.highlight || null);
    const activeHighlight = scanHighlight;

    const handleDeleteMasterItem = (item) => {
        if (!window.confirm(`Padam "${item.name}" (${item.item_no})?\nItem akan dipindahkan ke Tong dan boleh dipulihkan.`)) return;
        router.delete(`/verification/${item.id}`, { preserveScroll: true });
    };

    const handleForceDeleteMasterItem = (record) => {
        const typed = window.prompt(
            `PADAM KEKAL "${record.name}" (${record.item_no})?\n\n`
            + 'Tindakan ini TIDAK BOLEH DIBATALKAN — rekod akan hilang sepenuhnya dari sistem.\n\n'
            + 'Taip PADAM untuk mengesahkan:'
        );
        if (typed !== 'PADAM') return;
        router.delete(`/verification/${record.id}/force`, { preserveScroll: true });
    };

    const handleFilter = (key, val) => {
        const newVal = val === (key === 'status' ? statusFilter : catFilter) ? '' : val;
        if (key === 'status') setStatusFilter(newVal);
        else setCatFilter(newVal);
        router.get('/verification', {
            ...filters, search,
            category: key === 'category' ? newVal : catFilter,
            status: key === 'status' ? newVal : statusFilter,
            date_from: dateFrom, date_to: dateTo, tab: 'pipeline', page: 1,
        }, { preserveState: true, replace: true });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/verification', { ...filters, search, category: catFilter, status: statusFilter, date_from: dateFrom, date_to: dateTo, tab: 'pipeline', page: 1 },
            { preserveState: true, replace: true });
    };

    const handlePage = (p) => {
        router.get('/verification', { ...filters, search, category: catFilter, status: statusFilter, date_from: dateFrom, date_to: dateTo, tab: 'pipeline', page: p },
            { preserveState: true, replace: true });
    };

    const handleScanResult = (matchedItem, data) => {
        if (matchedItem) {
            setScanHighlight(matchedItem.item_no);
            // Clear status filter so the target item is visible
            if (statusFilter !== '') {
                setStatusFilter('');
            }
        }
    };

    return (
        <div>
            {/* Header */}
            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.gray900, margin: 0 }}>
                    Verification Pipeline
                </h1>
                <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                    Process incoming items: <strong style={{ color: '#E65100' }}>Receive</strong> (belum_diterima &rarr; diterima), then <strong style={{ color: '#1565C0' }}>Verify</strong> (diterima &rarr; PA-1 + Aset created).
                    Use the scanner to find items by S/N or barcode.
                </p>
            </div>

            {/* Pipeline rejection — already received / already verified / must receive first */}
            {pageErrors?.item && (
                <div style={{ padding: '10px 16px', background: '#FFEBEE', borderRadius: 8, marginBottom: 16, fontSize: 13, color: '#C62828' }}>
                    {pageErrors.item}
                </div>
            )}

            {/* Stats row */}
            <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                <StatCard label="All Items"   count={stats.total}          color={UTM.maroon} />
                <StatCard label="Belum Diterima" count={stats.belum_diterima} color="#E65100" />
                <StatCard label="Diterima"    count={stats.diterima}       color="#2E7D32" />
                <StatCard label="Verified"    count={stats.verified}       color="#1565C0" />
                <StatCard label="Assets"      count={stats.with_asset}     color="#7B1FA2" />
            </div>

            {/* Pipeline visual */}
            <div style={{
                display: 'flex', alignItems: 'center', gap: 4, marginBottom: 16,
                padding: '10px 16px', background: UTM.gray50, borderRadius: 8,
                border: `1px solid ${UTM.gray100}`, fontSize: 12,
            }}>
                <span style={{ fontWeight: 800, color: UTM.maroon, marginRight: 8 }}>Pipeline:</span>
                <StepBadge label="Belum Diterima" color="#E65100" />
                <span style={{ color: UTM.gray300 }}>&rarr;</span>
                <StepBadge label="Receive" color="#E65100" light />
                <span style={{ color: UTM.gray300 }}>&rarr;</span>
                <StepBadge label="Diterima" color="#2E7D32" />
                <span style={{ color: UTM.gray300 }}>&rarr;</span>
                <StepBadge label="Verify" color="#1565C0" light />
                <span style={{ color: UTM.gray300 }}>&rarr;</span>
                <StepBadge label="Aset (PA-2/3)" color="#7B1FA2" />
            </div>

            {/* Tong (soft-deleted master items) — admin/superadmin only */}
            {can_delete && (
                <>
                    <button onClick={() => setShowTrash(!showTrash)}
                        style={{
                            padding: '6px 14px', borderRadius: 6, marginBottom: 16,
                            border: `1.5px solid ${showTrash ? '#EF4444' : UTM.gray100}`,
                            background: showTrash ? '#FEF2F2' : UTM.white,
                            fontSize: 12, fontWeight: 700,
                            color: showTrash ? '#EF4444' : UTM.gray500, cursor: 'pointer',
                        }}>
                        Tong ({trashed?.length ?? 0})
                    </button>
                    {showTrash && (
                        <div style={{ background: UTM.white, borderRadius: 10, border: '1.5px solid #FECACA', overflow: 'auto', marginBottom: 20 }}>
                            <div style={{ padding: '10px 16px', background: '#FEF2F2', borderBottom: '1.5px solid #FECACA', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                <span style={{ fontSize: 12, fontWeight: 700, color: '#991B1B' }}>Tong Sampah — {trashed?.length ?? 0} item</span>
                                <button onClick={() => setShowTrash(false)} style={{ fontSize: 11, color: '#991B1B', background: 'none', border: 'none', cursor: 'pointer', fontWeight: 600 }}>Tutup</button>
                            </div>
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                                <thead>
                                    <tr style={{ background: '#FEF2F2' }}>
                                        <th style={thStyle}>Item No.</th>
                                        <th style={thStyle}>Nama</th>
                                        <th style={thStyle}>Kategori</th>
                                        <th style={thStyle}>Dipadam Pada</th>
                                        <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {(trashed ?? []).length === 0 ? (
                                        <tr><td colSpan={5} style={{ padding: '40px 20px', textAlign: 'center', color: UTM.gray500, fontSize: 13 }}>Tong sampah kosong.</td></tr>
                                    ) : (trashed ?? []).map(record => (
                                        <tr key={record.id} style={{ borderBottom: `1px solid ${UTM.gray100}` }}>
                                            <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 12 }}>{record.item_no}</td>
                                            <td style={{ ...tdStyle, fontWeight: 700 }}>{record.name}</td>
                                            <td style={tdStyle}>{record.category}</td>
                                            <td style={{ ...tdStyle, fontSize: 11, color: UTM.gray500 }}>{fmtDT(record.deleted_at)}</td>
                                            <td style={{ ...tdStyle, textAlign: 'right' }}>
                                                <div style={{ display: 'flex', gap: 6, justifyContent: 'flex-end' }}>
                                                    <button onClick={() => {
                                                        if (window.confirm('Pulihkan item ini?')) {
                                                            router.post(`/verification/${record.id}/restore`, {}, { preserveScroll: true });
                                                        }
                                                    }} style={{ padding: '5px 14px', borderRadius: 6, fontSize: 12, fontWeight: 700, background: '#E6F4EC', color: '#1A7A3C', border: '1px solid #B2DFC2', cursor: 'pointer' }}>
                                                        Pulihkan
                                                    </button>
                                                    {can_force_delete && (
                                                        <button onClick={() => handleForceDeleteMasterItem(record)}
                                                            title="Hapuskan secara kekal — tidak boleh dibatalkan (superadmin)"
                                                            style={{
                                                                padding: '5px 14px', borderRadius: 6, fontSize: 12, fontWeight: 700,
                                                                background: '#7F1D1D', color: UTM.white,
                                                                border: '1px solid #7F1D1D', cursor: 'pointer',
                                                            }}>
                                                            Padam Kekal
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </>
            )}

            {/* Scanner toggle */}
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: 12 }}>
                <button onClick={() => setShowScan(!showScan)}
                    style={{
                        padding: '8px 16px', borderRadius: 8,
                        border: `1.5px solid ${showScan ? UTM.maroon : UTM.gray100}`,
                        background: showScan ? '#FDF0F3' : UTM.white,
                        color: showScan ? UTM.maroon : UTM.gray500,
                        fontWeight: 700, fontSize: 12, cursor: 'pointer',
                    }}>
                    {showScan ? 'Close Scanner' : 'Scan Barcode'}
                </button>
            </div>

            {/* Scan panel */}
            {showScan && (
                <ScanPanel
                    onScanResult={handleScanResult}
                    onClose={() => { setShowScan(false); setScanHighlight(null); }}
                />
            )}

            {/* Filters */}
            <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                <form onSubmit={handleSearch} style={{ display: 'flex', gap: 8, flex: 1, minWidth: 200 }}>
                    <input value={search} onChange={e => setSearch(e.target.value)}
                        placeholder="Search name, item no., brand, serial..."
                        style={{
                            flex: 1, padding: '9px 14px', borderRadius: 8,
                            border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none',
                        }}
                    />
                    <button type="submit" style={{
                        padding: '9px 18px', borderRadius: 8, border: 'none',
                        background: UTM.maroon, color: UTM.white, fontWeight: 700, fontSize: 13, cursor: 'pointer',
                    }}>Search</button>
                </form>

                <div style={{ display: 'flex', gap: 6 }}>
                    {[
                        { key: 'belum_diterima', label: 'Belum Diterima', color: '#E65100' },
                        { key: 'diterima', label: 'Diterima', color: '#2E7D32' },
                        { key: 'verified', label: 'Verified', color: '#1565C0' },
                    ].map(s => (
                        <button key={s.key} onClick={() => handleFilter('status', s.key)}
                            style={{
                                padding: '6px 14px', borderRadius: 6,
                                border: `1.5px solid ${statusFilter === s.key ? UTM.maroon : UTM.gray100}`,
                                background: statusFilter === s.key ? '#FDF0F3' : UTM.white,
                                color: statusFilter === s.key ? UTM.maroon : UTM.gray500,
                                fontWeight: 600, fontSize: 12, cursor: 'pointer',
                            }}>{s.label}</button>
                    ))}
                </div>

                {categories?.length > 0 && (
                    <select value={catFilter} onChange={e => handleFilter('category', e.target.value)}
                        style={{
                            padding: '6px 14px', borderRadius: 6, fontSize: 12,
                            border: `1.5px solid ${UTM.gray100}`, background: UTM.white,
                            color: UTM.gray700, fontWeight: 600, cursor: 'pointer',
                        }}>
                        <option value="">All Labs</option>
                        {categories.map(c => <option key={c} value={c}>{c}</option>)}
                    </select>
                )}

                <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                    <input type="date" value={dateFrom}
                        onChange={e => {
                            setDateFrom(e.target.value);
                            router.get('/verification', { ...filters, search, category: catFilter, status: statusFilter, date_from: e.target.value, date_to: dateTo, tab: 'pipeline', page: 1 },
                                { preserveState: true, replace: true });
                        }}
                        style={{ padding: '6px 10px', borderRadius: 6, fontSize: 12, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontWeight: 600, cursor: 'pointer' }}
                    />
                    <span style={{ color: UTM.gray300, fontSize: 11 }}>to</span>
                    <input type="date" value={dateTo}
                        onChange={e => {
                            setDateTo(e.target.value);
                            router.get('/verification', { ...filters, search, category: catFilter, status: statusFilter, date_from: dateFrom, date_to: e.target.value, tab: 'pipeline', page: 1 },
                                { preserveState: true, replace: true });
                        }}
                        style={{ padding: '6px 10px', borderRadius: 6, fontSize: 12, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontWeight: 600, cursor: 'pointer' }}
                    />
                    {(dateFrom || dateTo) && (
                        <button onClick={() => {
                            setDateFrom(''); setDateTo('');
                            router.get('/verification', { ...filters, search, category: catFilter, status: statusFilter, date_from: '', date_to: '', tab: 'pipeline', page: 1 },
                                { preserveState: true, replace: true });
                        }}
                            style={{
                                padding: '4px 10px', borderRadius: 6, fontSize: 11,
                                border: `1px solid ${UTM.gray100}`, background: UTM.white,
                                color: UTM.gray500, fontWeight: 600, cursor: 'pointer',
                            }}>
                            Clear
                        </button>
                    )}
                </div>
            </div>

            {/* Items table */}
            {items.data?.length === 0 ? (
                <div style={{
                    background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`,
                    padding: 48, textAlign: 'center', color: UTM.gray500, fontSize: 14,
                }}>
                    No items found matching these filters.
                </div>
            ) : (
                <div style={{ background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`, overflow: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13, minWidth: 1000 }}>
                        <thead>
                            <tr style={{ background: UTM.gray50, borderBottom: `1.5px solid ${UTM.gray100}` }}>
                                <th style={thStyle}>Item No.</th>
                                <th style={thStyle}>Category</th>
                                <th style={thStyle}>Name</th>
                                <th style={{ ...thStyle, minWidth: 90 }}>Specification</th>
                                <th style={thStyle}>Brand</th>
                                <th style={thStyle}>Serial No.</th>
                                <th style={thStyle}>Country</th>
                                <th style={{ ...thStyle, textAlign: 'center' }}>Qty</th>
                                <th style={{ ...thStyle, textAlign: 'right' }}>Unit Price</th>
                                <th style={{ ...thStyle, textAlign: 'right' }}>Total</th>
                                <th style={thStyle}>Sodo</th>
                                <th style={thStyle}>DO No.</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.data.map((item) => {
                                const isHighlighted = activeHighlight === item.item_no;
                                return (
                                <React.Fragment key={item.id}>
                                <tr style={{
                                    borderBottom: `1px solid ${UTM.gray50}`,
                                    background: isHighlighted ? '#FFFFF0' : 'transparent',
                                    outline: isHighlighted ? `2px solid ${UTM.gold}` : 'none',
                                }}>
                                    <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 12 }}>{item.item_no}</td>
                                    <td style={{ ...tdStyle, fontSize: 12, color: UTM.gray600, maxWidth: 100 }}>{item.category}</td>
                                    <td style={{ ...tdStyle, maxWidth: 180, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }} title={item.name}>
                                        {item.name}
                                    </td>
                                    <td style={{ ...tdStyle, maxWidth: 140 }}><SpecTooltip spec={item.specification} /></td>
                                    <td style={tdStyle}>{item.brand ?? '—'}</td>
                                    <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 12, maxWidth: 130, wordBreak: 'break-all' }}>
                                        {item.serial_numbers ?? '—'}
                                    </td>
                                    <td style={{ ...tdStyle, fontSize: 12, color: UTM.gray600 }}>{item.country_of_origin ?? '—'}</td>
                                    <td style={{ ...tdStyle, textAlign: 'center' }}>{item.quantity}</td>
                                    <td style={{ ...tdStyle, textAlign: 'right', whiteSpace: 'nowrap' }}>
                                        {item.unit_price ? `RM ${item.unit_price.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}` : '—'}
                                    </td>
                                    <td style={{ ...tdStyle, textAlign: 'right', whiteSpace: 'nowrap' }}>
                                        {item.total_price ? `RM ${item.total_price.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}` : '—'}
                                    </td>
                                    <td style={{ ...tdStyle, fontSize: 12, fontFamily: 'monospace' }}>{item.sodo_bayaran ?? '—'}</td>
                                    <td style={{ ...tdStyle, fontSize: 12, fontFamily: 'monospace' }}>{item.do_no ?? '—'}</td>
                                    <td style={tdStyle}><StatusBadge status={item.status} /></td>
                                    <td style={tdStyle}>
                                        <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                                            <button onClick={() => setExpandedId(expandedId === item.id ? null : item.id)}
                                                style={{
                                                    padding: '4px 10px', borderRadius: 6, fontSize: 11, fontWeight: 700,
                                                    background: expandedId === item.id ? UTM.gold : '#EDE9E4',
                                                    color: expandedId === item.id ? UTM.maroon : UTM.gray700,
                                                    border: 'none', cursor: 'pointer', whiteSpace: 'nowrap',
                                                }}>
                                                {expandedId === item.id ? 'Tutup' : 'Butiran'}
                                            </button>
                                            {item.status === 'verified' ? (
                                                item.has_asset ? (
                                                    <a href={`/assets/${item.asset_id}/kewpa2`}
                                                        style={{ color: '#7B1FA2', fontWeight: 600, fontSize: 12, textDecoration: 'none' }}>
                                                        View Asset
                                                    </a>
                                                ) : (
                                                    <span style={{ color: UTM.gray300, fontSize: 12 }}>Verified</span>
                                                )
                                            ) : item.status === 'diterima' ? (
                                                can_verify ? (
                                                <button onClick={() => setVerifyItem(item)}
                                                    style={{
                                                        padding: '6px 12px', borderRadius: 6, border: 'none',
                                                        background: '#1565C0', color: UTM.white,
                                                        fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                                    }}>
                                                    Verify
                                                </button>
                                                ) : (
                                                <span style={{ color: UTM.gray500, fontSize: 11, fontStyle: 'italic' }}>
                                                    Menunggu pengesahan pentadbir
                                                </span>
                                                )
                                            ) : (
                                                <button onClick={() => setReceiveItem(item)}
                                                    style={{
                                                        padding: '6px 12px', borderRadius: 6, border: 'none',
                                                        background: '#E65100', color: UTM.white,
                                                        fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                                    }}>
                                                    Receive
                                                </button>
                                            )}
                                            {can_delete && !item.has_asset && !item.has_receiving && (
                                                <button onClick={() => handleDeleteMasterItem(item)}
                                                    title="Padam — pindah ke Tong (boleh dipulihkan)"
                                                    style={{
                                                        padding: '6px 12px', borderRadius: 6, border: 'none',
                                                        background: '#FEF2F2', color: '#DC2626',
                                                        fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                                    }}>
                                                    Hapus
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>

                                {expandedId === item.id && (
                                    <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                        <td colSpan={14} style={{ padding: 0 }}>
                                            <div style={{ padding: '20px 24px', display: 'flex', flexWrap: 'wrap', gap: 24, borderLeft: `4px solid ${UTM.gold}` }}>
                                                <div style={{ minWidth: 200 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                        Lab & Location
                                                    </h4>
                                                    <table style={{ width: '100%', fontSize: 12, lineHeight: 1.8 }}>
                                                        <tbody>
                                                            <tr><td style={{ color: UTM.gray500, width: '50%' }}>Lab</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.category || 'CAIRO Lab'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Location</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.location || item.category || 'CAIRO Lab'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Custodian</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.custodian_name || 'Not assigned'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Model</td>
                                                                <td style={{ fontWeight: 600, fontFamily: 'monospace', fontSize: 12, color: UTM.gray900 }}>{item.model || '—'}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style={{ minWidth: 200 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                        Time & Date
                                                    </h4>
                                                    <table style={{ width: '100%', fontSize: 12, lineHeight: 1.8 }}>
                                                        <tbody>
                                                            <tr><td style={{ color: UTM.gray500, width: '50%' }}>Added</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{fmtDT(item.created_at)}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Received</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.received_date && item.received_date !== '—' ? item.received_date : '—'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Verified</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.verified_at ? fmtDT(item.verified_at) : '—'}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style={{ flex: 1, minWidth: 250 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                        Supplier & Pricing
                                                    </h4>
                                                    <table style={{ width: '100%', fontSize: 12, lineHeight: 1.8 }}>
                                                        <tbody>
                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Supplier</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.supplier || '—'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Unit Price</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>RM {item.unit_price?.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Total Price</td>
                                                                <td style={{ fontWeight: 600, color: UTM.gray900 }}>RM {item.total_price?.toLocaleString('ms-MY', { minimumFractionDigits: 2 })}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Asset Tag</td>
                                                                <td style={{ fontWeight: 600, fontFamily: 'monospace', color: UTM.gray900 }}>{item.asset_tag || '—'}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style={{ flex: '1 1 100%', minWidth: 300 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                        Specification
                                                    </h4>
                                                    <pre style={{ margin: 0, fontSize: 12, lineHeight: 1.6, whiteSpace: 'pre-wrap', wordBreak: 'break-word', fontFamily: 'inherit', color: UTM.gray700, maxHeight: 150, overflow: 'auto' }}>
                                                        {item.specification || 'No specification available.'}
                                                    </pre>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                                </React.Fragment>
                            );})}
                        </tbody>
                    </table>

                    {items.last_page > 1 && (
                        <div style={{ display: 'flex', justifyContent: 'center', gap: 6, padding: '14px 16px', borderTop: `1px solid ${UTM.gray100}` }}>
                            {Array.from({ length: items.last_page }, (_, i) => i + 1).map(p => (
                                <button key={p} onClick={() => handlePage(p)}
                                    style={{
                                        padding: '6px 12px', borderRadius: 6, border: `1.5px solid ${p === items.current_page ? UTM.maroon : UTM.gray100}`,
                                        background: p === items.current_page ? '#FDF0F3' : UTM.white,
                                        color: p === items.current_page ? UTM.maroon : UTM.gray500,
                                        fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                    }}>{p}</button>
                            ))}
                        </div>
                    )}
                </div>
            )}

            <div style={{ marginTop: 16, display: 'flex', gap: 16, flexWrap: 'wrap', fontSize: 12, color: UTM.gray500 }}>
                <span>▤ <strong style={{ color: '#E65100' }}>Receive</strong> (orange) = belum_diterima — marks the item diterima; no PA-1 yet</span>
                <span>▤ <strong style={{ color: '#1565C0' }}>Verify</strong> (blue) = diterima — creates PA-1 + the PA-2/PA-3 asset</span>
                <span>▤ Threshold: <strong>RM 2,000</strong> — below = PA-3 (Inventori), RM 2,000 and above = PA-2 (Harta Tetap)</span>
            </div>

            {verifyItem && <VerifyModal item={verifyItem} onClose={() => setVerifyItem(null)} />}
            {receiveItem && <ReceiveModal item={receiveItem} onClose={() => setReceiveItem(null)} />}
        </div>
    );
}

function StepBadge({ label, color, light }) {
    return (
        <span style={{
            display: 'inline-block', padding: '2px 8px', borderRadius: 4,
            fontSize: 11, fontWeight: 700,
            background: light ? `${color}15` : `${color}20`,
            color: color,
        }}>{label}</span>
    );
}

// ─── Registry View (replaces Items Overview) ──────────────────────────────────
function RegistryView({ registryItems, registryCounts, filters }) {
    const [search, setSearch] = useState('');
    const [catFilter, setCatFilter] = useState('');
    const [srcFilter, setSrcFilter] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');
    const [expandedId, setExpandedId] = useState(null);

    // Client-side filtering for registry (data is already loaded from server)
    const filtered = registryItems.filter(item => {
        if (search) {
            const s = search.toLowerCase();
            if (!(item.name?.toLowerCase().includes(s) ||
                  item.item_no?.toLowerCase().includes(s) ||
                  item.brand?.toLowerCase().includes(s) ||
                  item.serial_no?.toLowerCase().includes(s) ||
                  item.category?.toLowerCase().includes(s))) return false;
        }
        if (catFilter && item.category !== catFilter) return false;
        if (srcFilter === 'diterima' && item.status !== 'diterima') return false;
        if (srcFilter === 'belum_diterima' && item.status !== 'belum_diterima') return false;
        if (srcFilter === 'assets' && item.source !== 'Asset') return false;
        if (dateFrom && item.created_at && new Date(item.created_at) < new Date(dateFrom)) return false;
        if (dateTo && item.created_at && new Date(item.created_at) > new Date(dateTo + 'T23:59:59')) return false;
        return true;
    });

    // Unique categories from data
    const categories = [...new Set(registryItems.map(i => i.category).filter(Boolean))].sort();

    return (
        <div>
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.gray900, margin: 0 }}>
                    Master Inventory Register
                </h1>
                <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                    Complete equipment register — all items and registered assets in one place.
                    Use the Pipeline tab to process incoming items.
                </p>
            </div>

            <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                <StatCard label="All Items"        count={registryCounts.total}          color={UTM.maroon} />
                <StatCard label="Diterima"         count={registryCounts.diterima}       color="#2E7D32" />
                <StatCard label="Belum Diterima"   count={registryCounts.belum_diterima} color="#E65100" />
                <StatCard label="Registered Assets" count={registryCounts.assets}        color="#7B1FA2" />
            </div>

            <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                <div style={{ display: 'flex', gap: 8, flex: 1, minWidth: 200 }}>
                    <input type="text" placeholder="Search name, item no., brand, spec, serial..."
                        value={search} onChange={e => setSearch(e.target.value)}
                        style={{ flex: 1, padding: '9px 14px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none' }}
                    />
                </div>

                <div style={{ display: 'flex', gap: 6 }}>
                    {[
                        { key: 'diterima', label: 'Diterima' },
                        { key: 'belum_diterima', label: 'Belum Diterima' },
                        { key: 'assets', label: 'Aset Sedia Ada' },
                    ].map(s => (
                        <button key={s.key} onClick={() => setSrcFilter(srcFilter === s.key ? '' : s.key)}
                            style={{
                                padding: '6px 14px', borderRadius: 6,
                                border: `1.5px solid ${srcFilter === s.key ? UTM.maroon : UTM.gray100}`,
                                background: srcFilter === s.key ? '#FDF0F3' : UTM.white,
                                color: srcFilter === s.key ? UTM.maroon : UTM.gray500,
                                fontWeight: 600, fontSize: 12, cursor: 'pointer',
                            }}>{s.label}</button>
                    ))}
                </div>

                {categories.length > 0 && (
                    <select value={catFilter} onChange={e => setCatFilter(e.target.value)}
                        style={{ padding: '6px 14px', borderRadius: 6, fontSize: 12, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontWeight: 600, cursor: 'pointer' }}>
                        <option value="">All Labs</option>
                        {categories.map(c => <option key={c} value={c}>{c}</option>)}
                    </select>
                )}

                <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                    <input type="date" value={dateFrom} onChange={e => setDateFrom(e.target.value)}
                        style={{ padding: '6px 10px', borderRadius: 6, fontSize: 12, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontWeight: 600, cursor: 'pointer' }} />
                    <span style={{ color: UTM.gray300, fontSize: 11 }}>to</span>
                    <input type="date" value={dateTo} onChange={e => setDateTo(e.target.value)}
                        style={{ padding: '6px 10px', borderRadius: 6, fontSize: 12, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontWeight: 600, cursor: 'pointer' }} />
                </div>
            </div>

            {filtered.length === 0 ? (
                <div style={{ background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`, padding: 48, textAlign: 'center', color: UTM.gray500, fontSize: 14 }}>
                    No items found. Try a different filter or search term.
                </div>
            ) : (
                <div style={{ background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`, overflow: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13, minWidth: 1200 }}>
                        <thead>
                            <tr style={{ background: UTM.gray50, borderBottom: `1.5px solid ${UTM.gray100}` }}>
                                <th style={thRegistry}>Source</th>
                                <th style={thRegistry}>Item No.</th>
                                <th style={thRegistry}>Category</th>
                                <th style={thRegistry}>Name</th>
                                <th style={thRegistry}>Brand</th>
                                <th style={thRegistry}>Spec</th>
                                <th style={thRegistry}>Serial No.</th>
                                <th style={{ ...thRegistry, textAlign: 'center' }}>Qty</th>
                                <th style={{ ...thRegistry, textAlign: 'right' }}>Unit Price</th>
                                <th style={{ ...thRegistry, textAlign: 'right' }}>Total</th>
                                <th style={thRegistry}>DO No.</th>
                                <th style={thRegistry}>Supplier</th>
                                <th style={thRegistry}>Status</th>
                                <th style={thRegistry}></th>
                            </tr>
                        </thead>
                        <tbody>
                            {filtered.map((item) => (
                                <React.Fragment key={item.id}>
                                <tr style={{ borderBottom: `1px solid ${UTM.gray50}` }}
                                    onMouseEnter={e => e.currentTarget.style.background = UTM.gray50}
                                    onMouseLeave={e => e.currentTarget.style.background = 'transparent'}>
                                    <td style={tdRegistry}><SourceBadge source={item.source} /></td>
                                    <td style={{ ...tdRegistry, fontFamily: 'monospace', fontSize: 12 }}>{item.item_no ?? '—'}</td>
                                    <td style={{ ...tdRegistry, maxWidth: 130 }}><span style={{ fontSize: 12, color: UTM.gray600 }}>{item.category ?? '—'}</span></td>
                                    <td style={{ ...tdRegistry, maxWidth: 200, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                        {item.url ? (
                                            <a href={item.url} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }} title={item.name}>{item.name}</a>
                                        ) : (
                                            <span title={item.name}>{item.name}</span>
                                        )}
                                    </td>
                                    <td style={tdRegistry}>{item.brand ?? '—'}</td>
                                    <td style={{ ...tdRegistry, maxWidth: 180 }}><SpecTooltip spec={item.specification} /></td>
                                    <td style={{ ...tdRegistry, maxWidth: 150 }}>
                                        <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray700, wordBreak: 'break-all' }}>{item.serial_no ?? '—'}</span>
                                    </td>
                                    <td style={{ ...tdRegistry, textAlign: 'center' }}>{item.quantity}</td>
                                    <td style={{ ...tdRegistry, textAlign: 'right', whiteSpace: 'nowrap' }}><PriceTag val={item.unit_price} /></td>
                                    <td style={{ ...tdRegistry, textAlign: 'right', whiteSpace: 'nowrap' }}><PriceTag val={item.total_price} /></td>
                                    <td style={{ ...tdRegistry, fontSize: 12, fontFamily: 'monospace' }}>{item.do_no ?? '—'}</td>
                                    <td style={{ ...tdRegistry, maxWidth: 140, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }} title={item.supplier}>{item.supplier}</td>
                                    <td style={tdRegistry}><StatusBadge status={item.status} /></td>
                                    <td style={tdRegistry}>
                                        <button onClick={() => setExpandedId(expandedId === item.id ? null : item.id)}
                                            style={{
                                                padding: '4px 10px', borderRadius: 6, fontSize: 11, fontWeight: 700,
                                                background: expandedId === item.id ? UTM.gold : '#EDE9E4',
                                                color: expandedId === item.id ? UTM.maroon : UTM.gray700,
                                                border: 'none', cursor: 'pointer', whiteSpace: 'nowrap',
                                            }}>
                                            {expandedId === item.id ? 'Tutup' : 'Butiran'}
                                        </button>
                                    </td>
                                </tr>

                                {expandedId === item.id && (
                                    <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                        <td colSpan={14} style={{ padding: 0 }}>
                                            <div style={{ padding: '20px 24px', display: 'flex', flexWrap: 'wrap', gap: 24, borderLeft: `4px solid ${UTM.gold}` }}>
                                                <div style={{ minWidth: 200 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>Time & Date</h4>
                                                    <table style={{ width: '100%', fontSize: 12, lineHeight: 1.8 }}>
                                                        <tbody>
                                                            <tr><td style={{ color: UTM.gray500, width: '50%' }}>Added</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{fmtDT(item.created_at)}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Received</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.received_date && item.received_date !== '—' ? item.received_date : '—'}</td></tr>
                                                            <tr><td style={{ color: UTM.gray500 }}>Verified</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.verified_at ? fmtDT(item.verified_at) : '—'}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style={{ flex: 1, minWidth: 300 }}>
                                                    <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>Full Specification</h4>
                                                    <pre style={{ margin: 0, fontSize: 12, lineHeight: 1.6, whiteSpace: 'pre-wrap', wordBreak: 'break-word', fontFamily: 'inherit', color: UTM.gray700, maxHeight: 200, overflow: 'auto' }}>
                                                        {item.specification || 'No specification available.'}
                                                    </pre>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                                </React.Fragment>
                            ))}
                        </tbody>
                    </table>
                    <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray500, textAlign: 'center' }}>
                        Showing {filtered.length} of {registryItems.length} items
                    </div>
                </div>
            )}

            <div style={{ marginTop: 16, display: 'flex', gap: 16, flexWrap: 'wrap', fontSize: 12, color: UTM.gray500 }}>
                <span>◈ <strong>Master</strong> = Equipment register item</span>
                <span>◈ <strong>DO</strong> = Delivery Order item (received)</span>
                <span>◈ <strong>Aset</strong> = Registered asset (PA-2/PA-3)</span>
            </div>
        </div>
    );
}

// ─── Main Component ────────────────────────────────────────────────────────────
export default function VerificationIndex({ items, stats, categories, filters, registry_items, registry_counts, trashed, can_verify, can_delete, can_force_delete }) {
    const [activeTab, setActiveTab] = useState(filters.tab === 'registry' ? 'registry' : 'pipeline');

    const tabs = [
        { key: 'pipeline',  label: 'Pipeline',  count: stats.total },
        { key: 'registry',  label: 'Registry',  count: registry_counts?.total },
    ];

    const handleTabChange = (key) => {
        setActiveTab(key);
        // Preserve state when switching tabs
        router.get('/verification', { ...filters, tab: key, page: 1 }, { preserveState: true, replace: true });
    };

    return (
        <AuthenticatedLayout>
            <Head title={activeTab === 'pipeline' ? 'Verification Pipeline' : 'Inventory Register'} />

            <div style={{ maxWidth: 1400, margin: '0 auto', padding: '24px 20px' }}>
                <TabBar tabs={tabs} activeTab={activeTab} onTabChange={handleTabChange} />

                {activeTab === 'pipeline' ? (
                    <PipelineView
                        items={items}
                        stats={stats}
                        categories={categories}
                        filters={filters}
                        can_verify={can_verify}
                        can_delete={can_delete}
                        can_force_delete={can_force_delete}
                        trashed={trashed}
                    />
                ) : (
                    <RegistryView
                        registryItems={registry_items || []}
                        registryCounts={registry_counts || {}}
                        filters={filters}
                    />
                )}
            </div>
        </AuthenticatedLayout>
    );
}

// ─── Shared styles ────────────────────────────────────────────────────────────
const thStyle = {
    padding: '10px 12px', textAlign: 'left', fontSize: 11, fontWeight: 700,
    textTransform: 'uppercase', letterSpacing: '0.05em', color: UTM.gray500,
    whiteSpace: 'nowrap',
};
const tdStyle = {
    padding: '10px 12px', color: UTM.gray700, verticalAlign: 'middle',
};
const thRegistry = {
    padding: '10px 12px', textAlign: 'left', fontSize: 11, fontWeight: 700,
    textTransform: 'uppercase', letterSpacing: '0.05em', color: UTM.gray500,
    whiteSpace: 'nowrap',
};
const tdRegistry = {
    padding: '10px 12px', color: UTM.gray700, verticalAlign: 'middle',
};
const inputStyle = {
    width: '100%', padding: '9px 12px', borderRadius: 8,
    border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none', boxSizing: 'border-box',
};
const textareaStyle = {
    width: '100%', padding: '9px 12px', borderRadius: 8,
    border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none', boxSizing: 'border-box',
    fontFamily: 'inherit',
};
const labelStyle = {
    display: 'block', fontSize: 12, fontWeight: 600, color: UTM.gray600, marginBottom: 4,
};
