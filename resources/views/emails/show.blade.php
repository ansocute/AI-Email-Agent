@extends('layouts.app')

@section('title', $email->subject)

@section('content')

<div class="max-w-7xl mx-auto">

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-green-400"
                     viewBox="0 0 20 20"
                     fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l3-3z"
                          clip-rule="evenodd"/>
                </svg>
            </div>

            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif


@if(session('error'))
    <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-red-400"
                     viewBox="0 0 20 20"
                     fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.293a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l-1.414 1.414 2 2a1 1 0 001.414 0l3-3z"
                          clip-rule="evenodd"/>
                </svg>
            </div>

            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
@endif


{{-- Back Button --}}
<div class="mb-6">
     <a href="{{ route('dashboard') }}"
         class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-teal-700">

        <svg class="h-5 w-5 transition-transform group-hover:-translate-x-1"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>

        Quay lại hộp thư
    </a>
</div>


{{-- Page Header --}}
<div class="mb-8 rounded-2xl border border-slate-200 bg-white px-6 py-7 shadow-sm sm:px-8">
    <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-teal-700">Chi tiết thư</p>
    <h1 class="wrap-break-word text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
        {{ $email->subject }}
    </h1>

    <p class="mt-2 text-sm text-slate-500">
        Email #{{ $email->id }} · {{ $email->received_at?->format('d/m/Y H:i') ?? 'Chưa có thời gian nhận' }}
    </p>
</div>


{{-- Main Grid --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


    {{-- ===================================================== --}}
    {{-- ORIGINAL EMAIL --}}
    {{-- ===================================================== --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

            <h2 class="text-lg font-semibold text-slate-900">
                Email gốc
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Nội dung email gốc
            </p>

        </div>


        <div class="space-y-6 px-6 py-6">

            {{-- From --}}
            <div>

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Người gửi
                </p>

                <p class="mt-1 break-all text-sm font-medium text-slate-800">
                    {{ $email->sender }}
                </p>

            </div>


            {{-- Subject --}}
            <div>

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Tiêu đề
                </p>

                <p class="mt-1 text-sm font-medium text-slate-800">
                    {{ $email->subject }}
                </p>

            </div>


            {{-- Received --}}
            @if($email->received_at)
                <div class="mb-5">

                    <p class="text-sm font-medium text-gray-500">
                        Received
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $email->received_at->format('d/m/Y H:i') }}
                    </p>

                </div>
            @endif


            {{-- Content --}}
            <div>

                <p class="text-sm font-medium text-gray-500 mb-2">
                    Message
                </p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 font-serif leading-relaxed text-slate-700 whitespace-pre-wrap wrap-break-word">
                    {{ $email->content }}
                </div>

            </div>

        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- AI DRAFT --}}
    {{-- ===================================================== --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Header --}}
        <div class="border-b border-teal-100 bg-teal-50 px-6 py-5">

            <div class="flex justify-between items-center">

                <div>

                    <h2 class="text-lg font-semibold text-teal-950">
                        AI Draft Reply
                    </h2>

                    <p class="mt-1 text-sm text-teal-700">
                        AI generated response
                    </p>

                </div>


                {{-- Generate button --}}
                @if(!$draft)

                    <form action="{{ route('emails.generate-draft', $email) }}"
                          method="POST">

                        @csrf

                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500">

                            <svg class="mr-2 h-4 w-4"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>

                            </svg>

                            Generate AI Draft

                        </button>

                    </form>

                @endif

            </div>

        </div>



        {{-- Draft Content --}}
        <div class="px-6 py-6">

            @if($draft)

                {{-- Status --}}
                <div class="mb-5">

                    @if($draft->status === 'sent')

                        <div class="flex items-center rounded-md bg-green-50 border border-green-200 px-4 py-3">

                            <svg class="h-5 w-5 text-green-500 mr-2"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7"/>

                            </svg>

                            <span class="text-sm font-medium text-green-800">
                                Email đã được gửi thành công
                            </span>

                        </div>

                    @else

                        <div class="flex items-center rounded-md bg-yellow-50 border border-yellow-200 px-4 py-3">

                            <svg class="h-5 w-5 text-yellow-500 mr-2"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 9v2m0 4h.01M10.29 3.86l-7.82 14a1 1 0 001.74 1.74h15.58a1 1 0 001.74-1.74l-7.82-14a1 1 0 00-3.48 0z"/>

                            </svg>

                            <span class="text-sm font-medium text-yellow-800">
                                Draft chưa được gửi
                            </span>

                        </div>

                    @endif

                </div>



                {{-- Edit Draft --}}
                @if($draft->status !== 'sent')

                    <form action="{{ route('emails.update-draft', $email) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <div class="mb-5">

                            <label for="content"
                                   class="block text-sm font-medium text-gray-700 mb-2">

                                Review & Edit Draft

                            </label>


                            <textarea
                                id="content"
                                name="content"
                                rows="14"
                                required
                                maxlength="10000"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 p-4 text-sm text-slate-900 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('content', $draft->content) }}</textarea>


                            @error('content')

                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Save --}}
                        <div class="flex justify-end">

                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500">

                                <svg class="mr-2 h-4 w-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"/>

                                </svg>

                                Save Draft

                            </button>

                        </div>

                    </form>



                    {{-- ================================================= --}}
                    {{-- SEND EMAIL --}}
                    {{-- ================================================= --}}

                    <div class="mt-6 pt-6 border-t border-gray-200">

                        <div class="mb-4">

                            <h3 class="text-sm font-semibold text-gray-900">
                                Ready to send?
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Bạn có thể thay đổi người nhận trước khi gửi.
                            </p>

                        </div>


                        <form action="{{ route('emails.send-draft', $email) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn gửi email này không?');">

                            @csrf

                            <label for="recipient" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Người nhận
                            </label>
                            <input
                                id="recipient"
                                name="recipient"
                                type="email"
                                required
                                value="{{ old('recipient', $email->sender_email ?? $email->sender) }}"
                                placeholder="nguoi.nhan@example.com"
                                class="mb-4 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            @error('recipient')
                                <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500">

                                <svg class="mr-2 h-5 w-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M3 10l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V10z"/>

                                </svg>

                                Send Email

                            </button>

                        </form>

                    </div>



                    {{-- ================================================= --}}
                    {{-- REGENERATE --}}
                    {{-- ================================================= --}}

                    <div class="mt-6 pt-6 border-t border-gray-200">

                        <p class="text-sm text-gray-500 mb-2">
                            Not satisfied with the draft?
                        </p>

                        <form action="{{ route('emails.generate-draft', $email) }}"
                              method="POST">

                            @csrf

                            <button type="submit"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900">

                                Regenerate Draft

                            </button>

                        </form>

                    </div>


                @else

                    {{-- ================================================= --}}
                    {{-- SENT STATE --}}
                    {{-- ================================================= --}}

                    <div class="rounded-lg bg-green-50 border border-green-200 p-5">

                        <h3 class="text-base font-semibold text-green-900">
                            Email Sent
                        </h3>

                        <p class="mt-2 text-sm text-green-800">
                            Email này đã được gửi thành công.
                        </p>

                        <p class="mt-2 text-sm text-green-700">
                            Không thể chỉnh sửa hoặc gửi lại draft này.
                        </p>

                    </div>

                @endif


            @else

                {{-- ================================================= --}}
                {{-- NO DRAFT --}}
                {{-- ================================================= --}}

                <div class="text-center py-12">

                    <svg class="mx-auto h-12 w-12 text-gray-400"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 01-1.806.547M8 4h8l-1 1v5.172a6 6 0 002.121 4.546l1.415 1.414A2 2 0 0116.13 18H7.87a2 2 0 01-1.415-3.414l1.415-1.414A6 6 0 0010 10.172V5L8 4z"/>

                    </svg>


                    <h3 class="mt-4 text-sm font-semibold text-gray-900">
                        No draft exists yet
                    </h3>


                    <p class="mt-2 text-sm text-gray-500">
                        Click "Generate AI Draft" to create an AI response.
                    </p>


                    <form action="{{ route('emails.generate-draft', $email) }}"
                          method="POST"
                          class="mt-6">

                        @csrf

                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500">

                            <svg class="mr-2 h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>

                            </svg>

                            Generate AI Draft

                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>

</div>

</div>

@endsection
