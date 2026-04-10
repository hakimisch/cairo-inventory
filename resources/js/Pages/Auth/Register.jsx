import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const UTM = { maroon: '#5C001F', gold: '#F8A617', white: '#FFFFFF', gray100: '#EDE9E4', gray500: '#8A8480', gray900: '#1E1B18' };
const inputStyle = { width: '100%', padding: '10px 14px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, fontSize: '13px', color: UTM.gray900, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const labelStyle = { display: 'block', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 6 };
const buttonStyle = { padding: '10px 24px', borderRadius: 8, fontSize: '13px', fontWeight: 700, background: UTM.maroon, color: UTM.white, border: 'none', cursor: 'pointer', boxShadow: '0 2px 8px rgba(92,0,31,0.2)' };

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({ name: '', email: '', password: '', password_confirmation: '' });

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), { onFinish: () => reset('password', 'password_confirmation') });
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '20px', fontWeight: 800, color: UTM.maroon, margin: 0 }}>Register</h2>
            </div>

            <form onSubmit={submit}>
                <div style={{ marginBottom: 20 }}>
                    <label style={labelStyle} htmlFor="name">Name</label>
                    <input id="name" name="name" value={data.name} style={inputStyle} autoComplete="name" autoFocus onChange={(e) => setData('name', e.target.value)} required />
                    {errors.name && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.name}</p>}
                </div>

                <div style={{ marginBottom: 20 }}>
                    <label style={labelStyle} htmlFor="email">Email</label>
                    <input id="email" type="email" name="email" value={data.email} style={inputStyle} autoComplete="username" onChange={(e) => setData('email', e.target.value)} required />
                    {errors.email && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.email}</p>}
                </div>

                <div style={{ marginBottom: 20 }}>
                    <label style={labelStyle} htmlFor="password">Password</label>
                    <input id="password" type="password" name="password" value={data.password} style={inputStyle} autoComplete="new-password" onChange={(e) => setData('password', e.target.value)} required />
                    {errors.password && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.password}</p>}
                </div>

                <div style={{ marginBottom: 20 }}>
                    <label style={labelStyle} htmlFor="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" value={data.password_confirmation} style={inputStyle} autoComplete="new-password" onChange={(e) => setData('password_confirmation', e.target.value)} required />
                    {errors.password_confirmation && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.password_confirmation}</p>}
                </div>

                <div className="mt-6 flex items-center justify-end gap-4">
                    <Link href={route('login')} style={{ fontSize: '12px', color: UTM.gray500, fontWeight: 600, textDecoration: 'none' }}>
                        Already registered?
                    </Link>
                    <button type="submit" style={{ ...buttonStyle, opacity: processing ? 0.7 : 1 }} disabled={processing}>
                        Register
                    </button>
                </div>
            </form>
        </GuestLayout>
    );
}