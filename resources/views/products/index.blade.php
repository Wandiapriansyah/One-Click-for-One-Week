@extends('layouts.app')

@section('content')
<div class="container">
    <section class="mx-auto flex min-h-[60vh] max-w-3xl flex-col items-center justify-center gap-8 px-6 py-12 text-center">
        <div class="w-full rounded-3xl border border-violet-100 bg-linear-to-br from-white via-violet-50 to-indigo-50 p-8 shadow-xl shadow-violet-100/60 transition duration-300 hover:-translate-y-1 hover:shadow-2xl sm:p-12">
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-violet-500">Pesan untukmu</p>
            <p id="content" aria-live="polite" aria-atomic="true" class="text-2xl font-semibold leading-relaxed text-slate-800 sm:text-3xl">
                Tekan tombol untuk mendapatkan kalimat motivasi.
            </p>
        </div>

        <button id="tombol" onclick="run()" class="inline-flex items-center gap-2 rounded-xl bg-linear-to-r from-violet-600 to-indigo-600 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-500/25 transition duration-200 hover:-translate-y-0.5 hover:from-violet-700 hover:to-indigo-700 hover:shadow-xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 active:translate-y-0 active:scale-95 disabled:cursor-wait disabled:opacity-60">
            <span aria-hidden="true">✨</span> Motivasi
        </button>
    </section>
</div>

<script>
    async function run() {
        const tombol = document.getElementById('tombol');
        const konten = document.getElementById('content');
        tombol.disabled = true;

        try {
            const response = await fetch('{{ route('motivation.random') }}', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Kalimat motivasi tidak dapat dimuat.');
            }

            konten.textContent = data.motivation;
            konten.animate(
                [
                    { opacity: 0, transform: 'translateY(12px) scale(0.98)' },
                    { opacity: 1, transform: 'translateY(0) scale(1)' }
                ],
                { duration: 450, easing: 'cubic-bezier(0.2, 0.8, 0.2, 1)' }
            );
        } catch (error) {
            konten.textContent = error.message || 'Koneksi bermasalah. Silakan coba lagi.';
        } finally {
            tombol.disabled = false;
        }
    }
    
</script>
@endsection