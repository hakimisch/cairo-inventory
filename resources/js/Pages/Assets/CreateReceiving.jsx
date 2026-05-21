import { useEffect } from 'react';
import { router } from '@inertiajs/react';

// ──────────────────────────────────────────────────────────────────────────────
// DEPRECATED — Inline create form is now available on ReceivingIndex.jsx.
// This page automatically redirects to the index page.
// ──────────────────────────────────────────────────────────────────────────────

export default function CreateReceiving() {
    useEffect(() => {
        router.visit(route('receivings.index'));
    }, []);

    return null;
}
