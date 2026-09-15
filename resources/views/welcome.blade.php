<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập · Inbox Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #1B2430; --teal: #3E7C74; --paper: #F5F4F0; }
        body { font-family: 'Inter', sans-serif; background: var(--paper); color: #1C1E21; }
        .font-serif-brand { font-family: 'Fraunces', serif; }
        .mail-float { animation: float 5s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-8px) rotate(2deg); } }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden">
    <main class="mx-auto grid min-h-screen max-w-6xl items-center gap-10 px-5 py-8 lg:grid-cols-[1fr_0.86fr] lg:px-10">
        <section class="order-2 lg:order-1">
            <div class="mb-8 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1B2430] text-white shadow-lg">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-4.5 3v-3.5A2.5 2.5 0 0 1 3 14V7"/><path d="m5 7 6 4 6-4"/></svg>
                </div>
                <span class="font-serif-brand text-2xl tracking-tight text-[#1B2430]">Inbox Agent</span>
            </div>
            <div class="max-w-xl">
                <p class="mb-4 text-sm font-bold uppercase tracking-[0.22em] text-[#3E7C74]">Trợ lý email thông minh</p>
                <h1 class="font-serif-brand text-5xl leading-[1.05] tracking-tight text-[#1B2430] sm:text-6xl">Mọi cuộc trò chuyện, <span class="text-[#3E7C74]">đúng nhịp.</span></h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-[#6B6F76]">Đồng bộ hộp thư, phân loại email và tạo phản hồi chuyên nghiệp trong vài giây.</p>
            </div>
            <div class="mt-10 flex flex-wrap gap-3 text-sm text-[#6B6F76]">
                <span class="rounded-full border border-[#D8E6E2] bg-[#E7F0EE] px-4 py-2 text-[#3E7C74]">Phân loại tự động</span>
                <span class="rounded-full border border-[#E4E2DC] bg-white px-4 py-2">Soạn thư bằng AI</span>
            </div>
        </section>
        <section class="order-1 lg:order-2">
            <div class="relative mx-auto max-w-md overflow-hidden rounded-4xl border border-white/70 bg-white p-7 shadow-[0_24px_70px_rgba(27,36,48,0.14)] sm:p-9">
                <div class="absolute -right-12 -top-12 h-36 w-36 rounded-full bg-[#E7F0EE]"></div>
                <div class="relative mb-8 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#3E7C74]">Chào mừng trở lại</p>
                        <h2 class="mt-2 font-serif-brand text-3xl text-[#1B2430]">Mở hộp thư</h2>
                        <p class="mt-2 text-sm leading-6 text-[#6B6F76]">Đăng nhập để bắt đầu xử lý email của bạn.</p>
                    </div>
                    <div class="mail-float rounded-2xl bg-[#FCEFD9] p-3 text-[#966B0C] shadow-sm" aria-hidden="true">
                        <svg class="h-8 w-8" viewBox="0 0 32 32" fill="none"><rect x="4" y="7" width="24" height="18" rx="3" fill="#FFF8E8" stroke="currentColor" stroke-width="1.7"/><path d="m6 10 10 8 10-8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
                @auth
                    <p class="mb-5 rounded-xl bg-[#E7F0EE] px-4 py-3 text-sm text-[#3E7C74]">Xin chào, {{ Auth::user()->name }}!</p>
                    <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center rounded-xl bg-[#1B2430] px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-[#3E7C74]">Vào hộp thư</a>
                @else
                    <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center gap-3 rounded-xl bg-[#1B2430] px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-[#3E7C74]">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-[#4285F4]">G</span>
                        Tiếp tục với Google
                    </a>
                    <p class="mt-5 text-center text-xs leading-5 text-[#8A8D92]">Kết nối an toàn với tài khoản Google của bạn</p>
                @endauth
            </div>
        </section>
    </main>
</body>
</html>
