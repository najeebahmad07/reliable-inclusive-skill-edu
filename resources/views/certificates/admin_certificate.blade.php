<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Authorization Certificate - {{ $admin->institute_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f1f5f9; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        @media print {
            body { background: transparent; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center">
    <div class="no-print mb-4 flex items-center justify-between w-full max-w-4xl">
        <a href="{{ url()->previous() }}" class="px-3 py-1 bg-slate-200 text-slate-700 rounded text-xs">&larr; Back</a>
        <button onclick="window.print()" class="px-4 py-1.5 bg-blue-600 text-white font-bold rounded text-xs shadow hover:bg-blue-700">
            Print Certificate
        </button>
    </div>

    <div class="w-full max-w-4xl bg-white p-10 rounded-2xl shadow-xl border-8 border-double border-blue-900 relative">
        <div class="border-2 border-slate-800 p-8 text-center relative">
            <div class="mb-4">
                <div class="flex items-center justify-center space-x-3 mb-2">
                    <img src="{{ asset('assets/images/logo.png') }}" class="h-16 w-auto" alt="RISE Logo">
                </div>
                <h1 class="text-3xl font-black font-cinzel text-slate-900 tracking-widest uppercase">
                    {{ config('rise.app_name', 'RISE') }}
                </h1>
                <p class="text-xs font-bold text-amber-700 tracking-wider uppercase mt-0.5">
                    {{ config('rise.tagline', 'Reliable Inclusive Skill Education') }}
                </p>
                <div class="mt-3 inline-block px-4 py-1 bg-blue-900 text-white rounded text-xs font-bold uppercase tracking-widest">
                    Certificate of Center Authorization
                </div>
            </div>

            <div class="my-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-500 block mb-1">This is officially certified that</span>
                <h2 class="text-2xl font-black text-slate-900 font-serif border-b-2 border-amber-400 inline-block px-8 pb-1 uppercase">
                    {{ $admin->institute_name ?? $admin->name }}
                </h2>
                <div class="text-xs text-slate-600 mt-2">
                    Under the supervision of <strong class="text-slate-900">{{ $admin->name }}</strong>
                    <br>
                    Located at: {{ $admin->institute_address ?? ($admin->city . ', ' . $admin->district) }}
                </div>
            </div>

            <p class="text-xs text-slate-700 max-w-2xl mx-auto leading-relaxed mb-6">
                is an Authorized Skill Training Center of RISE Council. The center is fully recognized and authorized to conduct educational, vocational, and computer training programs as approved by the Council.
            </p>

            <div class="flex items-center justify-between text-[11px] text-slate-600 px-6 py-2 bg-slate-50 rounded border border-slate-200 mb-8 max-w-xl mx-auto">
                <div>Center Code: <strong class="font-mono text-slate-900">RISE-{{ str_pad($admin->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Certificate ID: <strong class="font-mono text-blue-900">{{ $certificate->certificate_id }}</strong></div>
                <div>Authorized Date: <strong class="font-mono text-slate-900">{{ $certificate->issue_date ? $certificate->issue_date->format('d-m-Y') : date('d-m-Y') }}</strong></div>
            </div>

            <div class="grid grid-cols-2 items-end pt-6 text-xs text-slate-700 max-w-md mx-auto">
                <div>
                    <div class="w-32 border-b border-slate-400 mx-auto mb-1"></div>
                    <span class="font-medium">Verification Officer</span>
                </div>
                <div>
                    <div class="w-32 border-b border-slate-400 mx-auto mb-1"></div>
                    <span class="font-bold text-slate-900">National Director</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

