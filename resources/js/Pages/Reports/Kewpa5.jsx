import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
 
const UTM = {
    maroon: '#5C001F', gold: '#F8A617', goldDark: '#C9840A', sand: '#FFF5AB',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4',
    gray300: '#C5BFB8', gray500: '#8A8480', gray700: '#4A4540', gray900: '#1E1B18',
};
 
const fmt   = d => d ? new Date(d).toLocaleDateString('ms-MY') : '—';
const fmtRM = n => n != null ? Number(n).toLocaleString('ms-MY', { minimumFractionDigits: 2 }) : '—';
 
export default function Kewpa5({ assets, year, years, locations, totalValue, totalCount }) {
    const [selYear, setSelYear] = useState(String(year));
    const [selLoc, setSelLoc]   = useState('');
 
    const applyFilter = () => {
        router.get(route('reports.kewpa5'), { year: selYear, location: selLoc }, { preserveState: true });
    };
 
    return (
        <AuthenticatedLayout header={
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                    KEW.PA-5 — Senarai Daftar Inventori
                </h2>
            </div>
        }>
            <Head title="KEW.PA-5 Senarai Inventori" />
            <style>{`@media print { .no-print { display: none !important; } body { margin: 0; } }`}</style>
 
            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '24px' }}>
                <div style={{ maxWidth: 1100, margin: '0 auto' }}>
 
                    {/* Filter bar */}
                    <div className="no-print" style={{
                        background: UTM.white, borderRadius: 10, padding: '16px 20px',
                        boxShadow: '0 1px 4px rgba(92,0,31,0.07)', marginBottom: 20,
                        display: 'flex', flexWrap: 'wrap', gap: 12, alignItems: 'flex-end',
                    }}>
                        <div>
                            <label style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase',
                                            letterSpacing: '0.07em', color: UTM.gray500, display: 'block', marginBottom: 5 }}>
                                Tahun
                            </label>
                            <select value={selYear} onChange={e => setSelYear(e.target.value)}
                                style={{ padding: '8px 12px', borderRadius: 7, border: `1.5px solid ${UTM.gray100}`,
                                         fontSize: '13px', color: UTM.gray900, background: UTM.white, outline: 'none' }}>
                                {years.map(y => <option key={y} value={y}>{y}</option>)}
                            </select>
                        </div>
                        <div>
                            <label style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase',
                                            letterSpacing: '0.07em', color: UTM.gray500, display: 'block', marginBottom: 5 }}>
                                Lokasi / Unit
                            </label>
                            <select value={selLoc} onChange={e => setSelLoc(e.target.value)}
                                style={{ padding: '8px 12px', borderRadius: 7, border: `1.5px solid ${UTM.gray100}`,
                                         fontSize: '13px', color: UTM.gray900, background: UTM.white, outline: 'none',
                                         minWidth: 200 }}>
                                <option value="">Semua Lokasi</option>
                                {locations.map(l => <option key={l} value={l}>{l}</option>)}
                            </select>
                        </div>
                        <button onClick={applyFilter}
                            style={{ padding: '9px 20px', background: UTM.maroon, color: UTM.white,
                                     borderRadius: 7, border: 'none', fontSize: '13px', fontWeight: 700,
                                     cursor: 'pointer', boxShadow: '0 2px 6px rgba(92,0,31,0.2)' }}>
                            Tapis
                        </button>
                        <div style={{ flex: 1 }} />
                        <a href={route('reports.kewpa5.download') + `?year=${selYear}`}
                           style={{ padding: '9px 20px', background: '#1A7A3C', color: UTM.white,
                                    borderRadius: 7, fontSize: '13px', fontWeight: 700,
                                    textDecoration: 'none', boxShadow: '0 2px 6px rgba(26,122,60,0.2)' }}>
                            Muat Turun PDF
                        </a>
                        <button onClick={() => window.print()}
                            style={{ padding: '9px 20px', background: UTM.gray100, color: UTM.gray700,
                                     borderRadius: 7, border: 'none', fontSize: '13px', fontWeight: 700, cursor: 'pointer' }}>
                            Cetak
                        </button>
                    </div>
 
                    {/* Formal document */}
                    <div style={{ background: UTM.white, borderRadius: 10,
                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)',
                                  padding: '32px 36px', fontFamily: 'serif',
                                  fontSize: '11px', lineHeight: '1.5', color: '#000' }}>
 
                        <div style={{ textAlign: 'right', fontWeight: 700, marginBottom: 4 }}>KEW.PA-5</div>
                        <div style={{ textAlign: 'center', fontWeight: 700, fontSize: '13px',
                                      marginBottom: 4, textTransform: 'uppercase' }}>
                            UNIVERSITI TEKNOLOGI MALAYSIA
                        </div>
                        <div style={{ textAlign: 'center', fontWeight: 700, fontSize: '12px',
                                      marginBottom: 18, textDecoration: 'underline', textTransform: 'uppercase' }}>
                            SENARAI DAFTAR INVENTORI
                        </div>
                        <div style={{ marginBottom: 4, fontSize: '10px', textAlign: 'center', fontStyle: 'italic' }}>
                            (Satu (1) daftar bagi satu (1) jenis inventori dalam satu Pesanan Tempatan)
                        </div>
 
                        <div style={{ marginBottom: 16, lineHeight: 2 }}>
                            <p><strong>Tahun :</strong> {year}</p>
                            <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
                            <p><strong>Unit/Makmal :</strong> Research & Robotics</p>
                        </div>
 
                        <table style={{ width: '100%', borderCollapse: 'collapse', marginBottom: 20 }}>
                            <thead>
                                <tr style={{ background: '#f0f0f0', fontWeight: 700, fontSize: '10px',
                                             textTransform: 'uppercase', textAlign: 'center' }}>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: 30 }}>Bil</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '18%' }}>
                                        Nombor Siri Pendaftaran / Nombor Kod
                                    </th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', textAlign: 'left' }}>Nama Aset</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '10%' }}>Kategori</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '14%' }}>Lokasi / Unit</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '10%' }}>Kuantiti</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '10%' }}>Tarikh Terima</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '12%' }}>
                                        Harga Perolehan Asal (RM)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {assets.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} style={{ border: '1px solid #000', padding: '20px',
                                                                  textAlign: 'center', fontStyle: 'italic', color: UTM.gray500 }}>
                                            Tiada rekod inventori bagi tahun {year}.
                                        </td>
                                    </tr>
                                ) : assets.map((a, i) => (
                                    <tr key={a.id} style={{ background: i % 2 === 0 ? '#fff' : '#fafafa' }}>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center' }}>{i + 1}</td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px',
                                                     fontFamily: 'monospace', fontSize: '10px', textAlign: 'center' }}>
                                            {a.national_code || a.asset_tag}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px',
                                                     textTransform: 'uppercase', fontWeight: 600 }}>
                                            {a.name}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px',
                                                     textAlign: 'center', fontSize: '10px' }}>
                                            {a.category || '—'}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', fontSize: '10px' }}>
                                            {a.location || '—'}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center' }}>1</td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center' }}>
                                            {fmt(a.received_date)}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px',
                                                     textAlign: 'right', fontWeight: 700 }}>
                                            {fmtRM(a.purchase_price)}
                                        </td>
                                    </tr>
                                ))}
                                <tr style={{ fontWeight: 700, background: '#f0f0f0' }}>
                                    <td colSpan={7} style={{ border: '1px solid #000', padding: '6px 8px',
                                                             textAlign: 'right', textTransform: 'uppercase',
                                                             letterSpacing: '0.05em' }}>
                                        Jumlah Keseluruhan ({totalCount} item)
                                    </td>
                                    <td style={{ border: '1px solid #000', padding: '6px 8px',
                                                 textAlign: 'right', fontSize: '12px' }}>
                                        {fmtRM(totalValue)}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
 
                        {/* Signature */}
                        <div style={{ marginTop: 32, display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40 }}>
                            <div style={{ lineHeight: 2.2 }}>
                                <p style={{ fontWeight: 700 }}>Disediakan oleh:</p>
                                <p>......................................................................</p>
                                <p>Nama :</p>
                                <p>Jawatan :</p>
                                <p>Tarikh :</p>
                            </div>
                            <div style={{ lineHeight: 2.2 }}>
                                <p style={{ fontWeight: 700 }}>Disahkan oleh Dekan/Ketua PTJ:</p>
                                <p>......................................................................</p>
                                <p>Nama :</p>
                                <p>Jawatan :</p>
                                <p>Tarikh :</p>
                            </div>
                        </div>
 
                        <p style={{ marginTop: 20, fontSize: '10px', color: UTM.gray500, fontStyle: 'italic' }}>
                            * Laporan merangkumi semua inventori yang diterima bagi tahun {year}
                        </p>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
 