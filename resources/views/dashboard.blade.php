<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hộp thư — AI Email Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #1B2430;
            --navy-soft: #26313F;
            --paper: #F5F4F0;
            --ink: #1C1E21;
            --ink-soft: #6B6F76;
            --teal: #3E7C74;
            --teal-soft: #E7F0EE;
            --line: #E4E2DC;
        }
        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); }
        .font-serif-brand { font-family: 'Fraunces', serif; }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-60 shrink-0 text-white flex flex-col" style="background: var(--navy);">
            <div class="px-6 py-7">
                <div class="font-serif-brand text-xl tracking-tight">Inbox Agent</div>
                <div class="text-xs mt-1" style="color: #8891A0;">Trợ lý email tự động</div>
            </div>
            <nav class="mt-4 flex-1 px-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium" style="background: var(--navy-soft);">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: var(--teal);"></span>
                    Hộp thư
                </a>
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm" style="color: #8891A0;">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: #45505F;"></span>
                    Nhật ký hoạt động
                    <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded" style="background: #2E3A49; color: #8891A0;">sắp có</span>
                </div>
            </nav>
            <div class="px-3 pb-5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm" style="color: #8891A0;">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 px-10 py-8 max-w-4xl">

            <div class="flex items-start justify-between mb-8">
                <div>
                    <h1 class="font-serif-brand text-3xl">Chào, {{ ucfirst(explode(' ', Auth::user()->name)[0]) }}</h1>
                    <p class="text-sm mt-1" style="color: var(--ink-soft);">
                        @if($emails->isNotEmpty())
                            Đồng bộ gần nhất {{ $emails->max('updated_at')->diffForHumans() }}
                        @else
                            Chưa có email nào được đồng bộ
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('emails.sync') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm font-medium px-4 py-2.5 rounded-lg text-white transition"
                        style="background: var(--teal);">
                        Đồng bộ ngay
                    </button>
                </form>
            </div>

            @if(session('synced') !== null)
                <div class="mb-6 text-sm px-4 py-3 rounded-lg" style="background: var(--teal-soft); color: var(--teal);">
                    Đã đồng bộ và phân loại {{ session('synced') }} email mới.
                </div>
            @endif

            @if($emails->isEmpty())
                <div class="text-center py-20 rounded-xl border border-dashed" style="border-color: var(--line);">
                    <p class="font-serif-brand text-lg mb-2">Hộp thư đang trống</p>
                    <p class="text-sm" style="color: var(--ink-soft);">Bấm "Đồng bộ ngay" để Agent lấy và phân loại email từ Gmail của bạn.</p>
                </div>
            @else
                <div class="rounded-xl border overflow-hidden" style="border-color: var(--line); background: white;">
                    @foreach($emails as $email)
                        <div class="flex items-start gap-4 px-5 py-4 {{ !$loop->last ? 'border-b' : '' }}" style="border-color: var(--line);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-medium shrink-0"
                                style="background: var(--teal-soft); color: var(--teal);">
                                {{ mb_strtoupper(mb_substr($email->sender_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium truncate">{{ $email->sender_name }}</span>
                                    <span class="text-xs shrink-0" style="color: var(--ink-soft);">
                                        {{ $email->received_at?->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm font-medium mt-0.5 truncate">{{ $email->subject }}</p>
                                <p class="text-sm mt-0.5 truncate" style="color: var(--ink-soft);">{{ $email->content }}</p>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full shrink-0"
                                style="background: {{ $email->category_badge['bg'] }}; color: {{ $email->category_badge['text'] }};">
                                {{ $email->category_badge['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

        </main>
    </div>
</body>
</html>