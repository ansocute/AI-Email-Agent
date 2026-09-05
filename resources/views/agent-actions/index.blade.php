<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đề xuất AI & Nhật ký — AI Email Agent</title>
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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-[#8891A0] hover:text-white transition">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: #45505F;"></span>
                    Hộp thư
                </a>
                <a href="{{ route('actions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium" style="background: var(--navy-soft);">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: var(--teal);"></span>
                    Đề xuất AI
                    @php $pendingCount = $actions->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto text-[11px] font-semibold px-2 py-0.5 rounded-full" style="background: #FCEFD9; color: #966B0C;">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
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
        <main class="flex-1 px-10 py-8 max-w-5xl">

            <div class="flex items-start justify-between mb-8">
                <div>
                    <h1 class="font-serif-brand text-3xl">Đề xuất của AI</h1>
                    <p class="text-sm mt-1" style="color: var(--ink-soft);">
                        Xem và phê duyệt các hành động tự động do AI đề xuất (tạo sự kiện lịch, trả lời email).
                    </p>
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 text-sm px-4 py-3 rounded-lg flex items-center gap-2 border" style="background: var(--teal-soft); color: var(--teal); border-color: #C2DDD8;">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 text-sm px-4 py-3 rounded-lg flex items-center gap-2 border" style="background: #FBE8E8; color: #B23B3B; border-color: #F4C4C4;">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Danh sách agent_actions --}}
            <section class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-serif-brand text-xl">Danh sách hành động</h2>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-md" style="background: white; border: 1px solid var(--line); color: var(--ink-soft);">
                        Tổng cộng: {{ $actions->total() }} đề xuất
                    </span>
                </div>

                @if($actions->isEmpty())
                    <div class="text-center py-16 rounded-xl border border-dashed bg-white/50" style="border-color: var(--line);">
                        <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center text-xl" style="background: var(--teal-soft); color: var(--teal);">
                            ✨
                        </div>
                        <p class="font-serif-brand text-lg mb-1">Chưa có đề xuất nào</p>
                        <p class="text-sm" style="color: var(--ink-soft);">
                            Khi AI phân tích email và gợi ý hành động (hoặc khi thêm data giả bằng Tinker), danh sách sẽ hiển thị tại đây.
                        </p>
                    </div>
                @else
                    <div class="rounded-xl border overflow-hidden shadow-sm" style="border-color: var(--line); background: white;">
                        <div class="divide-y" style="border-color: var(--line);">
                            @foreach($actions as $action)
                                <div class="p-5 flex flex-col md:flex-row md:items-start justify-between gap-4 hover:bg-slate-50/50 transition">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            {{-- Type badge --}}
                                            @if($action->type === 'create_event')
                                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1" style="background: #EAF0FA; color: #2B579A;">
                                                    📅 Tạo sự kiện lịch
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1" style="background: #EBF7EE; color: #2D7738;">
                                                    ✉️ Soạn thảo trả lời
                                                </span>
                                            @endif

                                            {{-- Status badge --}}
                                            @if($action->status === 'approved')
                                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full" style="background: var(--teal-soft); color: var(--teal);">
                                                    ✓ Đã duyệt
                                                </span>
                                            @elseif($action->status === 'rejected')
                                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full" style="background: #FBE8E8; color: #B23B3B;">
                                                    ✕ Đã từ chối
                                                </span>
                                            @else
                                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full" style="background: #FCEFD9; color: #966B0C;">
                                                    ⏳ Chờ duyệt
                                                </span>
                                            @endif

                                            <span class="text-xs" style="color: var(--ink-soft);">
                                                #{{ $action->id }} • {{ $action->created_at?->diffForHumans() }}
                                            </span>
                                        </div>

                                        {{-- Email reference --}}
                                        @if($action->email)
                                            <div class="text-xs mb-1.5 flex items-center gap-1 text-slate-500">
                                                <span class="font-medium">Email liên quan:</span>
                                                <span class="text-slate-700 font-medium truncate">{{ $action->email->subject }}</span>
                                                <span class="text-slate-400">({{ $action->email->sender_name }})</span>
                                            </div>
                                        @endif

                                        {{-- Content --}}
                                        <div class="text-sm p-3 rounded-lg border whitespace-pre-line" style="background: #F9F9F8; border-color: var(--line); color: var(--ink);">
                                            {{ $action->content }}
                                        </div>
                                    </div>

                                    {{-- Actions (Duyệt / Từ chối) --}}
                                    <div class="shrink-0 flex md:flex-col items-center gap-2 pt-1">
                                        @if($action->status === 'pending')
                                            <form method="POST" action="{{ route('actions.approve', $action) }}" class="w-full">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full text-xs font-medium px-4 py-2 rounded-lg text-white transition flex items-center justify-center gap-1 shadow-sm hover:opacity-95"
                                                    style="background: var(--teal);">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Duyệt
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('actions.reject', $action) }}" class="w-full">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full text-xs font-medium px-4 py-2 rounded-lg transition flex items-center justify-center gap-1 border hover:bg-red-50"
                                                    style="color: #B23B3B; border-color: #F4C4C4; background: white;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Từ chối
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs italic text-center px-2 py-1" style="color: var(--ink-soft);">
                                                Đã xử lý
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $actions->links() }}
                    </div>
                @endif
            </section>

            {{-- Section Nhật ký hoạt động (Activity Logs) --}}
            <section class="mt-12 pt-8 border-t" style="border-color: var(--line);">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-serif-brand text-xl">Nhật ký hoạt động</h2>
                        <p class="text-xs mt-0.5" style="color: var(--ink-soft);">Lịch sử thao tác duyệt, từ chối và các hoạt động của hệ thống</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-md" style="background: white; border: 1px solid var(--line); color: var(--ink-soft);">
                        {{ count($logs ?? []) }} bản ghi gần nhất
                    </span>
                </div>

                @if(empty($logs) || $logs->isEmpty())
                    <div class="text-center py-10 rounded-xl border border-dashed bg-white/50" style="border-color: var(--line);">
                        <p class="text-sm" style="color: var(--ink-soft);">Chưa có nhật ký hoạt động nào được ghi lại.</p>
                    </div>
                @else
                    <div class="rounded-xl border overflow-hidden shadow-sm" style="border-color: var(--line); background: white;">
                        <div class="divide-y" style="border-color: var(--line);">
                            @foreach($logs as $log)
                                <div class="px-5 py-3.5 flex items-center justify-between text-sm hover:bg-slate-50/50 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0"
                                            style="background: var(--teal-soft); color: var(--teal);">
                                            {{ mb_strtoupper(mb_substr($log->user?->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-sm font-medium text-slate-800">{{ $log->action_description }}</span>
                                            @if($log->user)
                                                <span class="text-xs text-slate-400 ml-1.5">bởi {{ $log->user->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs shrink-0 text-slate-400 ml-3">
                                        {{ \Carbon\Carbon::parse($log->timestamp)->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

        </main>
    </div>
</body>
</html>
