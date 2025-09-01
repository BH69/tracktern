<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackTern - Tasks Management</title>
    
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
                <h2 class="text-lg font-semibold">TASKS MANAGEMENT</h2>
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
                            <a href="{{ route('coordinators.tasks-management') }}" class="flex items-center px-4 py-3 text-white rounded-lg font-semibold" style="background-color: #2a3866;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Tasks Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinators.logbook-review') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
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
            <!-- Header with New Task Button and Search -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">TASKS MANAGEMENT</h1>
                        <p class="text-gray-600">Assign, track, and manage intern tasks and assignments</p>
                    </div>
                </div>
                
                <!-- Action Bar -->
                <div class="flex items-center justify-between mt-6">
                    <!-- Left: New Task Button -->
                    <a href="{{ route('coordinators.tasks-management.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>New Task</span>
                    </a>
                    
                    <!-- Right: Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" class="block w-80 pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Search Student...">
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tasks Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Table Headers -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="grid grid-cols-8 gap-4">
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Date
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Assigned To
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Company
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Supervisor
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Task Assigned
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Status
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Due Date
                        </div>
                        <div class="bg-purple-600 text-white text-center py-3 px-4 rounded-full font-semibold text-sm">
                            Actions
                        </div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    @if(isset($tasks) && $tasks->count() > 0)
                        @foreach($tasks as $task)
                            <div class="px-6 py-4">
                                <div class="grid grid-cols-8 gap-4 items-center">
                                    <!-- Date -->
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">{{ $task->date->format('M j, Y') }}</p>
                                    </div>
                                    
                                    <!-- Assigned To -->
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-900">{{ $task->assignedUser->name }}</p>
                                    </div>
                                    
                                    <!-- Company -->
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">{{ $task->company }}</p>
                                    </div>
                                    
                                    <!-- Supervisor -->
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">{{ $task->supervisor }}</p>
                                    </div>
                                    
                                    <!-- Task Assigned -->
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600 max-w-xs truncate" title="{{ $task->task_assigned }}">
                                            {{ Str::limit($task->task_assigned, 30) }}
                                        </p>
                                    </div>
                                    
                                    <!-- Status -->
                                    <div class="text-center">
                                        @if($task->status === 'pending')
                                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                                Pending
                                            </span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                                In Progress
                                            </span>
                                        @elseif($task->status === 'completed')
                                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                                Completed
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Due Date -->
                                    <div class="text-center">
                                        <p class="text-sm {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $task->due_date->format('M j, Y') }}
                                            @if($task->due_date->isPast() && $task->status !== 'completed')
                                                <br><span class="text-xs">(Overdue)</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800 p-1" title="View Task Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 p-1" title="Edit Task">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 p-1" title="Delete Task">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Empty State -->
                        <div class="px-6 py-16 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Tasks Yet</h3>
                            <p class="mt-2 text-gray-500">Get started by creating your first task assignment for an intern.</p>
                            <div class="mt-6">
                                <a href="{{ route('coordinators.tasks-management.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create First Task
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>


