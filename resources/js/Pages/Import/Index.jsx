import { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ suppliers, preview }) {
    const { flash, errors } = usePage().props;

    const [importAs, setImportAs] = useState('do');
    const [supplierId, setSupplierId] = useState('');
    const [reference, setReference] = useState('');
    const [uploadMethod, setUploadMethod] = useState('file'); // file | paste
    const [file, setFile] = useState(null);
    const [pasted, setPasted] = useState('');
    const [loading, setLoading] = useState(false);

    // Editable items from preview
    const [items, setItems] = useState(preview?.items || null);
    const [importing, setImporting] = useState(false);

    // Sync items state from preview prop when Inertia re-renders (e.g. after preview POST)
    useEffect(() => {
        if (preview?.items) {
            setItems(preview.items);
        } else if (!preview) {
            setItems(null);
        }
    }, [preview]);

    function handleFileChange(e) {
        setFile(e.target.files[0]);
    }

    function handlePreview() {
        if (!file && !pasted.trim()) return;
        setLoading(true);
        const form = new FormData();
        if (file) form.append('file', file);
        if (pasted.trim()) form.append('pasted', pasted);
        form.append('type_hint', 'auto');

        router.post(route('import.preview'), form, {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => setLoading(false),
        });
    }

    function handleItemChange(index, field, value) {
        setItems(prev => {
            const next = [...prev];
            next[index] = { ...next[index], [field]: value };
            return next;
        });
    }

    function handleRemoveItem(index) {
        setItems(prev => prev.filter((_, i) => i !== index));
    }

    function handleConfirm() {
        if (!items || items.length === 0) return;
        setImporting(true);

        router.post(route('import.confirm'), {
            items: items,
            import_as: importAs,
            reference: reference || preview?.metadata?.reference || '',
            supplier_id: supplierId || '',
            supplier_name: preview?.metadata?.supplier || '',
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setItems(null);
                setFile(null);
                setPasted('');
                setImporting(false);
            },
            onError: () => setImporting(false),
        });
    }

    function handleCancel() {
        setItems(null);
        setFile(null);
        setPasted('');
    }

    const hasPreview = preview && items;

    const btnStyle = {
        padding: '10px 24px', border: 'none', borderRadius: 6,
        fontSize: 14, fontWeight: 600, cursor: 'pointer',
    };

    return (
        <AuthenticatedLayout>
            <Head title="Import Data" />
            <div style={{ padding: 24, maxWidth: 1000, margin: '0 auto' }}>

                <h1 style={{ fontSize: 24, fontWeight: 700, marginBottom: 24 }}>
                    Import Procurement Data
                </h1>

                {flash?.success && (
                    <div style={{
                        padding: '12px 16px', background: '#F0FDF4', border: '1px solid #86EFAC',
                        borderRadius: 8, marginBottom: 16, color: '#166534', fontWeight: 500,
                    }}>
                        {flash.success}
                    </div>
                )}

                {flash?.error && (
                    <div style={{
                        padding: '12px 16px', background: '#FEF2F2', border: '1px solid #FECACA',
                        borderRadius: 8, marginBottom: 16, color: '#991B1B', fontWeight: 500,
                    }}>
                        {flash.error}
                    </div>
                )}

                {Object.keys(errors || {}).length > 0 && (
                    <div style={{
                        padding: '12px 16px', background: '#FEF2F2', border: '1px solid #FECACA',
                        borderRadius: 8, marginBottom: 16, color: '#991B1B',
                    }}>
                        <p style={{ fontWeight: 600, margin: '0 0 4px', fontSize: 13 }}>Errors:</p>
                        {Object.values(errors).flat().map((msg, i) => (
                            <p key={i} style={{ margin: 0, fontSize: 12, color: '#DC2626' }}>{msg}</p>
                        ))}
                    </div>
                )}

                {/* Upload Section */}
                {!hasPreview && (
                    <div style={{
                        border: '2px dashed #D1D5DB', borderRadius: 12, padding: 32,
                        background: '#FAFAFA', marginBottom: 24,
                    }}>
                        {/* Import type selector */}
                        <div style={{ display: 'flex', gap: 16, marginBottom: 20, flexWrap: 'wrap' }}>
                            <div>
                                <label style={{ fontWeight: 600, display: 'block', marginBottom: 6, fontSize: 13 }}>
                                    Import As
                                </label>
                                <div style={{ display: 'flex', gap: 8 }}>
                                    <button onClick={() => setImportAs('do')} style={{
                                        ...btnStyle,
                                        background: importAs === 'do' ? '#5C001F' : '#F3F4F6',
                                        color: importAs === 'do' ? '#FFF' : '#374151',
                                    }}>Delivery Order (DO)</button>
                                    <button onClick={() => setImportAs('po')} style={{
                                        ...btnStyle,
                                        background: importAs === 'po' ? '#5C001F' : '#F3F4F6',
                                        color: importAs === 'po' ? '#FFF' : '#374151',
                                    }}>Purchase Order (PO)</button>
                                </div>
                            </div>
                            <div>
                                <label style={{ fontWeight: 600, display: 'block', marginBottom: 6, fontSize: 13 }}>
                                    Supplier
                                </label>
                                <select
                                    value={supplierId}
                                    onChange={e => setSupplierId(e.target.value)}
                                    style={{
                                        padding: '8px 12px', border: '1px solid #D1D5DB',
                                        borderRadius: 6, width: 260, fontSize: 14,
                                    }}
                                >
                                    <option value="">Auto-detect from file</option>
                                    {suppliers?.map(s => (
                                        <option key={s.id} value={s.id}>{s.name}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Method selector */}
                        <div style={{ display: 'flex', gap: 12, marginBottom: 20 }}>
                            <button onClick={() => setUploadMethod('file')} style={{
                                ...btnStyle,
                                background: uploadMethod === 'file' ? '#5C001F' : '#F3F4F6',
                                color: uploadMethod === 'file' ? '#FFF' : '#374151',
                            }}>Upload File</button>
                            <button onClick={() => setUploadMethod('paste')} style={{
                                ...btnStyle,
                                background: uploadMethod === 'paste' ? '#5C001F' : '#F3F4F6',
                                color: uploadMethod === 'paste' ? '#FFF' : '#374151',
                            }}>Paste Text</button>
                        </div>

                        {/* File upload */}
                        {uploadMethod === 'file' && (
                            <div style={{ marginBottom: 16 }}>
                                <input
                                    type="file"
                                    accept=".pdf,.csv,.xls,.xlsx,.txt"
                                    onChange={handleFileChange}
                                    style={{ fontSize: 14 }}
                                />
                                <p style={{ color: '#6B7280', fontSize: 12, marginTop: 8 }}>
                                    Supported: PDF (scanned or digital), CSV, Excel (.xlsx), text files
                                </p>
                            </div>
                        )}

                        {/* Paste text */}
                        {uploadMethod === 'paste' && (
                            <div style={{ marginBottom: 16 }}>
                                <textarea
                                    value={pasted}
                                    onChange={e => setPasted(e.target.value)}
                                    rows={8}
                                    placeholder={`Paste data here. Supports CSV format:\n\nitem_code,description,brand,serial_number,quantity\n1,Office Desktop,DELL,TONKCH4,14\n2,Monitor,DELL,BLZLRC4,14`}
                                    style={{
                                        width: '100%', padding: 12, border: '1px solid #D1D5DB',
                                        borderRadius: 6, fontSize: 13, fontFamily: 'monospace',
                                        resize: 'vertical', boxSizing: 'border-box',
                                    }}
                                />
                            </div>
                        )}

                        <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
                            <button
                                onClick={handlePreview}
                                disabled={(!file && !pasted.trim()) || loading}
                                style={{
                                    ...btnStyle,
                                    background: (!file && !pasted.trim()) ? '#D1D5DB' : '#2563EB',
                                    color: '#FFF',
                                }}
                            >
                                {loading ? 'Processing...' : 'Extract & Preview'}
                            </button>
                            <span style={{ fontSize: 12, color: '#6B7280' }}>
                                Items will be editable before import.
                            </span>
                        </div>
                    </div>
                )}

                {/* Preview Section */}
                {hasPreview && (
                    <div>
                        {/* Summary */}
                        <div style={{
                            background: '#F8FAFC', border: '1px solid #E2E8F0', borderRadius: 8,
                            padding: 16, marginBottom: 20,
                        }}>
                            <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap', alignItems: 'center' }}>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>SOURCE</span>
                                    <div style={{ fontWeight: 600, textTransform: 'uppercase' }}>
                                        {preview.source || 'Unknown'}
                                        {preview.file_name && ` (${preview.file_name})`}
                                    </div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>ITEMS</span>
                                    <div style={{ fontWeight: 600 }}>{items?.length || 0}</div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>IMPORT AS</span>
                                    <div style={{ fontWeight: 600, textTransform: 'uppercase' }}>{importAs}</div>
                                </div>
                                {preview?.metadata?.reference && (
                                    <div>
                                        <span style={{ color: '#64748B', fontSize: 12 }}>REFERENCE</span>
                                        <div style={{ fontWeight: 600 }}>{preview.metadata.reference}</div>
                                    </div>
                                )}
                                {preview?.metadata?.supplier && (
                                    <div>
                                        <span style={{ color: '#64748B', fontSize: 12 }}>SUPPLIER</span>
                                        <div style={{ fontWeight: 600 }}>{preview.metadata.supplier}</div>
                                    </div>
                                )}
                                {preview?.confidence !== undefined && (
                                    <div>
                                        <span style={{ color: '#64748B', fontSize: 12 }}>CONFIDENCE</span>
                                        <div style={{ fontWeight: 600 }}>
                                            <span style={{
                                                color: preview.confidence >= 80 ? '#059669' :
                                                       preview.confidence >= 50 ? '#D97706' : '#DC2626',
                                            }}>
                                                {preview.confidence}%
                                            </span>
                                            {preview.confidence < 80 && (
                                                <span style={{ color: '#6B7280', fontSize: 11, marginLeft: 6 }}>
                                                    (review recommended)
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Low confidence warning */}
                        {preview?.confidence !== undefined && preview.confidence < 80 && (
                            <div style={{
                                background: preview.confidence < 50 ? '#FEF2F2' : '#FFFBEB',
                                border: `1px solid ${preview.confidence < 50 ? '#FECACA' : '#FDE68A'}`,
                                borderRadius: 8, padding: '12px 16px', marginBottom: 20,
                            }}>
                                <span style={{
                                    fontWeight: 600, fontSize: 14,
                                    color: preview.confidence < 50 ? '#991B1B' : '#92400E',
                                }}>
                                    {preview.confidence < 50 ? '⚠ Low Confidence' : '⚠ Medium Confidence'}
                                </span>
                                <p style={{ margin: '4px 0 0', fontSize: 13, color: '#6B7280' }}>
                                    The OCR had difficulty reading this document accurately.
                                    Please review each item below.
                                    {preview.raw_text && ' Use the raw text section to copy any missing items.'}
                                </p>
                            </div>
                        )}

                        {/* Raw text (collapsible) */}
                        {preview?.raw_text && (
                            <details style={{ marginBottom: 16 }}>
                                <summary style={{
                                    cursor: 'pointer', fontWeight: 600, fontSize: 13,
                                    color: '#6B7280', padding: '8px 0', userSelect: 'none',
                                }}>
                                    Show Raw OCR Text (for manual reference)
                                </summary>
                                <pre style={{
                                    background: '#F3F4F6', border: '1px solid #E5E7EB',
                                    borderRadius: 6, padding: 12, fontSize: 11,
                                    fontFamily: 'monospace', whiteSpace: 'pre-wrap',
                                    wordBreak: 'break-word', maxHeight: 300, overflow: 'auto',
                                    lineHeight: 1.5,
                                }}>{preview.raw_text}</pre>
                            </details>
                        )}

                        {/* Reference override */}
                        <div style={{ marginBottom: 16 }}>
                            <label style={{ fontSize: 12, fontWeight: 600, color: '#374151', display: 'block', marginBottom: 4 }}>
                                Reference Number (optional override)
                            </label>
                            <input
                                type="text"
                                value={reference}
                                onChange={e => setReference(e.target.value)}
                                placeholder={preview?.metadata?.reference || 'Auto-generated if empty'}
                                style={{
                                    padding: '8px 12px', border: '1px solid #D1D5DB',
                                    borderRadius: 6, width: 400, fontSize: 13,
                                }}
                            />
                        </div>

                        {/* Items Table */}
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                                <thead>
                                    <tr style={{ borderBottom: '2px solid #E5E7EB', background: '#F9FAFB' }}>
                                        <th style={thStyle}>#</th>
                                        <th style={thStyle}>Code</th>
                                        <th style={{ ...thStyle, minWidth: 180 }}>Description</th>
                                        <th style={thStyle}>Brand</th>
                                        <th style={thStyle}>Model</th>
                                        <th style={thStyle}>S/N</th>
                                        <th style={thStyle}>Category</th>
                                        <th style={thStyle}>Qty</th>
                                        <th style={thStyle}>Price</th>
                                        <th style={thStyle}></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.map((item, idx) => (
                                        <tr key={idx} style={{
                                            borderBottom: '1px solid #E5E7EB',
                                            background: idx % 2 === 0 ? '#FFF' : '#F9FAFB',
                                        }}>
                                            <td style={tdStyle}>{idx + 1}</td>
                                            <td style={tdStyle}>
                                                <input value={item.item_code || ''}
                                                    onChange={e => handleItemChange(idx, 'item_code', e.target.value)}
                                                    style={inputStyle} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input value={item.description || ''}
                                                    onChange={e => handleItemChange(idx, 'description', e.target.value)}
                                                    style={{ ...inputStyle, minWidth: 160 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input value={item.brand || ''}
                                                    onChange={e => handleItemChange(idx, 'brand', e.target.value)}
                                                    style={{ ...inputStyle, width: 70 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input value={item.model || ''}
                                                    onChange={e => handleItemChange(idx, 'model', e.target.value)}
                                                    style={{ ...inputStyle, width: 70 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input value={item.serial_number || ''}
                                                    onChange={e => handleItemChange(idx, 'serial_number', e.target.value)}
                                                    style={{ ...inputStyle, width: 90, fontFamily: 'monospace', fontSize: 11 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input value={item.category || ''}
                                                    onChange={e => handleItemChange(idx, 'category', e.target.value)}
                                                    style={{ ...inputStyle, width: 80 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input type="number" min="1" value={item.quantity_ordered || 1}
                                                    onChange={e => handleItemChange(idx, 'quantity_ordered', parseInt(e.target.value) || 1)}
                                                    style={{ ...inputStyle, width: 50 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <input type="number" step="0.01" min="0"
                                                    value={item.unit_price ?? ''}
                                                    onChange={e => handleItemChange(idx, 'unit_price', e.target.value ? parseFloat(e.target.value) : null)}
                                                    style={{ ...inputStyle, width: 70 }} />
                                            </td>
                                            <td style={tdStyle}>
                                                <button onClick={() => handleRemoveItem(idx)}
                                                    style={{
                                                        padding: '4px 8px', background: '#FEF2F2',
                                                        border: '1px solid #FECACA', borderRadius: 4,
                                                        color: '#991B1B', cursor: 'pointer', fontSize: 12,
                                                    }}>
                                                    x
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Actions */}
                        <div style={{ marginTop: 20, display: 'flex', gap: 12, justifyContent: 'flex-end', flexWrap: 'wrap' }}>
                            <button onClick={() => {
                                setItems(prev => [...prev, {
                                    item_code: '', description: '', brand: '',
                                    model: '', serial_number: '', category: '',
                                    quantity_ordered: 1, unit: 'unit', unit_price: null,
                                }]);
                            }} style={{
                                padding: '10px 20px', background: '#EFF6FF',
                                border: '1px solid #BFDBFE', borderRadius: 6,
                                fontSize: 14, cursor: 'pointer', color: '#1E40AF',
                                fontWeight: 500,
                            }}>
                                + Add Row
                            </button>
                            <button onClick={handleCancel} style={{
                                padding: '10px 20px', background: '#FFF',
                                border: '1px solid #D1D5DB', borderRadius: 6, fontSize: 14, cursor: 'pointer',
                            }}>
                                Cancel
                            </button>
                            <button onClick={handleConfirm} disabled={importing} style={{
                                padding: '10px 24px',
                                background: importing ? '#D1D5DB' : '#059669',
                                color: '#FFF', border: 'none', borderRadius: 6,
                                fontSize: 14, fontWeight: 600,
                                cursor: importing ? 'default' : 'pointer',
                            }}>
                                {importing ? 'Importing...' : `Import ${items?.length || 0} Items as ${importAs.toUpperCase()}`}
                            </button>
                        </div>
                    </div>
                )}

            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '10px 8px', textAlign: 'left', fontWeight: 600, fontSize: 12,
    color: '#374151', whiteSpace: 'nowrap',
};
const tdStyle = {
    padding: '6px 8px', verticalAlign: 'top',
};
const inputStyle = {
    padding: '4px 6px', border: '1px solid #D1D5DB', borderRadius: 4,
    fontSize: 12, width: '100%', boxSizing: 'border-box', background: '#FFF',
};
