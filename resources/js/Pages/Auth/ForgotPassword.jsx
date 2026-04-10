import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';

const UTM = { maroon: '#5C001F', gold: '#F8A617', white: '#FFFFFF', gray100: '#EDE9E4', gray500: '#8A8480', gray900: '#1E1B18' };
const inputStyle = { width: '100%', padding: '10px 14px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, fontSize: '13px', color: UTM.gray900, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const labelStyle = { display: 'block', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 6 };
const buttonStyle = { padding: '10px 24px', borderRadius: 8, fontSize: '13px', fontWeight: 700, background: UTM.maroon, color: UTM.white, border: 'none', cursor: 'pointer', boxShadow: '0 2px 8px rgba(92,0,31,0.2)' };

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    const submit = (e) => {
        e.preventDefault();
        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 16 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '20px', fontWeight: 800, color: UTM.maroon, margin: 0 }}>Lupa Kata Laluan</h2>
            </div>

            <div style={{ fontSize: '13px', color: UTM.gray500, lineHeight: 1.6, marginBottom: 24 }}>
                Lupa kata laluan anda? Tiada masalah. Sila masukkan alamat e-mel anda dan kami akan menghantar pautan tetapan semula kepada anda.
            </div>

            {status && <div className="mb-4 text-sm font-medium text-green-600">{status}</div>}

            <form onSubmit={submit}>
                <div style={{ marginBottom: 20 }}>
                    <label style={labelStyle} htmlFor="email">Email</label>
                    <input id="email" type="email" name="email" value={data.email} style={inputStyle} autoFocus onChange={(e) => setData('email', e.target.value)} />
                    {errors.email && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.email}</p>}
                </div>

                <div className="mt-4 flex items-center justify-end">
                    <button type="submit" style={{ ...buttonStyle, opacity: processing ? 0.7 : 1 }} disabled={processing}>
                        Hantar Pautan
                    </button>
                </div>
            </form>
        </GuestLayout>
    );
}