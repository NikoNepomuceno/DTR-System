@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto" style="max-height: 100vh;">
        <x-dash-nav />
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="stat-card rounded-xl p-4 flex items-center space-x-4" style="background: var(--card-blue-bg);">
                <div class="bg-blue-500 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-gray-800 text-sm">Today's Entries</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-900">{{ $todayEntries ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card rounded-xl p-4 flex items-center space-x-4" style="background: var(--card-green-bg);">
                <div class="bg-green-400 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8" />
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-gray-800 text-sm">Active Employees</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-900">{{ $activeEmployees ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card rounded-xl p-4 flex items-center space-x-4" style="background: var(--card-purple-bg);">
                <div class="bg-purple-400 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-gray-800 text-sm">Total Records</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-900">{{ $totalRecords ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card rounded-xl p-4 flex items-center space-x-4" style="background: var(--card-orange-bg);">
                <div class="bg-orange-400 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-gray-800 text-sm">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-900">{{ $thisMonth ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white border rounded-xl p-6 mb-8">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-semibold text-lg text-accent">Today's Activity</span>
            </div>
            @if(isset($recentEntries) && $recentEntries->count() > 0)
                <div class="space-y-3">
                    @foreach($recentEntries as $entry)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-green-500 rounded-full p-1 mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $entry->user->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $entry->user->employee_id }} •
                                        {{ $entry->user->department }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $entry->getTimeInFormatted() }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->getStatusText() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 dark:text-gray-500 py-12">No entries recorded today</div>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl p-6">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span class="font-semibold text-lg text-accent">Export DTR Report</span>
            </div>
            <form action="{{ route('dtr.export-pdf') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Employee</label>
                    <select name="user_id"
                        class="w-full border dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        required>
                        <option value="">Select employee</option>
                        @if(isset($employees))
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_id }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Start Date</label>
                    <input type="date" name="from_date"
                        class="w-full border dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        required>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">End Date</label>
                    <input type="date" name="to_date"
                        class="w-full border dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        required>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection