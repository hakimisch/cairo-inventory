import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const UTM = { maroon: '#5C001F', gold: '#F8A617', white: '#FFFFFF', gray100: '#EDE9E4', gray500: '#8A8480', gray900: '#1E1B18' };
const buttonStyle = { padding: '10px 24px', borderRadius: 8, fontSize: '13px', fontWeight: 700, background: UTM.maroon, color: UTM.white, border: 'none', cursor: 'pointer', boxShadow: '0 2px 8px rgba(92,0,31,0.2)' };

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();
        post(route('verification.send'));
    };

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 16 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '20px', fontWeight: 800, color: UTM.maroon, margin: 0 }}>Pengesahan E-mel</h2>
            </div>

            <div style={{ fontSize: '13px', color: UTM.gray500, lineHeight: 1.6, marginBottom: 24 }}>
                Terima kasih kerana mendaftar! Sebelum bermula, sila sahkan alamat e-mel anda melalui pautan yang dihantar. Jika anda tidak menerimanya, kami sedia menghantarnya semula.
            </div>

            {status === 'verification-link-sent' && (
                <div style={{ padding: '12px', background: '#E6F4EC', color: '#1A7A3C', borderRadius: 8, border: '1px solid #B2DFC2', fontSize: '12px', fontWeight: 600, marginBottom: 24 }}>
                    Pautan pengesahan baru telah dihantar ke alamat e-mel anda semasa pendaftaran.
                </div>
            )}

            <form onSubmit={submit}>
                <div className="mt-4 flex items-center justify-between">
                    <button type="submit" style={{ ...buttonStyle, opacity: processing ? 0.7 : 1 }} disabled={processing}>
                        Resend Verification Email
                    </button>

                    <Link href={route('logout')} method="post" as="button" style={{ fontSize: '12px', color: UTM.gray500, fontWeight: 700, textDecoration: 'underline', background: 'transparent', border: 'none', cursor: 'pointer' }}>
                        Log Out
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}