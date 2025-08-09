<x-super-admin-layout>
    <!-- Page Header -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">School Management</h1>
                <p class="mt-2 text-gray-600">Manage all schools in your academic management system</p>
            </div>
            <a href="{{ route('super-admin.schools.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New School
            </a>
        </div>


            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('admin_credentials'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-green-900">School Created Successfully!</h3>
                            <div class="mt-3 bg-white rounded-lg p-4 border border-green-200">
                                <h4 class="font-medium text-green-900 mb-2">School Login Credentials:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-green-800">School ID</label>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <code class="bg-green-100 px-3 py-1 rounded text-green-900 font-mono">{{ session('school_code') ?? 'N/A' }}</code>
                                            <button onclick="copyToClipboard('{{ session('school_code') ?? '' }}')" class="text-green-600 hover:text-green-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-green-800">Admin Email</label>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <code class="bg-green-100 px-3 py-1 rounded text-green-900 font-mono">{{ session('admin_credentials')['email'] }}</code>
                                            <button onclick="copyToClipboard('{{ session('admin_credentials')['email'] }}')" class="text-green-600 hover:text-green-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-green-800">Admin Password</label>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <code class="bg-green-100 px-3 py-1 rounded text-green-900 font-mono">{{ session('admin_credentials')['password'] }}</code>
                                        <button onclick="copyToClipboard('{{ session('admin_credentials')['password'] }}')" class="text-green-600 hover:text-green-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-green-700 mt-3">
                                <strong>Important:</strong> Provide these credentials to the school. They can log in at
                                <a href="{{ url('/login') }}" class="underline font-medium">{{ url('/login') }}</a>
                                and will have full access to Academic Structure, Examinations, Finance Management, and can create Teachers and Students.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('super-admin.schools.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by name or code..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <button type="submit" 
                                class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('super-admin.schools.index') }}" 
                               class="px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Password Reset Success Modal -->
            @if(session('new_password'))
                <div id="passwordResetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Password Reset Successful</h3>
                                <button type="button" onclick="closePasswordResetModal()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium text-green-900">New Password Generated</span>
                                    </div>
                                    <div class="text-sm text-green-800">
                                        <p><strong>School:</strong> {{ session('school_code') }}</p>
                                        <p><strong>New Password:</strong></p>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <input type="text" value="{{ session('new_password') }}" readonly
                                                   class="flex-1 px-3 py-2 bg-white border border-green-300 rounded-md text-sm font-mono">
                                            <button type="button" onclick="copyToClipboard('{{ session('new_password') }}', this)"
                                                    class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-xs">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="font-medium text-yellow-900">Important</span>
                                    </div>
                                    <div class="text-sm text-yellow-800">
                                        <p>Please save this password securely and share it with the school administrator. This password will not be shown again.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" onclick="closePasswordResetModal()"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Schools Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($schools as $school)
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
                                            <div class="flex items-center space-x-2">
                                                <span class="font-mono bg-blue-100 px-3 py-1 rounded text-blue-800 font-semibold">{{ $school->code }}</span>
                                                <button onclick="copyToClipboard('{{ $school->code }}')"
                                                        class="text-gray-400 hover:text-gray-600" title="Copy School ID">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($school->status === 'active') bg-green-100 text-green-800
                                                @elseif($school->status === 'inactive') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($school->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $school->users_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $school->students_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $school->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- View/Manage Button -->
                                                <a href="{{ route('super-admin.schools.show', $school) }}"
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View
                                                </a>

                                                <!-- Credentials Button -->
                                                <button onclick="showCredentials('{{ $school->code }}', '{{ $school->name }}')"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                    </svg>
                                                    Credentials
                                                </button>

                                                <!-- Status Toggle -->
                                                @if($school->status === 'active')
                                                    <button onclick="confirmAction('Are you sure you want to deactivate this school?', () => updateSchoolStatus('{{ $school->id }}', 'inactive'))"
                                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Deactivate
                                                    </button>
                                                @elseif($school->status === 'inactive')
                                                    <button onclick="updateSchoolStatus('{{ $school->id }}', 'active')"
                                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Activate
                                                    </button>
                                                @else
                                                    <button onclick="updateSchoolStatus('{{ $school->id }}', 'active')"
                                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Reactivate
                                                    </button>
                                                @endif

                                                <!-- Dropdown Menu -->
                                                <div class="relative inline-block text-left">
                                                    <button type="button" onclick="toggleDropdown('dropdown-{{ $school->id }}')"
                                                            class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <div id="dropdown-{{ $school->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                        <div class="py-1">
                                                            <a href="{{ route('super-admin.schools.edit', $school) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit School</a>
                                                            <button onclick="resetPassword('{{ $school->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reset Password</button>
                                                            <button onclick="viewAuditLog('{{ $school->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Audit Log</button>
                                                            @if($school->status !== 'suspended')
                                                                <button onclick="confirmAction('Are you sure you want to suspend this school?', () => updateSchoolStatus('{{ $school->id }}', 'suspended'))" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Suspend School</button>
                                                            @endif
                                                            <div class="border-t border-gray-100"></div>
                                                            <button onclick="deleteSchool('{{ $school->id }}', '{{ $school->name }}', {{ $school->users_count }}, {{ $school->students_count }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Delete School
                                                            </button>
                                                            <button onclick="forceDeleteSchool('{{ $school->id }}', '{{ $school->name }}')" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 font-medium">
                                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                </svg>
                                                                Force Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No schools found. <a href="{{ route('super-admin.schools.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first school</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($schools->hasPages())
                        <div class="mt-6">
                            {{ $schools->links() }}
                        </div>
                    @endif
                </div>
            </div>
    </div>

    <!-- Credentials Modal -->
    <div id="credentialsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">School Login Credentials</h3>
                    <button onclick="closeCredentialsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">School ID</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="text" id="schoolId" readonly
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 font-mono">
                            <button onclick="copyToClipboard(document.getElementById('schoolId').value)"
                                    class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Copy
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="password" id="schoolPassword" readonly
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 font-mono">
                            <button onclick="togglePasswordVisibility()"
                                    class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Show
                            </button>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <p class="text-sm text-blue-800">
                            <strong>Instructions:</strong> Provide these credentials to the school. They can use the School ID and Password to log in at
                            <a href="{{ url('/login') }}" class="underline">{{ url('/login') }}</a>
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeCredentialsModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Close
                    </button>
                    <button onclick="resetPassword()"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                toast.textContent = 'Copied to clipboard!';
                document.body.appendChild(toast);
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 2000);
            });
        }

        function showCredentials(schoolCode, schoolName) {
            document.getElementById('modalTitle').textContent = schoolName + ' - Login Credentials';
            document.getElementById('schoolId').value = schoolCode;
            document.getElementById('schoolPassword').value = '••••••••'; // Placeholder - you'll need to fetch actual password
            document.getElementById('credentialsModal').classList.remove('hidden');
        }

        function closeCredentialsModal() {
            document.getElementById('credentialsModal').classList.add('hidden');
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('schoolPassword');
            const button = event.target;
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                button.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                button.textContent = 'Show';
            }
        }

        function resetPassword() {
            if (confirm('Are you sure you want to reset the password for this school?')) {
                // Implement password reset functionality
                alert('Password reset functionality will be implemented');
            }
        }

        function activateSchool(schoolId) {
            if (confirm('Are you sure you want to activate this school?')) {
                updateSchoolStatus(schoolId, 'active');
            }
        }

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== dropdownId) {
                    d.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        function resetPassword(schoolId) {
            if (confirm('Are you sure you want to reset the password for this school? A new password will be generated.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/super-admin/schools/${schoolId}/reset-password`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';

                form.appendChild(csrfToken);
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewAuditLog(schoolId) {
            window.open(`/super-admin/audit?school_id=${schoolId}`, '_blank');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick*="toggleDropdown"]')) {
                const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
                allDropdowns.forEach(d => d.classList.add('hidden'));
            }
        });

        // Enhanced copy to clipboard with better feedback
        function copyToClipboard(text, button = null) {
            navigator.clipboard.writeText(text).then(function() {
                if (button) {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                    setTimeout(() => {
                        button.innerHTML = originalText;
                    }, 2000);
                }

                // Show toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
                toast.textContent = 'Copied to clipboard!';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                alert('Failed to copy to clipboard');
            });
        }

        // Credentials modal functions
        function showCredentials(schoolCode, schoolName) {
            document.getElementById('modal-school-name').textContent = schoolName;
            document.getElementById('modal-school-code').textContent = schoolCode;
            document.getElementById('credential-school-id').value = schoolCode;
            document.getElementById('credential-password').value = '••••••••••••'; // Placeholder
            document.getElementById('credentialsModal').classList.remove('hidden');
        }

        function closeCredentialsModal() {
            document.getElementById('credentialsModal').classList.add('hidden');
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('credential-password');
            const toggleButton = event.target;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordField.value = 'Contact super admin for actual password';
                toggleButton.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                passwordField.value = '••••••••••••';
                toggleButton.textContent = 'Show';
            }
        }

        function openLoginPage() {
            window.open('{{ route("login") }}', '_blank');
        }

        function closePasswordResetModal() {
            document.getElementById('passwordResetModal').style.display = 'none';
        }

        // Delete school functions
        function deleteSchool(schoolId, schoolName, userCount, studentCount) {
            let message = `Are you sure you want to delete "${schoolName}"?`;

            if (userCount > 0 || studentCount > 0) {
                message += `\n\nThis school has ${userCount} users and ${studentCount} students.`;
                message += `\nThe school will be deactivated instead of deleted to preserve data.`;
            } else {
                message += `\n\nThis action cannot be undone!`;
            }

            if (confirm(message)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/super-admin/schools/${schoolId}`;

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = '_token';
                tokenField.value = '{{ csrf_token() }}';
                form.appendChild(tokenField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function forceDeleteSchool(schoolId, schoolName) {
            const message = `⚠️ DANGER: Force delete "${schoolName}"?\n\nThis will PERMANENTLY DELETE:\n- The school\n- All users\n- All students\n- All academic data\n\nThis action CANNOT be undone!\n\nType "DELETE" to confirm:`;

            const confirmation = prompt(message);

            if (confirmation === 'DELETE') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/super-admin/schools/${schoolId}/force-delete`;

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = '_token';
                tokenField.value = '{{ csrf_token() }}';
                form.appendChild(tokenField);

                document.body.appendChild(form);
                form.submit();
            } else if (confirmation !== null) {
                alert('Force delete cancelled. You must type "DELETE" exactly to confirm.');
            }
        }
    </script>

    <!-- Credentials Modal -->
    <div id="credentialsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">School Credentials</h3>
                    <button type="button" onclick="closeCredentialsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-medium text-blue-900">School Information</span>
                        </div>
                        <div class="text-sm text-blue-800">
                            <p><strong>School Name:</strong> <span id="modal-school-name"></span></p>
                            <p><strong>School ID:</strong> <span id="modal-school-code" class="font-mono"></span></p>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span class="font-medium text-green-900">Login Credentials</span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-green-700 mb-1">School ID (Username)</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" id="credential-school-id" readonly
                                           class="flex-1 px-3 py-2 bg-white border border-green-300 rounded-md text-sm font-mono">
                                    <button type="button" onclick="copyToClipboard(document.getElementById('credential-school-id').value, this)"
                                            class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-xs">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-green-700 mb-1">Password</label>
                                <div class="flex items-center space-x-2">
                                    <input type="password" id="credential-password" readonly
                                           class="flex-1 px-3 py-2 bg-white border border-green-300 rounded-md text-sm">
                                    <button type="button" onclick="togglePasswordVisibility()"
                                            class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-xs">
                                        Show
                                    </button>
                                    <button type="button" onclick="copyToClipboard(document.getElementById('credential-password').value, this)"
                                            class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-xs">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-yellow-900">Login Instructions</span>
                        </div>
                        <div class="text-sm text-yellow-800">
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Go to the school login page</li>
                                <li>Enter the School ID as the username</li>
                                <li>Enter the provided password</li>
                                <li>Click "Sign in to School"</li>
                            </ol>
                            <p class="mt-2 text-xs">
                                <strong>Login URL:</strong>
                                <a href="{{ route('login') }}" target="_blank" class="text-yellow-700 underline hover:text-yellow-900">
                                    {{ route('login') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCredentialsModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Close
                    </button>
                    <button type="button" onclick="openLoginPage()"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Open Login Page
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
