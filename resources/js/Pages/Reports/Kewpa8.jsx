import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
 
const UTM = {
    maroon: '#5C001F', gold: '#F8A617', goldDark: '#C9840A', sand: '#FFF5AB',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4',
    gray300: '#C5BFB8', gray500: '#8A8480', gray700: '#4A4540', gray900: '#1E1B18',
};
 
const fmtRM = n => n != null ? Number(n).toLocaleString('ms-MY', { minimumFractionDigits: 2 }) : '0.00';
 
export default function Kewpa8({ rows, year, years, totals }) {
    const [selYear, setSelYear] = useState(String(year));
 
    const applyFilter = () => {
        router.get(route('reports.kewpa8'), { year: selYear }, { preserveState: true });
    };
 
    return (
        <AuthenticatedLayout header={
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                    KEW.PA-8 — Laporan Tahunan Harta Tetap &amp; Inventori
                </h2>
            </div>
        }>
            <Head title="KEW.PA-8 Laporan Tahunan" />
            <style>{`@media print { .no-print { display: none !important; } body { margin: 0; } }`}</style>
 
            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '24px' }}>
                <div style={{ maxWidth: 960, margin: '0 auto' }}>
 
                    {/* Summary cards */}
                    <div className="no-print" style={{
                        display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12, marginBottom: 20,
                    }}>
                        {[
                            { label: 'Bil. Harta Tetap', value: totals.fixed_count, color: UTM.maroon },
                            { label: 'Nilai Harta Tetap', value: `RM ${fmtRM(totals.fixed_value)}`, color: UTM.maroon },
                            { label: 'Bil. Inventori', value: totals.inventory_count, color: UTM.goldDark },
                            { label: 'Nilai Inventori', value: `RM ${fmtRM(totals.inventory_value)}`, color: UTM.goldDark },
                        ].map((c, i) => (
                            <div key={i} style={{ background: UTM.white, borderRadius: 10, padding: '16px 20px',
                                                  borderLeft: `4px solid ${c.color}`,
                                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)' }}>
                                <p style={{ fontSize: '10px', fontWeight: 700, textTransform: 'uppercase',
                                            letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 4 }}>
                                    {c.label}
                                </p>
                                <p style={{ fontSize: '20px', fontWeight: 900, color: c.color, lineHeight: 1 }}>
                                    {c.value}
                                </p>
                            </div>
                        ))}
                    </div>
 
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
                        <button onClick={applyFilter}
                            style={{ padding: '9px 20px', background: UTM.maroon, color: UTM.white,
                                     borderRadius: 7, border: 'none', fontSize: '13px', fontWeight: 700,
                                     cursor: 'pointer', boxShadow: '0 2px 6px rgba(92,0,31,0.2)' }}>
                            Tapis
                        </button>
                        <div style={{ flex: 1 }} />
                        <a href={route('reports.kewpa8.download') + `?year=${selYear}`}
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
 
                        <div style={{ textAlign: 'right', fontWeight: 700, marginBottom: 4 }}>KEW.PA-8</div>
                        <div style={{ textAlign: 'center', fontWeight: 700, fontSize: '13px',
                                      marginBottom: 4, textTransform: 'uppercase' }}>
                            UNIVERSITI TEKNOLOGI MALAYSIA
                        </div>
                        <div style={{ marginBottom: 4, lineHeight: 2 }}>
                            <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
                        </div>
                        <div style={{ textAlign: 'center', fontWeight: 700, fontSize: '12px',
                                      marginBottom: 20, textDecoration: 'underline', textTransform: 'uppercase' }}>
                            LAPORAN TAHUNAN HARTA TETAP DAN INVENTORI BAGI TAHUN {year}
                        </div>
 
                        <table style={{ width: '100%', borderCollapse: 'collapse', marginBottom: 24 }}>
                            <thead>
                                <tr style={{ background: '#f0f0f0', fontWeight: 700, fontSize: '10px',
                                             textTransform: 'uppercase', textAlign: 'center' }}>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: 30 }}>Bil.</th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', textAlign: 'left' }}>
                                        Unit/Makmal/Bahagian Di Bawahnya
                                    </th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '11%' }}>
                                        Bilangan KEW.PA-2
                                    </th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '15%' }}>
                                        Jumlah Nilai Harta Tetap (RM)
                                    </th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '11%' }}>
                                        Bilangan KEW.PA-3
                                    </th>
                                    <th style={{ border: '1px solid #000', padding: '6px 8px', width: '15%' }}>
                                        Jumlah Nilai Inventori (RM)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} style={{ border: '1px solid #000', padding: '20px',
                                                                  textAlign: 'center', fontStyle: 'italic', color: UTM.gray500 }}>
                                            Tiada rekod bagi tahun {year}.
                                        </td>
                                    </tr>
                                ) : rows.map((row, i) => (
                                    <tr key={i} style={{ background: i % 2 === 0 ? '#fff' : '#fafafa' }}>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center' }}>
                                            {i + 1}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', fontWeight: 600 }}>
                                            {row.location || 'Tidak Ditetapkan'}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center',
                                                     fontWeight: 700 }}>
                                            {row.fixed_count}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'right',
                                                     fontWeight: 700 }}>
                                            {fmtRM(row.fixed_value)}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'center',
                                                     fontWeight: 700 }}>
                                            {row.inventory_count}
                                        </td>
                                        <td style={{ border: '1px solid #000', padding: '5px 8px', textAlign: 'right',
                                                     fontWeight: 700 }}>
                                            {fmtRM(row.inventory_value)}
                                        </td>
                                    </tr>
                                ))}
 
                                {/* Totals */}
                                <tr style={{ fontWeight: 700, background: '#e8e8e8', fontSize: '12px' }}>
                                    <td colSpan={2} style={{ border: '1px solid #000', padding: '7px 8px',
                                                             textAlign: 'right', textTransform: 'uppercase',
                                                             letterSpacing: '0.05em' }}>
                                        JUMLAH
                                    </td>
                                    <td style={{ border: '1px solid #000', padding: '7px 8px', textAlign: 'center' }}>
                                        {totals.fixed_count}
                                    </td>
                                    <td style={{ border: '1px solid #000', padding: '7px 8px', textAlign: 'right' }}>
                                        {fmtRM(totals.fixed_value)}
                                    </td>
                                    <td style={{ border: '1px solid #000', padding: '7px 8px', textAlign: 'center' }}>
                                        {totals.inventory_count}
                                    </td>
                                    <td style={{ border: '1px solid #000', padding: '7px 8px', textAlign: 'right' }}>
                                        {fmtRM(totals.inventory_value)}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
 
                        {/* Signature */}
                        <div style={{ marginTop: 8 }}>
                            <p style={{ fontWeight: 700, marginBottom: 12 }}>
                                Tandatangan Dekan/Ketua PTJ
                            </p>
                            <p style={{ marginBottom: 6 }}>......................................................................</p>
                            <div style={{ lineHeight: 2.2 }}>
                                <p>Nama : _________________________________</p>
                                <p>Jawatan : _________________________________</p>
                                <p>Tarikh : _________________________________</p>
                            </div>
                        </div>
 
                        <p style={{ marginTop: 20, fontSize: '10px', color: UTM.gray500, fontStyle: 'italic' }}>
                            * Laporan merangkumi semua aset alih yang dipegang sehingga tahun {year}
                        </p>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}