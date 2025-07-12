@extends('layouts.app')

@section('content')
    <x-employee-nav />
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="card rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-primary">DTR History</h1>
                    <p class="text-secondary">View your past time records</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-muted">Total Records</div>
                    <div class="text-2xl font-bold interactive-primary">{{ $dtrs->total() }}</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card rounded-xl p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Month</label>
                    <select class="input-field">
                        <option>All Months</option>
                        <option>January 2024</option>
                        <option>February 2024</option>
                        <option>March 2024</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Status</label>
                    <select class="input-field">
                        <option>All Status</option>
                        <option>Present</option>
                        <option>Absent</option>
                        <option>Late</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="btn-primary">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-primary border-opacity-20">
                <h2 class="text-lg font-semibold text-primary">Time Records</h2>
            </div>

            @if($dtrs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-secondary">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Time
                                    In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Time
                                    Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Break
                                    Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total
                                    Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary divide-opacity-20">
                            @foreach($dtrs as $dtr)
                                <tr class="hover:bg-secondary hover:bg-opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary">
                                            {{ $dtr->date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-muted">
                                            {{ $dtr->date->format('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary">
                                        {{ $dtr->getTimeInFormatted() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary">
                                        {{ $dtr->getTimeOutFormatted() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary">
                                        {{ $dtr->break_hours }}h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary">
                                            {{ $dtr->total_hours }}h
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'present' => 'bg-success bg-opacity-10 text-success',
                                                'absent' => 'bg-error bg-opacity-10 text-error',
                                                'late' => 'bg-warning bg-opacity-10 text-warning',
                                                'half-day' => 'bg-warning bg-opacity-10 text-warning'
                                            ];
                                            $color = $statusColors[$dtr->status] ?? 'bg-tertiary text-muted';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ ucfirst($dtr->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-primary border-opacity-20">
                    {{ $dtrs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-primary">No records found</h3>
                    <p class="mt-1 text-sm text-secondary">Get started by clocking in for your first day.</p>
                </div>
            @endif
        </div>
    </div>
@endsection