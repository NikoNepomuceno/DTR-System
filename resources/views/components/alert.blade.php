@props(['type' => 'info', 'message' => ''])

@php
    $colors = [
        'success' => 'bg-green-100 text-green-800',
        'error' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];
@endphp

@if($message)
    <div class="p-4 rounded mb-4 {{ $colors[$type] ?? $colors['info'] }}">
        {{ $message }}
    </div>
@endif 