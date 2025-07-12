@extends('layouts.app')

@section('content')
<x-dash-nav />
<div class="container mx-auto py-8">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-3-3.87M4 21v-2a4 4 0 0 1 3-3.87m9-7A4 4 0 1 1 7 7a4 4 0 0 1 8 0z"/></svg>
            <span class="font-semibold text-lg text-accent dark:text-blue-400">Employee Management</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(isset($employees) && $employees->count() > 0)
                @foreach($employees as $employee)
                    <div class="border dark:border-gray-700 rounded-lg p-4 flex flex-col justify-between bg-white dark:bg-gray-800">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->name }}</h3>
                            <p class="text-gray-600 dark:text-white">{{ $employee->department }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full dark:bg-gray-200 dark:text-gray-900">ID: {{ $employee->employee_id }}</span>
                            @php
                                $status = $employee->getCurrentStatus();
                                $statusColors = [
                                    'Clocked In' => 'bg-green-100 text-green-800',
                                    'On Break' => 'bg-yellow-100 text-yellow-800',
                                    'Clocked Out' => 'bg-gray-100 text-gray-800',
                                    'Not Present' => 'bg-red-100 text-red-800'
                                ];
                                $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="{{ $color }} text-xs px-4 py-1 rounded-full">{{ $status }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-3 text-center py-8">
                    <div class="text-gray-400">No employees found</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 