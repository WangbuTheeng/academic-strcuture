@extends('layouts.super-admin')

@section('title', 'Audit Log Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Audit Log Details</h1>
                    <p class="mt-2 text-sm text-gray-600">Detailed information about this audit log entry</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('super-admin.audit.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Audit Logs
                    </a>
                </div>
            </div>
        </div>

        <!-- Audit Log Details Card -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $auditLog->action ?? 'Audit Log Entry' }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Performed on {{ $auditLog->created_at->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Action</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(str_contains($auditLog->action ?? '', 'created')) bg-green-100 text-green-800
                                @elseif(str_contains($auditLog->action ?? '', 'updated')) bg-blue-100 text-blue-800
                                @elseif(str_contains($auditLog->action ?? '', 'deleted')) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $auditLog->action ?? 'Unknown Action' }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">User</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($auditLog->user)
                                {{ $auditLog->user->name }} ({{ $auditLog->user->email }})
                            @else
                                System
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Resource Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $auditLog->resource_type ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Resource ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $auditLog->resource_id ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($auditLog->category === 'super_admin') bg-purple-100 text-purple-800
                                @elseif($auditLog->category === 'school_management') bg-blue-100 text-blue-800
                                @elseif($auditLog->category === 'authentication') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $auditLog->category ?? 'general')) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Severity</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($auditLog->severity === 'critical') bg-red-100 text-red-800
                                @elseif($auditLog->severity === 'warning') bg-yellow-100 text-yellow-800
                                @elseif($auditLog->severity === 'info') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($auditLog->severity ?? 'info') }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $auditLog->ip_address ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="text-xs text-gray-600 break-all">
                                {{ $auditLog->user_agent ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Timestamp</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $auditLog->created_at->format('F j, Y \a\t g:i:s A') }}
                            <span class="text-gray-500">({{ $auditLog->created_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Old Values -->
        @if($auditLog->old_values && !empty($auditLog->old_values))
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Previous Values</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Values before the change</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <pre class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if($auditLog->new_values && !empty($auditLog->new_values))
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">New Values</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Values after the change</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <pre class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
