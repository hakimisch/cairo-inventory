import { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ suppliers, preview, file_name }) {
    const { errors, flash } = usePage().props;

    const [file, setFile] = useState(null);
    const [docType, setDocType] = useState('auto');
    const [supplierId, setSupplierId] = useState('');
    const [uploading, setUploading] = useState(false);

    // Editable items from preview
    const [items, setItems] = useState(preview?.items || null);
    const [importing, setImporting] = useState(false);

    function handleFileChange(e) {
        setFile(e.target.files[0]);
    }

    function handlePreview() {
        if (!file) return;
        setUploading(true);
        const form = new FormData();
        form.append('file', file);
        form.append('type', docType);
        router.post(route('pdf-import.preview'), form, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => setUploading(false),
            onError: () => setUploading(false),
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

        router.post(route('pdf-import.confirm'), {
            items: items,
            document_type: docType === 'auto' ? (preview?.document_type || 'po') : docType,
            reference: preview?.reference || '',
            supplier_id: supplierId || '',
            supplier_name: preview?.supplier || '',
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setItems(null);
                setFile(null);
                setImporting(false);
            },
            onError: () => setImporting(false),
        });
    }

    function handleCancel() {
        setItems(null);
        setFile(null);
    }

    const hasPreview = preview && items;

    return (
        <AuthenticatedLayout>
            <Head title="PDF Import" />
            <div style={{ padding: 24, maxWidth: 1200, margin: '0 auto' }}>

                <h1 style={{ fontSize: 24, fontWeight: 700, marginBottom: 24 }}>
                    Import PDF Document
                </h1>

                {flash?.success && (
                    <div style={{
                        padding: '12px 16px', background: '#F0FDF4', border: '1px solid #86EFAC',
                        borderRadius: 8, marginBottom: 16, color: '#166534', fontWeight: 500,
                    }}>
                        {flash.success}
                    </div>
                )}

                {errors?.file && (
                    <div style={{
                        padding: '12px 16px', background: '#FEF2F2', border: '1px solid #FECACA',
                        borderRadius: 8, marginBottom: 16, color: '#991B1B',
                    }}>
                        {errors.file}
                    </div>
                )}

                {/* Upload Section */}
                {!hasPreview && (
                    <div style={{
                        border: '2px dashed #D1D5DB', borderRadius: 12, padding: 32,
                        background: '#FAFAFA', marginBottom: 24,
                    }}>
                        <div style={{ marginBottom: 16 }}>
                            <label style={{ fontWeight: 600, display: 'block', marginBottom: 6 }}>
                                Document Type
                            </label>
                            <select
                                value={docType}
                                onChange={e => setDocType(e.target.value)}
                                style={{
                                    padding: '8px 12px', border: '1px solid #D1D5DB',
                                    borderRadius: 6, width: 280, fontSize: 14,
                                }}
                            >
                                <option value="auto">Auto-detect</option>
                                <option value="po">Purchase Order (PO)</option>
                                <option value="do">Delivery Order (DO)</option>
                                <option value="quotation">Quotation / Price Schedule</option>
                            </select>
                        </div>

                        <div style={{ marginBottom: 16 }}>
                            <label style={{ fontWeight: 600, display: 'block', marginBottom: 6 }}>
                                Supplier (optional)
                            </label>
                            <select
                                value={supplierId}
                                onChange={e => setSupplierId(e.target.value)}
                                style={{
                                    padding: '8px 12px', border: '1px solid #D1D5DB',
                                    borderRadius: 6, width: 280, fontSize: 14,
                                }}
                            >
                                <option value="">Auto-detect from document</option>
                                {suppliers?.map(s => (
                                    <option key={s.id} value={s.id}>{s.name}</option>
                                ))}
                            </select>
                        </div>

                        <div style={{ marginBottom: 16 }}>
                            <label style={{ fontWeight: 600, display: 'block', marginBottom: 6 }}>
                                PDF File
                            </label>
                            <input
                                type="file"
                                accept=".pdf"
                                onChange={handleFileChange}
                                style={{ fontSize: 14 }}
                            />
                        </div>

                        <button
                            onClick={handlePreview}
                            disabled={!file || uploading}
                            style={{
                                padding: '10px 24px', background: !file ? '#D1D5DB' : '#2563EB',
                                color: '#FFF', border: 'none', borderRadius: 6,
                                fontSize: 14, fontWeight: 600, cursor: !file ? 'default' : 'pointer',
                            }}
                        >
                            {uploading ? 'Processing...' : 'Upload & Extract'}
                        </button>

                        <p style={{ color: '#6B7280', fontSize: 13, marginTop: 12 }}>
                            Supported: scanned PDFs (CCITT/G4) and digital PDFs.
                            Extracts item names, quantities, prices, and serial numbers.
                        </p>
                    </div>
                )}

                {/* Preview Section */}
                {hasPreview && (
                    <div>
                        {/* Document Summary */}
                        <div style={{
                            background: '#F8FAFC', border: '1px solid #E2E8F0', borderRadius: 8,
                            padding: 16, marginBottom: 20,
                        }}>
                            <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap' }}>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>DOCUMENT</span>
                                    <div style={{ fontWeight: 600 }}>{preview?.reference || 'Unknown'}</div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>TYPE</span>
                                    <div style={{ fontWeight: 600 }}>
                                        <span style={{
                                            display: 'inline-block', padding: '2px 8px', borderRadius: 4,
                                            fontSize: 12, fontWeight: 600,
                                            background: preview?.document_type === 'po' ? '#EFF6FF' :
                                                       preview?.document_type === 'do' ? '#F0FDF4' :
                                                       '#FEF3C7',
                                            color: preview?.document_type === 'po' ? '#1E40AF' :
                                                   preview?.document_type === 'do' ? '#166534' :
                                                   '#92400E',
                                        }}>
                                            {preview?.document_type?.toUpperCase() || 'UNKNOWN'}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>SUPPLIER</span>
                                    <div style={{ fontWeight: 600 }}>{preview?.supplier || 'Auto-detected'}</div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>DATE</span>
                                    <div style={{ fontWeight: 600 }}>{preview?.date || 'N/A'}</div>
                                </div>
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>ITEMS</span>
                                    <div style={{ fontWeight: 600 }}>{items?.length || 0}</div>
                                </div>
                                {preview?.total_amount && (
                                    <div>
                                        <span style={{ color: '#64748B', fontSize: 12 }}>TOTAL</span>
                                        <div style={{ fontWeight: 600 }}>
                                            RM {Number(preview.total_amount).toLocaleString('en-MY', { minimumFractionDigits: 2 })}
                                        </div>
                                    </div>
                                )}
                                <div>
                                    <span style={{ color: '#64748B', fontSize: 12 }}>METHOD</span>
                                    <div style={{
                                        fontWeight: 600, fontSize: 13,
                                        color: preview?.method === 'digital' ? '#166534' : '#92400E',
                                    }}>
                                        {preview?.method === 'digital' ? 'Digital (direct)' : 'OCR'}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Items Table */}
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{
                                width: '100%', borderCollapse: 'collapse', fontSize: 13,
                            }}>
                                <thead>
                                    <tr style={{ borderBottom: '2px solid #E5E7EB', background: '#F9FAFB' }}>
                                        <th style={thStyle}>#</th>
                                        <th style={thStyle}>Code</th>
                                        <th style={{ ...thStyle, minWidth: 200 }}>Description</th>
                                        <th style={thStyle}>Brand</th>
                                        <th style={thStyle}>Model</th>
                                        <th style={thStyle}>Category</th>
                                        <th style={thStyle}>Qty</th>
                                        <th style={thStyle}>Unit</th>
                                        <th style={thStyle}>Unit Price</th>
                                        <th style={thStyle}>Actions</th>
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
                                                <input
                                                    value={item.item_code || ''}
                                                    onChange={e => handleItemChange(idx, 'item_code', e.target.value)}
                                                    style={inputStyle}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    value={item.description || ''}
                                                    onChange={e => handleItemChange(idx, 'description', e.target.value)}
                                                    style={{ ...inputStyle, minWidth: 180 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    value={item.brand || ''}
                                                    onChange={e => handleItemChange(idx, 'brand', e.target.value)}
                                                    style={{ ...inputStyle, width: 80 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    value={item.model || ''}
                                                    onChange={e => handleItemChange(idx, 'model', e.target.value)}
                                                    style={{ ...inputStyle, width: 80 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    value={item.category || ''}
                                                    onChange={e => handleItemChange(idx, 'category', e.target.value)}
                                                    style={{ ...inputStyle, width: 100 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    type="number"
                                                    min="1"
                                                    value={item.quantity_ordered || 1}
                                                    onChange={e => handleItemChange(idx, 'quantity_ordered', parseInt(e.target.value) || 1)}
                                                    style={{ ...inputStyle, width: 50 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    value={item.unit || 'unit'}
                                                    onChange={e => handleItemChange(idx, 'unit', e.target.value)}
                                                    style={{ ...inputStyle, width: 50 }}
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={item.unit_price || ''}
                                                    onChange={e => handleItemChange(idx, 'unit_price', e.target.value ? parseFloat(e.target.value) : null)}
                                                    style={{ ...inputStyle, width: 80 }}
                                                    placeholder="-"
                                                />
                                            </td>
                                            <td style={tdStyle}>
                                                <button
                                                    onClick={() => handleRemoveItem(idx)}
                                                    style={{
                                                        padding: '4px 8px', background: '#FEF2F2',
                                                        border: '1px solid #FECACA', borderRadius: 4,
                                                        color: '#991B1B', cursor: 'pointer', fontSize: 12,
                                                    }}
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Action Buttons */}
                        <div style={{
                            marginTop: 20, display: 'flex', gap: 12,
                            justifyContent: 'flex-end',
                        }}>
                            <button
                                onClick={handleCancel}
                                style={{
                                    padding: '10px 20px', background: '#FFF',
                                    border: '1px solid #D1D5DB', borderRadius: 6,
                                    fontSize: 14, cursor: 'pointer',
                                }}
                            >
                                Cancel
                            </button>
                            <button
                                onClick={handleConfirm}
                                disabled={importing}
                                style={{
                                    padding: '10px 24px',
                                    background: importing ? '#D1D5DB' : '#059669',
                                    color: '#FFF', border: 'none', borderRadius: 6,
                                    fontSize: 14, fontWeight: 600,
                                    cursor: importing ? 'default' : 'pointer',
                                }}
                            >
                                {importing ? 'Importing...' : `Import ${items?.length || 0} Items`}
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
    fontSize: 12, width: '100%', boxSizing: 'border-box',
    background: '#FFF',
};
