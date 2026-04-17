@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="m-0 text-gray-900 text-2xl font-semibold">🔔 Notifications</h1>
            <p class="text-gray-500 text-sm mt-1">All system notifications for your account</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    ✓ Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-5 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @php
    $typeColors = [
        'ticket'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'border' => 'border-orange-200'],
        'job'     => ['bg' => 'bg-green-50',  'text' => 'text-green-600',  'border' => 'border-green-200'],
        'asset'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'border' => 'border-blue-200'],
        'visit'   => ['bg' => 'bg-teal-50',   'text' => 'text-teal-600',   'border' => 'border-teal-200'],
        'employee'=> ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
        'system'  => ['bg' => 'bg-gray-50',   'text' => 'text-gray-500',   'border' => 'border-gray-200'],
    ];
    @endphp

    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
            $data  = $notification->data;
            $type  = $data['type'] ?? 'system';
            $cc    = $typeColors[$type] ?? $typeColors['system'];
            $unread = is_null($notification->read_at);
            @endphp

            <div class="bg-white rounded-xl border {{ $cc['border'] }} {{ $unread ? 'border-l-4' : '' }} flex items-start gap-4 p-4 {{ $unread ? 'shadow-sm' : 'opacity-75' }} transition-all">
                {{-- Icon --}}
                <div class="text-2xl mt-0.5 shrink-0">{{ $data['icon'] ?? '🔔' }}</div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-sm font-semibold text-gray-800">{{ $data['title'] ?? '' }}</span>
                            @if($unread)
                                <span class="ml-2 inline-block w-2 h-2 rounded-full bg-blue-500 align-middle"></span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 shrink-0 mt-0.5">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $data['body'] ?? '' }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}"
                               onclick="markRead('{{ $notification->id }}')"
                               class="text-xs text-blue-600 hover:underline font-medium">
                                View →
                            </a>
                        @endif
                        @if($unread)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 bg-transparent border-none p-0 cursor-pointer">
                                    Mark read
                                </button>
                            </form>
                        @endif
                        <span class="text-xs {{ $cc['text'] }} {{ $cc['bg'] }} px-2 py-0.5 rounded-full">{{ ucfirst($type) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 px-4 py-16 text-center text-gray-400">
                <div class="text-5xl mb-3">🔔</div>
                <div class="font-medium text-gray-600">You're all caught up!</div>
                <div class="text-sm mt-1">No notifications yet. They'll appear here as activity happens.</div>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
