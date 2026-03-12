import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa3({ asset }) {
    return (
        <AuthenticatedLayout header={<h2 className="print:hidden uppercase text-sm font-bold">Pratonton KEW.PA-3</h2>}>
            <Head title={`KEW.PA-3 - ${asset.asset_tag}`} />
            
            <div className="py-12 bg-white min-h-screen">
                <div className="max-w-5xl mx-auto border-2 border-black p-8 font-serif text-[11px] leading-tight">
                    <div className="text-right font-bold mb-2">KEW.PA-3</div>
                    <div className="text-center font-bold text-lg mb-6 underline uppercase tracking-tighter">DAFTAR HARTA MODAL</div>
                    
                    <div className="grid grid-cols-2 mb-4 border-b border-black pb-2">
                        <div>
                            <p><strong>Kementerian/Jabatan:</strong> CAIRO UTM</p>
                            <p><strong>Bahagian:</strong> Research & Robotics</p>
                        </div>
                        <div className="text-right">
                            <p><strong>No. Siri Pendaftaran:</strong> {asset.asset_tag}</p>
                        </div>
                    </div>

                    <div className="font-bold mb-1">BAHAGIAN A</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 w-1/4 font-bold bg-gray-50 uppercase">Keterangan Aset</td>
                                <td colSpan="3" className="border border-black p-2 uppercase font-medium">{asset.name}</td>
                            </tr>
                            {/* Spatie Media Library Photo Integration */}
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">Gambar Aset</td>
                                <td colSpan="3" className="border border-black p-2 h-40 text-center text-gray-400">
                                    {asset.image_url ? (
                                        <img src={asset.image_url} alt={asset.name} className="mx-auto h-full object-contain" />
                                    ) : (
                                        <span className="italic uppercase">[ Gambar akan dijana oleh Spatie Media Library ]</span>
                                    )}
                                </td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">Kategori</td>
                                <td className="border border-black p-2 w-1/4">{asset.category}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-1/4 uppercase">Tarikh Perolehan</td>
                                <td className="border border-black p-2">{new Date(asset.received_date || asset.created_at).toLocaleDateString('ms-MY')}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">Harga Perolehan</td>
                                <td className="border border-black p-2 font-bold text-indigo-700 uppercase">RM {Number(asset.purchase_price).toLocaleString()}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">Lokasi</td>
                                <td className="border border-black p-2">{asset.location}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="font-bold mb-1 uppercase italic text-gray-600">BAHAGIAN B (Aksesori/Komponen)</div>
                    <table className="w-full border-collapse border border-black">
                        <thead className="bg-gray-50 text-center font-bold">
                            <tr>
                                <th className="border border-black p-1 w-8">Bil</th>
                                <th className="border border-black p-1">No. Siri Pendaftaran Komponen</th>
                                <th className="border border-black p-1">Jenis/Jenama</th>
                                <th className="border border-black p-1">Kos (RM)</th>
                                <th className="border border-black p-1">Tarikh Pasang</th>
                            </tr>
                        </thead>
                        <tbody>
                            {asset.components && asset.components.length > 0 ? (
                                asset.components.map((comp, index) => (
                                    <tr key={index}>
                                        <td className="border border-black p-1 text-center">{index + 1}</td>
                                        <td className="border border-black p-1">{comp.serial_no}</td>
                                        <td className="border border-black p-1">{comp.brand}</td>
                                        <td className="border border-black p-1 text-right">{Number(comp.cost).toLocaleString()}</td>
                                        <td className="border border-black p-1 text-center">{comp.installed_date}</td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td className="border border-black p-4 text-center">1</td>
                                    <td className="border border-black p-4 italic text-gray-400 uppercase">Tiada data komponen / aksesori didaftarkan</td>
                                    <td className="border border-black p-4"></td>
                                    <td className="border border-black p-4"></td>
                                    <td className="border border-black p-4"></td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="mt-8 flex justify-end gap-3 print:hidden">
                        <a 
                            href={route('assets.kewpa3.download', asset.id)} 
                            className="bg-indigo-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-indigo-700 transition"
                        >
                            Download Official PDF (KEW.PA-3)
                        </a>
                        <button 
                            onClick={() => window.print()} 
                            className="border border-gray-300 px-6 py-2 rounded font-bold hover:bg-gray-50"
                        >
                            Cetak Skrin
                        </button>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}