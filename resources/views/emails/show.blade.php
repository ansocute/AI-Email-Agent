@extends('layouts.app')

@section('title', $email->subject)

@section('content')

<div class="max-w-7xl mx-auto">

```
{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
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
            <div class="flex-shrink-0">
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
    <a href="{{ route('emails.index') }}"
       class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900">

        <svg class="mr-2 h-5 w-5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>

        Back to Emails
    </a>
</div>


{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">
        {{ $email->subject }}
    </h1>

    <p class="mt-1 text-sm text-gray-500">
        Email ID: {{ $email->id }}
    </p>
</div>


{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    {{-- ===================================================== --}}
    {{-- ORIGINAL EMAIL --}}
    {{-- ===================================================== --}}

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

            <h2 class="text-lg font-semibold text-gray-900">
                Original Email
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Nội dung email gốc
            </p>

        </div>


        <div class="px-6 py-6">

            {{-- From --}}
            <div class="mb-5">

                <p class="text-sm font-medium text-gray-500">
                    From
                </p>

                <p class="mt-1 text-sm font-medium text-gray-900 break-all">
                    {{ $email->sender }}
                </p>

            </div>


            {{-- Subject --}}
            <div class="mb-5">

                <p class="text-sm font-medium text-gray-500">
                    Subject
                </p>

                <p class="mt-1 text-sm font-medium text-gray-900">
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

                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 whitespace-pre-wrap break-words font-serif leading-relaxed">
                    {{ $email->content }}
                </div>

            </div>

        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- AI DRAFT --}}
    {{-- ===================================================== --}}

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">

            <div class="flex justify-between items-center">

                <div>

                    <h2 class="text-lg font-semibold text-indigo-900">
                        AI Draft Reply
                    </h2>

                    <p class="text-sm text-indigo-700 mt-1">
                        AI generated response
                    </p>

                </div>


                {{-- Generate button --}}
                @if(!$draft)

                    <form action="{{ route('emails.generate-draft', $email) }}"
                          method="POST">

                        @csrf

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">

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
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 text-sm text-gray-900">{{ old('content', $draft->content) }}</textarea>


                            @error('content')

                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Save --}}
                        <div class="flex justify-end">

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">

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
                                Email sẽ được gửi đến:
                            </p>

                            <p class="mt-1 text-sm font-medium text-gray-700 break-all">
                                {{ $email->sender }}
                            </p>

                        </div>


                        <form action="{{ route('emails.send', $email) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn gửi email này không?');">

                            @csrf

                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 rounded-md text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">

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
                                class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">

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
```

</div>

@endsection
