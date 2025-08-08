@if(isset($schoolContext))
    <div class="flex items-center space-x-3 px-4 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-200 shadow-sm">
        <!-- School Logo -->
        @if(isset($schoolBranding['logo']) && $schoolBranding['logo'])
            <img src="{{ asset('storage/' . $schoolBranding['logo']) }}"
                 alt="{{ $schoolContext['school_name'] }}"
                 class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm">
        @else
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-sm">
                @if($schoolContext['is_super_admin'])
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                @else
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                @endif
            </div>
        @endif

        <!-- School Information -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-3">
                @if($schoolContext['is_super_admin'])
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        SUPER ADMIN
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $schoolContext['school_code'] }}
                    </span>
                @endif

                <div>
                    <div class="text-sm font-semibold text-gray-900 truncate">
                        {{ $schoolContext['school_name'] }}
                    </div>
                    @if(!$schoolContext['is_super_admin'])
                        <div class="text-xs text-gray-600">
                            School ID: {{ $schoolContext['school_id'] }} • {{ auth()->user()->name }}
                        </div>
                    @else
                        <div class="text-xs text-gray-600">
                            Global System Management
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
            @if($schoolContext['is_super_admin'])
                <!-- Super Admin Quick Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('super-admin.schools.create') }}"
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition-all duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New School
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-3 py-1.5 border border-indigo-300 shadow-sm text-xs leading-4 font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Quick Access
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
                            <div class="py-2">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                    Quick Actions
                                </div>
                                <a href="{{ route('super-admin.dashboard') }}"
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Dashboard</div>
                                        <div class="text-xs text-gray-500">System overview</div>
                                    </div>
                                </a>
                                <a href="{{ route('super-admin.schools.index') }}"
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Manage Schools</div>
                                        <div class="text-xs text-gray-500">View all schools</div>
                                    </div>
                                </a>
                                <a href="{{ route('super-admin.schools.create') }}"
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-900 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Create School</div>
                                        <div class="text-xs text-gray-500">Add new school</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- School User Status -->
                <div class="flex items-center space-x-2">
                    <div class="text-xs text-gray-600 text-right">
                        <div class="font-medium">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                        <div class="text-gray-500">{{ now()->format('H:i') }}</div>
                    </div>
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-400 to-green-500 flex items-center justify-center shadow-sm">
                        <div class="h-2 w-2 bg-white rounded-full"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
