<x-super-admin-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Super Admin Dashboard</h1>
                <p class="mt-2 text-gray-600">Welcome to the central control center for multi-institutional academic operations.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Institutions</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_schools'] }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Active Institutions</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['active_schools'] }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Inactive Institutions</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['inactive_schools'] }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">New This Month</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['new_schools_this_month'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Summary Views</h2>
                <p class="mt-2 text-gray-600">Quickly access key areas of the system.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="{{ route('super-admin.schools.index') }}" class="bg-gray-50 p-6 rounded-lg hover:bg-gray-100 transition flex items-center">
                        <svg class="w-8 h-8 text-blue-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">View Schools</h3>
                            <p class="mt-2 text-gray-600">Manage all registered schools, view their status, and access their details.</p>
                        </div>
                    </a>
                    <a href="{{ route('super-admin.analytics.index') }}" class="bg-gray-50 p-6 rounded-lg hover:bg-gray-100 transition flex items-center">
                        <svg class="w-8 h-8 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">View Analytics</h3>
                            <p class="mt-2 text-gray-600">Explore detailed analytics and reports on system-wide performance.</p>
                        </div>
                    </a>
                    <a href="{{ route('super-admin.audit.index') }}" class="bg-gray-50 p-6 rounded-lg hover:bg-gray-100 transition flex items-center">
                        <svg class="w-8 h-8 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">View Audit Logs</h3>
                            <p class="mt-2 text-gray-600">Review system activity and security logs for all schools.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Recent Schools</h2>
                <p class="mt-2 text-gray-600">A summary of the most recently added schools.</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentSchools as $school)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $school->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $school->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-mono bg-blue-100 px-3 py-1 rounded text-blue-800 font-semibold">{{ $school->code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($school->status === 'active') bg-green-100 text-green-800
                                            @elseif($school->status === 'inactive') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($school->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $school->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('super-admin.schools.show', $school) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No schools found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Super Admin Features</h2>
                <p class="mt-2 text-gray-600">This panel provides comprehensive control over the entire multi-school system. As a super admin, you have access to the following key features:</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">School Management</h3>
                        <p class="mt-2 text-gray-600">Create, view, edit, and manage all schools within the system. You can also manage their status (active, inactive, suspended) and reset their credentials.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">System Analytics</h3>
                        <p class="mt-2 text-gray-600">Access detailed analytics and reports on system-wide performance, including school growth, user activity, and financial overviews.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Audit Logs</h3>
                        <p class="mt-2 text-gray-600">Monitor all significant activities and changes made across the system. The audit logs provide a detailed record of actions for security and accountability.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Data Isolation</h3>
                        <p class="mt-2 text-gray-600">Ensure that each school's data is securely isolated, preventing any unauthorized access between institutions.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Centralized Control</h3>
                        <p class="mt-2 text-gray-600">Manage the entire system from a single, centralized dashboard, with a clear overview of all schools and their activities.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">User Management</h3>
                        <p class="mt-2 text-gray-600">Oversee all user accounts across the system, with the ability to manage super admin and school admin roles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
