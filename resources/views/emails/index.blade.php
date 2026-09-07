@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Inbox (Mock)</h3>
    </div>
    <ul class="divide-y divide-gray-200">
        @forelse ($emails as $email)
            <li>
                <a href="{{ route('emails.show', $email->id) }}" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">{{ $email->subject }}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                @if($email->category)
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $email->category }}
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    From: {{ $email->sender }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <p>
                                    Received: <time datetime="{{ $email->received_at }}">{{ $email->received_at }}</time>
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="px-4 py-5 text-gray-500">No emails found. Run DB seeder to populate.</li>
        @endforelse
    </ul>
</div>
@endsection
