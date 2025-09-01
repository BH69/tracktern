<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackTern - Logbook Review</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <!-- Top Bar -->
    <div class="bg-purple-900 text-white shadow-lg">
        <div class="flex items-center justify-between px-6 py-4">
            <!-- Left: Logo and Page Label -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold">TrackTern</h1>
                <span class="text-purple-200">|</span>
                <h2 class="text-lg font-semibold">LOGBOOK REVIEW</h2>
            </div>
            
            <!-- Right: Notifications and Profile -->
            <div class="flex items-center space-x-6">
                <!-- Notifications -->
                <button class="relative p-2 hover:bg-purple-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.18-3.18M9 17h5L12 21m0-4V6a9 9 0 00-18 0v5.172m0 0a4 4 0 11-7.656 2.828L3 15m6 2.172v0a4 4 0 01-7.656-2.828L15.172 21"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </button>
                
                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 p-2 hover:bg-purple-800 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center border-2 border-white">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="hidden md:block">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="text-white w-64 min-h-screen shadow-lg" style="background-color: #354484;">
            <nav class="mt-8">
                <div class="px-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('coordinators.dashboard') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.create-intern') }}" class="flex items-center px-4 py-3 text-green-100 rounded-lg transition-colors" style="color: #bbf7d0;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#bbf7d0';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Intern
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.intern-progress-tracker') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Intern Progress Tracker
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.requirements-management') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Requirements Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.tasks-management') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Tasks Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.logbook-review') }}" class="flex items-center px-4 py-3 text-white rounded-lg font-semibold" style="background-color: #2a3866;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Logbook Review
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.internship-output-archive') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Internship Output Archive
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.evaluation-feedback') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Evaluation & Feedback
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 bg-yellow-50 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">LOGBOOK REVIEW</h1>
                <p class="text-gray-600">Review and evaluate intern logbook entries and daily activities</p>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                    @php
                        $totalEntries = $logbookEntries->count();
                        $pendingEntries = $logbookEntries->where('raw_status', 'completed')->count();
                        $approvedEntries = $logbookEntries->where('raw_status', 'approved')->count();
                        $rejectedEntries = $logbookEntries->where('raw_status', 'rejected')->count();
                    @endphp
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalEntries }}</div>
                        <div class="text-sm text-gray-600">Total Entries</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-2xl font-bold text-yellow-600">{{ $pendingEntries }}</div>
                        <div class="text-sm text-gray-600">Pending Review</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $approvedEntries }}</div>
                        <div class="text-sm text-gray-600">Approved</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-2xl font-bold text-red-600">{{ $rejectedEntries }}</div>
                        <div class="text-sm text-gray-600">Rejected</div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Logbook Entries Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Table Headers -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="grid grid-cols-8 gap-4">
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Name
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Date
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Time In
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Time Out
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Hours
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Notes
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Status
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Actions
                        </div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    @forelse($logbookEntries as $entry)
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-8 gap-4 items-center">
                                <!-- Name -->
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $entry['user_name'] }}</p>
                                </div>
                                
                                <!-- Date -->
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">{{ $entry['date'] }}</p>
                                </div>
                                
                                <!-- Time In -->
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">{{ $entry['time_in'] }}</p>
                                </div>
                                
                                <!-- Time Out -->
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">{{ $entry['time_out'] }}</p>
                                </div>
                                
                                <!-- Hours -->
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $entry['hours_logged'] }} hrs</p>
                                </div>
                                
                                <!-- Notes -->
                                <div class="text-center">
                                    @if($entry['notes'])
                                        <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $entry['notes'] }}">
                                            {{ Str::limit($entry['notes'], 30) }}
                                        </p>
                                    @else
                                        <span class="text-sm text-gray-400 italic">No notes</span>
                                    @endif
                                </div>
                                
                                <!-- Status -->
                                <div class="text-center">
                                    @if($entry['raw_status'] === 'completed')
                                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            Pending Review
                                        </span>
                                    @elseif($entry['raw_status'] === 'approved')
                                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            Approved
                                        </span>
                                    @elseif($entry['raw_status'] === 'rejected')
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            Rejected
                                        </span>
                                    @elseif($entry['raw_status'] === 'logged_in')
                                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $entry['raw_status'])) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="text-center">
                                    @if($entry['raw_status'] === 'completed')
                                        <div class="flex items-center justify-center space-x-2">
                                            <form method="POST" action="{{ route('coordinators.logbook-review.approve', $entry['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('coordinators.logbook-review.reject', $entry['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors" 
                                                        onclick="return confirm('Are you sure you want to reject this entry?')">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">No actions available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No logbook entries</h3>
                                <p class="mt-1 text-sm text-gray-500">No student logbook entries have been submitted yet.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</body>
</html>


