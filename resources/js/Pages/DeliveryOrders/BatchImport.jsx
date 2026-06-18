import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    for (let i = 0; i < line.length; i++) {
        const ch = line[i];
        if (ch === '"') {
            inQuotes = !inQuotes;
        } else if (ch === ',' && !inQuotes) {
            result.push(current.trim());
            current = '';
        } else {
            current += ch;
        }
    }
    result.push(current.trim());
    return result;
}

function parsePastedText(text) {
    const lines = text.trim().split('\n').filter(l => l.trim());
    if (lines.length < 2) return { items: [], errors: ['Need at least a header row + 1 data row'] };

    const headers = parseCSVLine(lines[0]).map(h => h.toLowerCase().replace(/[^a-z0-9]/g, ''));
    const colMap = {};
    const expected = ['description', 'category', 'brand', 'model', 'serial_number', 'quantity', 'qty', 'unit', 'item_code'];

    headers.forEach((h, i) => {
        if (h.includes('description') || h.includes('item') || h.includes('name') || h.includes('product')) colMap.description = i;
        else if (h.includes('category')) colMap.category = i;
        else if (h.includes('brand')) colMap.brand = i;
        else if (h.includes('model')) colMap.model = i;
        else if (h.includes('serial') || h.includes('sn') || h.includes('s/n')) colMap.serial_number = i;
        else if (h.includes('quantity') || h.includes('qty') || h.includes('order')) colMap.quantity_ordered = i;
        else if (h.includes('unit')) colMap.unit = i;
        else if (h.includes('code') || h.includes('itemcode')) colMap.item_code = i;
    });

    if (colMap.description === undefined) {
        return { items: [], errors: ['Could not find a "description" column. Expected headers like: description, category, brand, model, serial_number, quantity, unit'] };
    }

    const items = [];
    const errors = [];

    for (let r = 1; r < lines.length; r++) {
        const cols = parseCSVLine(lines[r]);
        const desc = (cols[colMap.description] || '').trim();
        if (!desc) {
            errors.push(`Row ${r + 1}: skipped (empty description)`);
            continue;
        }
        items.push({
            description: desc,
            category: colMap.category !== undefined ? (cols[colMap.category] || '').trim() : '',
            brand: colMap.brand !== undefined ? (cols[colMap.brand] || '').trim() : '',
            model: colMap.model !== undefined ? (cols[colMap.model] || '').trim() : '',
            serial_number: colMap.serial_number !== undefined ? (cols[colMap.serial_number] || '').trim() : '',
            quantity_ordered: colMap.quantity_ordered !== undefined ? parseInt(cols[colMap.quantity_ordered]) || 1 : 1,
            unit: colMap.unit !== undefined ? (cols[colMap.unit] || '').trim() : 'unit',
            item_code: colMap.item_code !== undefined ? (cols[colMap.item_code] || '').trim() : '',
        });
    }

    return { items, errors };
}

const DOTCOM_TEMPLATE = `description,brand,model,serial_number,quantity,category
POE Switch 16-port,HIKVISION,DS-3E1318P,,1,Security Access For Office
POE Switch 8-port,HIKVISION,DS-3E1310P-EI/M,"GQ7650300, GQ7650349",2,Security Access For Office
Portable PA System,SHURE,SLXD14/153T,"405566544000160844, 405566544000161292",2,Multimedia Equipments For Office
Handheld Video Camera,DJI,OSMO POCKET 3,,5,Multimedia Equipments For Office
Smart TV 76 inch,SAMSUNG,LH75WMBWLGC,OLLJHNLL200009,1,Multimedia Equipments For Office
Smart TV 65 inch,SAMSUNG,LH65WMBWBGC,"OLY7HNJLZ00005, OLY7HNJXBO00006",2,Multimedia Equipments For Office
All-in-one Printer,EPSON,ECOTANK L3250,,1,Support Equipments For Office
Transparent Display Unit,IKEA,MILSBO,,1,Support Equipments For Office
Desktop PC,DELL,QBS1250,"TONKCH4, IINKCH4, 40NKCH4, 41NKCH4, 50NKCH4, TZZMKCH4, SZMKCH4, BZMKCH4, CZMKCH4, DHNKCH4, FONKCH4, GHNKCH4, HHNKCH4",12,ICT Equipment For Office
Standard Monitor,DELL,E2425HSM,"BLZLRC4, BM1LRC4, BQDMRC4, BQGLRC4, BOHKRC4, BQHMRC4, BOLLRC4, BQNKRC4, C2DKRC4, FXCMRC4, FXDKRC4, FXFMRC4, FKHMRC4, FXJLRC4",14,ICT Equipment For Office
Laptop ThinkPad P16s,LENOVO,ThinkPad P16s,"PF5ZR5BL, PF5ZR9M6, PF5ZSGH0, PF5ZSGH8",4,ICT Equipment For Office
Modular Cold Storage,TOSHIBA,GR-RF531WI-PMY,A07DZ-0058,1,Support Equipments For Office
Compact Microwave,PANASONIC,NN-ST34NBMPQ,5D95250354,1,Support Equipments For Office
3D Printer X1 Carbon,BAMBU LAB,X1 CARBON,"OOMO09D561500960, 0OM08D561501301",2,Smart Manufacturing Lab
Server Rack 42U,Vertiv,VE Rack SR-V081220SF,,1,Smart Manufacturing Lab
KVM Switch,Vertiv,CLRA19KMM,21023300512244020043,1,Smart Manufacturing Lab
NAS,SYNOLOGY,FS3410,2610UNRQC6Z64,1,Smart Manufacturing Lab
Vision COBOT System,ROKEA,CR12-12/1.4,"C600U00648 (Controller), X600TZ00023 (Robot)",1,Robotic Equipment
UPS,NEUROPOWER,GALLEONPRO III 20K,85222511500194,1,Robotic Equipment
Automatic Sorter Machine,DAMON,ASM,,1,Robotic Equipment`;

export default function BatchImport({ deliveryOrder }) {
    const [pastedText, setPastedText] = useState('');
    const [parsed, setParsed] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [result, setResult] = useState(null);
    const [csvFile, setCsvFile] = useState(null);

    function handleParse() {
        const result = parsePastedText(pastedText);
        setParsed(result);
        setResult(null);
    }

    function handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        setCsvFile(file);
        const reader = new FileReader();
        reader.onload = (ev) => {
            const text = ev.target.result;
            setPastedText(text);
            const result = parsePastedText(text);
            setParsed(result);
            setResult(null);
        };
        reader.readAsText(file);
    }

    function loadTemplate() {
        setPastedText(DOTCOM_TEMPLATE);
        setCsvFile(null);
        setParsed(null);
        setResult(null);
    }

    function handleSubmit() {
        if (!parsed || parsed.items.length === 0) return;
        setIsSubmitting(true);
        router.post(route('delivery-orders.batch-import', deliveryOrder.id), {
            items: parsed.items,
        }, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                setResult({ type: 'success', count: parsed.items.length, errors: parsed.errors });
                setParsed(null);
                setPastedText('');
            },
            onError: (errs) => {
                setResult({ type: 'error', message: Object.values(errs).join(', ') });
                setIsSubmitting(false);
            },
        });
    }

    return (
        <AuthenticatedLayout>
            <Head title="Batch Import Line Items" />
            <div style={{ padding: 24, maxWidth: 900, margin: '0 auto' }}>
                {/* ── Header ───────────────────────────────────────────────── */}
                <div style={{ marginBottom: 20 }}>
                    <Link href={route('delivery-orders.show', deliveryOrder.id)}
                          style={{ color: '#5C001F', fontSize: 13, textDecoration: 'none' }}>
                        ← Back to {deliveryOrder.do_no}
                    </Link>
                    <h1 style={{ fontSize: 22, fontWeight: 700, margin: '8px 0 4px' }}>Batch Import Line Items</h1>
                    <p style={{ color: '#6B7280', fontSize: 13, margin: 0 }}>
                        Paste DO data, upload CSV, or load the DOTCOM template &mdash; all items are imported at once.
                    </p>
                </div>

                {/* ── Result Banner ────────────────────────────────────────── */}
                {result && (
                    <div style={{
                        padding: 14, borderRadius: 8, marginBottom: 20,
                        backgroundColor: result.type === 'success' ? '#F0FDF4' : '#FEF2F2',
                        color: result.type === 'success' ? '#166534' : '#DC2626',
                        border: `1px solid ${result.type === 'success' ? '#BBF7D0' : '#FECACA'}`,
                        fontSize: 13,
                    }}>
                        {result.type === 'success' ? (
                            <>
                                ✅ <strong>{result.count} items</strong> imported successfully to <strong>{deliveryOrder.do_no}</strong>.
                                {result.errors?.length > 0 && (
                                    <div style={{ marginTop: 6, fontSize: 12, color: '#92400E' }}>
                                        {result.errors.length} warnings (skipped rows):<br />
                                        {result.errors.map((e, i) => <div key={i}>{e}</div>)}
                                    </div>
                                )}
                            </>
                        ) : (
                            <>❌ Error: {result.message}</>
                        )}
                    </div>
                )}

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 20 }}>
                    {/* ── Left: DOTCOM Template ────────────────────────────── */}
                    <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20 }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 8px', color: '#374151' }}>Load DOTCOM Template</h2>
                        <p style={{ fontSize: 12, color: '#6B7280', margin: '0 0 12px' }}>
                            Pre-fills ~20 sample items from the actual DOTCOM DO (UTMCAIRO/26/005).
                        </p>
                        <button onClick={loadTemplate}
                            style={{
                                padding: '8px 16px', backgroundColor: '#5C001F', color: '#fff',
                                border: 'none', borderRadius: 6, fontSize: 12, fontWeight: 600, cursor: 'pointer',
                            }}>
                            Load DOTCOM DO Template
                        </button>
                    </div>

                    {/* ── Right: CSV Upload ────────────────────────────────── */}
                    <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20 }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 8px', color: '#374151' }}>Upload CSV File</h2>
                        <p style={{ fontSize: 12, color: '#6B7280', margin: '0 0 12px' }}>
                            CSV with headers: description, category, brand, model, serial_number, quantity, unit
                        </p>
                        <input type="file" accept=".csv,.tsv,.txt" onChange={handleFileUpload}
                            style={{ fontSize: 12 }} />
                    </div>
                </div>

                {/* ── Text Input ───────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 20 }}>
                    <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 8px', color: '#374151' }}>Or Paste DO Data</h2>
                    <p style={{ fontSize: 12, color: '#6B7280', margin: '0 0 12px' }}>
                        Paste CSV/TSV data below. First row must be headers. Columns: description (required), category, brand, model, serial_number, quantity, unit
                    </p>
                    <textarea
                        value={pastedText}
                        onChange={(e) => { setPastedText(e.target.value); setParsed(null); setResult(null); }}
                        placeholder={`description,brand,model,serial_number,quantity,category\nPOE Switch 16-port,HIKVISION,DS-3E1318P,,1,Security Access\nDesktop PC,DELL,QCM1250,SN001,1,ICT Equipment`}
                        rows={10}
                        style={{
                            width: '100%', padding: 12, borderRadius: 8, border: '1px solid #D1D5DB',
                            fontSize: 12, fontFamily: 'monospace', resize: 'vertical', boxSizing: 'border-box',
                        }}
                    />
                    <div style={{ display: 'flex', gap: 8, marginTop: 12 }}>
                        <button onClick={handleParse} disabled={!pastedText.trim()}
                            style={{
                                padding: '8px 20px', backgroundColor: '#5C001F', color: '#fff',
                                border: 'none', borderRadius: 6, fontSize: 13, fontWeight: 600,
                                cursor: pastedText.trim() ? 'pointer' : 'not-allowed', opacity: pastedText.trim() ? 1 : 0.5,
                            }}>
                            Preview Items
                        </button>
                    </div>
                </div>

                {/* ── Preview Table ────────────────────────────────────────── */}
                {parsed && (
                    <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden', marginBottom: 20 }}>
                        <div style={{ padding: '14px 20px', borderBottom: '1px solid #E5E7EB', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <span style={{ fontSize: 13, fontWeight: 600, color: '#374151' }}>
                                Preview: {parsed.items.length} items parsed
                                {parsed.errors.length > 0 && (
                                    <span style={{ color: '#92400E', marginLeft: 8, fontSize: 12 }}>
                                        ({parsed.errors.length} warnings)
                                    </span>
                                )}
                            </span>
                            <button onClick={handleSubmit} disabled={isSubmitting || parsed.items.length === 0}
                                style={{
                                    padding: '8px 20px', backgroundColor: '#166534', color: '#fff',
                                    border: 'none', borderRadius: 6, fontSize: 13, fontWeight: 600,
                                    cursor: isSubmitting || parsed.items.length === 0 ? 'not-allowed' : 'pointer',
                                    opacity: isSubmitting || parsed.items.length === 0 ? 0.5 : 1,
                                }}>
                                {isSubmitting ? 'Importing...' : `Import ${parsed.items.length} Items →`}
                            </button>
                        </div>

                        {/* Parsing errors */}
                        {parsed.errors.length > 0 && (
                            <div style={{ padding: '10px 20px', backgroundColor: '#FFFBEB', borderBottom: '1px solid #FDE68A', fontSize: 12, color: '#92400E' }}>
                                {parsed.errors.map((e, i) => <div key={i}>⚠ {e}</div>)}
                            </div>
                        )}

                        {/* Items preview */}
                        <div style={{ maxHeight: 400, overflow: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                                <thead>
                                    <tr style={{ backgroundColor: '#F9FAFB', position: 'sticky', top: 0 }}>
                                        <th style={thStyle}>#</th>
                                        <th style={thStyle}>Description</th>
                                        <th style={thStyle}>Category</th>
                                        <th style={thStyle}>Brand</th>
                                        <th style={thStyle}>Model</th>
                                        <th style={thStyle}>Serial No.</th>
                                        <th style={thStyle}>Qty</th>
                                        <th style={thStyle}>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {parsed.items.map((item, i) => (
                                        <tr key={i} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                            <td style={tdStyle}>{i + 1}</td>
                                            <td style={tdStyle}>
                                                <div style={{ fontWeight: 600 }}>{item.description}</div>
                                                {item.item_code && <div style={{ fontSize: 11, color: '#9CA3AF' }}>Code: {item.item_code}</div>}
                                            </td>
                                            <td style={tdStyle}>{item.category || '—'}</td>
                                            <td style={tdStyle}>{item.brand || '—'}</td>
                                            <td style={tdStyle}>{item.model || '—'}</td>
                                            <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 11, maxWidth: 180, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                {item.serial_number || '—'}
                                            </td>
                                            <td style={tdStyle}>{item.quantity_ordered}</td>
                                            <td style={tdStyle}>{item.unit}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}

                {/* ── Format Guide ─────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#F9FAFB', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20 }}>
                    <h3 style={{ fontSize: 13, fontWeight: 600, margin: '0 0 8px', color: '#374151' }}>CSV Format Guide</h3>
                    <div style={{ fontSize: 12, color: '#6B7280', lineHeight: 1.6 }}>
                        <p style={{ margin: '0 0 4px' }}><strong>Required column:</strong> description</p>
                        <p style={{ margin: '0 0 4px' }}><strong>Optional columns:</strong> category, brand, model, serial_number, quantity, unit, item_code</p>
                        <p style={{ margin: 0 }}>Column headers are matched flexibly — "name/product" → description, "qty/quantity_ordered" → quantity, "sn/s/n" → serial_number</p>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '8px 10px',
    textAlign: 'left',
    fontWeight: 600,
    fontSize: 11,
    color: '#6B7280',
    textTransform: 'uppercase',
    letterSpacing: '0.05em',
};
const tdStyle = {
    padding: '8px 10px',
    verticalAlign: 'middle',
};
