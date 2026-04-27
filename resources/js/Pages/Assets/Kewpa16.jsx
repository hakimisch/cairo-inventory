import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa16({ asset, assessment }) {
    const { data, setData, post, processing, errors } = useForm({
        plate_no: assessment?.plate_no ?? '',
        chassis_no: assessment?.chassis_no ?? '',
        engine_no: assessment?.engine_no ?? '',
        vehicle_brand: assessment?.vehicle_brand ?? '',
        vehicle_model: assessment?.vehicle_model ?? '',
        vehicle_year: assessment?.vehicle_year ?? '',
        road_tax_expiry: assessment?.road_tax_expiry ?? '',
        engine_capacity: assessment?.engine_capacity ?? '',
        fuel_type: assessment?.fuel_type ?? '',
        vehicle_color: assessment?.vehicle_color ?? '',
        condition_report: assessment?.condition_report ?? '',
        estimated_value: assessment?.estimated_value ?? '',
        assessment_date: assessment?.assessment_date ?? '',
        assessor_name: assessment?.assessor_name ?? '',
        assessor_position: assessment?.assessor_position ?? '',
        recommendation: assessment?.recommendation ?? '',
        status: assessment?.status ?? 'draft',
        notes: assessment?.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('assets.vehicle-disposal.store', asset.id), {
            preserveScroll: true,
        });
    };

    const inputClass = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
    const labelClass = 'block text-sm font-medium text-gray-700';
    const fieldGroupClass = 'grid grid-cols-1 md:grid-cols-3 gap-4';

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        KEW.PA-16 — Perakuan Pelupusan Kenderaan
                    </h2>
                    <div className="flex gap-2">
                        <Link
                            href={route('assets.kewpa2', asset.id)}
                            className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            ← Back to Asset
                        </Link>
                        {assessment?.id && (
                            <Link
                                href={route('assets.vehicle-disposal.kewpa16.download', asset.id)}
                                className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                            >
                                Download PDF
                            </Link>
                        )}
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-16 - Vehicle Disposal Assessment" />

            <div className="py-6">
                <div className="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    {/* Asset Info Card */}
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Asset Information</h3>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><span className="text-gray-500">Tag:</span> <span className="font-medium">{asset.asset_tag}</span></div>
                            <div><span className="text-gray-500">Name:</span> <span className="font-medium">{asset.name}</span></div>
                            <div><span className="text-gray-500">Brand:</span> <span className="font-medium">{asset.brand || '-'}</span></div>
                            <div><span className="text-gray-500">Model:</span> <span className="font-medium">{asset.model || '-'}</span></div>
                            <div><span className="text-gray-500">Serial No:</span> <span className="font-medium">{asset.serial_number || '-'}</span></div>
                            <div><span className="text-gray-500">Category:</span> <span className="font-medium">{asset.category}</span></div>
                            <div><span className="text-gray-500">Location:</span> <span className="font-medium">{asset.location}</span></div>
                            <div><span className="text-gray-500">Status:</span>
                                <span className={`ml-1 px-2 py-0.5 text-xs font-medium rounded-full ${
                                    asset.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                }`}>{asset.status}</span>
                            </div>
                        </div>
                    </div>

                    {/* Assessment Form */}
                    <form onSubmit={handleSubmit} className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-gray-800 mb-6">Vehicle Disposal Assessment Form</h3>

                        {/* Vehicle Registration Details */}
                        <div className="mb-6">
                            <h4 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">A. Vehicle Registration Details</h4>
                            <div className={fieldGroupClass}>
                                <div>
                                    <label className={labelClass}>Plate No. *</label>
                                    <input type="text" value={data.plate_no} onChange={e => setData('plate_no', e.target.value)} className={inputClass} required />
                                    {errors.plate_no && <p className="text-red-500 text-xs mt-1">{errors.plate_no}</p>}
                                </div>
                                <div>
                                    <label className={labelClass}>Chassis No. (VIN)</label>
                                    <input type="text" value={data.chassis_no} onChange={e => setData('chassis_no', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Engine No.</label>
                                    <input type="text" value={data.engine_no} onChange={e => setData('engine_no', e.target.value)} className={inputClass} />
                                </div>
                            </div>
                        </div>

                        {/* Vehicle Specifications */}
                        <div className="mb-6">
                            <h4 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">B. Vehicle Specifications</h4>
                            <div className={fieldGroupClass}>
                                <div>
                                    <label className={labelClass}>Brand / Make</label>
                                    <input type="text" value={data.vehicle_brand} onChange={e => setData('vehicle_brand', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Model</label>
                                    <input type="text" value={data.vehicle_model} onChange={e => setData('vehicle_model', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Year</label>
                                    <input type="number" value={data.vehicle_year} onChange={e => setData('vehicle_year', e.target.value)} className={inputClass} min="1900" max="2099" />
                                </div>
                            </div>
                            <div className={fieldGroupClass + ' mt-3'}>
                                <div>
                                    <label className={labelClass}>Engine Capacity</label>
                                    <input type="text" value={data.engine_capacity} onChange={e => setData('engine_capacity', e.target.value)} className={inputClass} placeholder="e.g. 2000cc" />
                                </div>
                                <div>
                                    <label className={labelClass}>Fuel Type</label>
                                    <select value={data.fuel_type} onChange={e => setData('fuel_type', e.target.value)} className={inputClass}>
                                        <option value="">-- Select --</option>
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electric">Electric</option>
                                        <option value="Hybrid">Hybrid</option>
                                    </select>
                                </div>
                                <div>
                                    <label className={labelClass}>Colour</label>
                                    <input type="text" value={data.vehicle_color} onChange={e => setData('vehicle_color', e.target.value)} className={inputClass} />
                                </div>
                            </div>
                            <div className={fieldGroupClass + ' mt-3'}>
                                <div>
                                    <label className={labelClass}>Road Tax Expiry</label>
                                    <input type="date" value={data.road_tax_expiry} onChange={e => setData('road_tax_expiry', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Estimated Market Value (RM)</label>
                                    <input type="number" step="0.01" value={data.estimated_value} onChange={e => setData('estimated_value', e.target.value)} className={inputClass} />
                                </div>
                            </div>
                        </div>

                        {/* Condition & Assessment */}
                        <div className="mb-6">
                            <h4 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">C. Condition & Assessment</h4>
                            <div className="mb-3">
                                <label className={labelClass}>Condition Report</label>
                                <textarea rows={4} value={data.condition_report} onChange={e => setData('condition_report', e.target.value)} className={inputClass} placeholder="Describe the physical condition of the vehicle..." />
                            </div>
                            <div className={fieldGroupClass}>
                                <div>
                                    <label className={labelClass}>Assessment Date</label>
                                    <input type="date" value={data.assessment_date} onChange={e => setData('assessment_date', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Assessor Name</label>
                                    <input type="text" value={data.assessor_name} onChange={e => setData('assessor_name', e.target.value)} className={inputClass} />
                                </div>
                                <div>
                                    <label className={labelClass}>Assessor Position</label>
                                    <input type="text" value={data.assessor_position} onChange={e => setData('assessor_position', e.target.value)} className={inputClass} />
                                </div>
                            </div>
                        </div>

                        {/* Recommendation & Status */}
                        <div className="mb-6">
                            <h4 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">D. Recommendation & Status</h4>
                            <div className="mb-3">
                                <label className={labelClass}>Recommendation</label>
                                <textarea rows={3} value={data.recommendation} onChange={e => setData('recommendation', e.target.value)} className={inputClass} placeholder="Disposal recommendation..." />
                            </div>
                            <div className={fieldGroupClass}>
                                <div>
                                    <label className={labelClass}>Status</label>
                                    <select value={data.status} onChange={e => setData('status', e.target.value)} className={inputClass}>
                                        <option value="draft">Draft</option>
                                        <option value="submitted">Submitted</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div className="md:col-span-2">
                                    <label className={labelClass}>Notes</label>
                                    <input type="text" value={data.notes} onChange={e => setData('notes', e.target.value)} className={inputClass} />
                                </div>
                            </div>
                        </div>

                        {/* Submit */}
                        <div className="flex items-center justify-end gap-3 pt-4 border-t">
                            <Link
                                href={route('assets.kewpa2', asset.id)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {processing ? 'Saving...' : 'Save Assessment'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
