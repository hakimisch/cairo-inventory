import React from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa30({ asset, lossReport }) {
    const fr = lossReport.final_loss_report || {};
    const lr = lossReport;

    const { data, setData, post, processing, errors } = useForm({
        // Section 1: Asset Details
        asset_tag_no: fr.asset_tag_no ?? lr.asset?.asset_tag ?? '',
        asset_description: fr.asset_description ?? lr.asset?.name ?? '',
        asset_category: fr.asset_category ?? lr.asset?.category ?? '',
        asset_serial_no: fr.asset_serial_no ?? lr.asset?.serial_number ?? '',
        asset_location_registered: fr.asset_location_registered ?? lr.asset?.location ?? '',
        last_custodian: fr.last_custodian ?? lr.last_officer ?? '',

        // Section 2: Loss Description
        incident_description: fr.incident_description ?? lr.notes ?? '',
        incident_date: fr.incident_date ?? lr.loss_date ?? '',
        incident_time: fr.incident_time ?? '',
        incident_location_details: fr.incident_location_details ?? lr.incident_location ?? '',
        incident_circumstances: fr.incident_circumstances ?? lr.loss_method ?? '',

        // Section 3: Police Findings
        police_investigation_findings: fr.police_investigation_findings ?? '',
        police_officer_name: fr.police_officer_name ?? '',
        police_station: fr.police_station ?? '',
        police_report_ref: fr.police_report_ref ?? lr.police_report_no ?? '',

        // Section 4: Witness Statements
        witness_1_statement: fr.witness_1_statement ?? '',
        witness_1_name: fr.witness_1_name ?? '',
        witness_2_statement: fr.witness_2_statement ?? '',
        witness_2_name: fr.witness_2_name ?? '',

        // Section 5: Procedural Compliance
        complied_with_procedures: fr.complied_with_procedures ?? null,
        procedural_compliance_notes: fr.procedural_compliance_notes ?? '',
        procedural_gaps: fr.procedural_gaps ?? '',

        // Section 6: Prevention Steps
        prevention_actions_taken: fr.prevention_actions_taken ?? '',
        prevention_recommendations: fr.prevention_recommendations ?? '',

        // Section 7: Investigation Summary
        investigation_conclusion: fr.investigation_conclusion ?? '',

        // Section 8: Recommendation
        recommended_action: fr.recommended_action ?? '',
        recommendation_rationale: fr.recommendation_rationale ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('assets.loss-reports.kewpa30.store', [asset.id, lossReport.id]), {
            preserveScroll: true,
        });
    };

    const sectionBox = { background: '#fff', borderRadius: 8, border: '1px solid #e5e7eb', marginBottom: 16, overflow: 'hidden' };
    const sectionHeader = { background: '#5C001F', color: '#fff', padding: '10px 16px', fontSize: 13, fontWeight: 700, textTransform: 'uppercase' };
    const sectionBody = { padding: 16 };
    const fieldRow = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 12 };
    const fullRow = { marginBottom: 12 };
    const labelStyle = { display: 'block', fontSize: 11, fontWeight: 600, color: '#4B5563', marginBottom: 4, textTransform: 'uppercase', letterSpacing: '0.05em' };
    const inputStyle = { width: '100%', padding: '8px 10px', borderRadius: 6, border: '1.5px solid #e5e7eb', fontSize: 13, color: '#1F2937', background: '#fff', outline: 'none', boxSizing: 'border-box' };
    const textareaStyle = { ...inputStyle, minHeight: 80, resize: 'vertical' };
    const selectStyle = { ...inputStyle };

    const InputField = ({ label, field, type = 'text', step, options }) => (
        <div>
            <label style={labelStyle}>{label}</label>
            {options ? (
                <select style={selectStyle} value={data[field] ?? ''} onChange={e => setData(field, e.target.value)}>
                    <option value="">— Pilih —</option>
                    {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
            ) : (
                <input type={type} step={step} style={inputStyle} value={data[field] ?? ''}
                    onChange={e => setData(field, type === 'checkbox' ? e.target.checked : e.target.value)} />
            )}
            {errors[field] && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 2 }}>{errors[field]}</p>}
        </div>
    );

    const TextareaField = ({ label, field }) => (
        <div style={fullRow}>
            <label style={labelStyle}>{label}</label>
            <textarea style={textareaStyle} value={data[field] ?? ''} onChange={e => setData(field, e.target.value)} />
            {errors[field] && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 2 }}>{errors[field]}</p>}
        </div>
    );

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">KEW.PA-30 — Laporan Akhir Kehilangan</h2>
                    <div className="flex gap-2">
                        <Link href={route('loss-reports.index')} className="text-sm text-indigo-600 hover:text-indigo-900">← Senarai Kehilangan</Link>
                        <a href={route('assets.loss-reports.kewpa30.download', [asset.id, lossReport.id])}
                           className="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            Download PDF
                        </a>
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-30 - Laporan Akhir Kehilangan" />
            <div className="py-6">
                <div className="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    <form onSubmit={handleSubmit}>
                        {/* ── SECTION 1: ASSET DETAILS ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 1: Butiran Aset / Asset Details</div>
                            <div style={sectionBody}>
                                <div style={fieldRow}>
                                    <InputField label="(a) No. Tag Aset" field="asset_tag_no" />
                                    <InputField label="(b) Perihal Aset" field="asset_description" />
                                </div>
                                <div style={fieldRow}>
                                    <InputField label="(c) Kategori" field="asset_category" />
                                    <InputField label="(d) No. Siri" field="asset_serial_no" />
                                </div>
                                <div style={fieldRow}>
                                    <InputField label="(e) Lokasi Berdaftar" field="asset_location_registered" />
                                    <InputField label="(f) Pegawai Terakhir" field="last_custodian" />
                                </div>
                            </div>
                        </div>

                        {/* ── SECTION 2: LOSS DESCRIPTION ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 2: Perihal Kehilangan / Loss Description</div>
                            <div style={sectionBody}>
                                <TextareaField label="(a) Perihal Kehilangan" field="incident_description" />
                                <div style={fieldRow}>
                                    <InputField label="(b) Tarikh Kejadian" field="incident_date" type="date" />
                                    <InputField label="(c) Masa" field="incident_time" type="time" />
                                </div>
                                <TextareaField label="(d) Tempat Kejadian" field="incident_location_details" />
                                <TextareaField label="(e) Cara Kehilangan" field="incident_circumstances" />
                            </div>
                        </div>

                        {/* ── SECTION 3: POLICE FINDINGS ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 3: Dapatan Polis / Police Findings</div>
                            <div style={sectionBody}>
                                <div style={fieldRow}>
                                    <InputField label="No. Laporan Polis" field="police_report_ref" />
                                    <InputField label="Balai Polis" field="police_station" />
                                </div>
                                <InputField label="Pegawai Penyiasat" field="police_officer_name" />
                                <TextareaField label="Dapatan Siasatan Polis" field="police_investigation_findings" />
                            </div>
                        </div>

                        {/* ── SECTION 4: WITNESS STATEMENTS ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 4: Keterangan Saksi / Witness Statements</div>
                            <div style={sectionBody}>
                                <div style={fieldRow}>
                                    <InputField label="(a) Nama Saksi" field="witness_1_name" />
                                    <InputField label="(b) Nama Saksi" field="witness_2_name" />
                                </div>
                                <TextareaField label="(a) Keterangan Saksi" field="witness_1_statement" />
                                <TextareaField label="(b) Keterangan Saksi" field="witness_2_statement" />
                            </div>
                        </div>

                        {/* ── SECTION 5: PROCEDURAL COMPLIANCE ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 5: Pematuhan Prosedur / Procedural Compliance</div>
                            <div style={sectionBody}>
                                <div style={fullRow}>
                                    <label style={labelStyle}>Prosedur Dipatuhi?</label>
                                    <div style={{ display: 'flex', gap: 16 }}>
                                        <label style={{ fontSize: 13, display: 'flex', alignItems: 'center', gap: 4 }}>
                                            <input type="radio" name="complied" checked={data.complied_with_procedures === true}
                                                onChange={() => setData('complied_with_procedures', true)} /> Ya
                                        </label>
                                        <label style={{ fontSize: 13, display: 'flex', alignItems: 'center', gap: 4 }}>
                                            <input type="radio" name="complied" checked={data.complied_with_procedures === false}
                                                onChange={() => setData('complied_with_procedures', false)} /> Tidak
                                        </label>
                                        <label style={{ fontSize: 13, display: 'flex', alignItems: 'center', gap: 4 }}>
                                            <input type="radio" name="complied" checked={data.complied_with_procedures === null}
                                                onChange={() => setData('complied_with_procedures', null)} /> N/A
                                        </label>
                                    </div>
                                </div>
                                <TextareaField label="Catatan Pematuhan" field="procedural_compliance_notes" />
                                <TextareaField label="Kekurangan / Gaps" field="procedural_gaps" />
                            </div>
                        </div>

                        {/* ── SECTION 6: PREVENTION STEPS ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 6: Langkah Pencegahan / Prevention Steps</div>
                            <div style={sectionBody}>
                                <TextareaField label="Tindakan Diambil" field="prevention_actions_taken" />
                                <TextareaField label="Saranan Pencegahan" field="prevention_recommendations" />
                            </div>
                        </div>

                        {/* ── SECTION 7: INVESTIGATION SUMMARY ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 7: Rumusan Siasatan / Investigation Summary</div>
                            <div style={sectionBody}>
                                <TextareaField label="Kesimpulan Siasatan" field="investigation_conclusion" />
                            </div>
                        </div>

                        {/* ── SECTION 8: RECOMMENDATION ── */}
                        <div style={sectionBox}>
                            <div style={sectionHeader}>Bahagian 8: Syor / Recommendation</div>
                            <div style={sectionBody}>
                                <div style={fullRow}>
                                    <label style={labelStyle}>Tindakan Disyorkan</label>
                                    <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
                                        {[
                                            { value: 'gantian_setara', label: 'Gantian Setara' },
                                            { value: 'surcaj', label: 'Surcaj' },
                                            { value: 'tatatertib', label: 'Tindakan Tatatertib' },
                                            { value: 'hapuskira', label: 'Hapuskira' },
                                        ].map(o => (
                                            <label key={o.value} style={{ fontSize: 13, display: 'flex', alignItems: 'center', gap: 4, padding: '6px 12px', borderRadius: 6, border: data.recommended_action === o.value ? '2px solid #5C001F' : '1.5px solid #e5e7eb', background: data.recommended_action === o.value ? '#FFF5AB' : '#fff', cursor: 'pointer' }}>
                                                <input type="radio" name="recommended_action" value={o.value}
                                                    checked={data.recommended_action === o.value}
                                                    onChange={e => setData('recommended_action', e.target.value)} /> {o.label}
                                            </label>
                                        ))}
                                    </div>
                                </div>
                                <TextareaField label="Rasional / Justifikasi" field="recommendation_rationale" />
                            </div>
                        </div>

                        {/* ── SUBMIT ── */}
                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 8 }}>
                            <Link href={route('loss-reports.index')}
                                style={{ padding: '8px 20px', borderRadius: 6, fontSize: 13, color: '#4B5563', background: '#fff', border: '1.5px solid #d1d5db', textDecoration: 'none' }}>
                                Batal
                            </Link>
                            <button type="submit" disabled={processing}
                                style={{ padding: '8px 20px', borderRadius: 6, fontSize: 13, fontWeight: 600, color: '#fff', background: '#5C001F', border: 'none', cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.6 : 1 }}>
                                {processing ? 'Menyimpan...' : 'Simpan Laporan Akhir'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
