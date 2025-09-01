<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackTern - Assigned Tasks</title>
    
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
            <!-- Left: Logo and Dashboard Label -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold">TrackTern</h1>
                <span class="text-purple-200">|</span>
                <h2 class="text-lg font-semibold">ASSIGNED TASKS</h2>
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
                        @if(isset($student) && $student->profile_picture)
                            <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover border-2 border-white">
                        @else
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center border-2 border-white">
                                <span class="text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="font-medium">{{ Auth::user()->name ?: 'Student' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <a href="{{ route('student.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                            <a href="{{ route('student.dashboard') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.requirements-checklist') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Requirements Checklist
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.assigned-tasks') }}" class="flex items-center px-4 py-3 text-white rounded-lg font-semibold" style="background-color: #2a3866;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Assigned Tasks
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.logbook') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Logbook
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.documentions-uploads-output') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Documents & Uploads
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.evaluation-forms') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Evaluation Forms
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 bg-blue-50 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Assigned Tasks</h1>
                <p class="text-gray-600">View and manage your assigned tasks from coordinators</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tasks Container -->
            <div class="bg-white rounded-lg shadow-md">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Your Tasks</h2>
                            <p class="text-gray-600">Tasks assigned to you by coordinators</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Filter by Status -->
                            <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">All Tasks</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="p-6">
                    @if(isset($tasks) && $tasks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tasks as $task)
                                <!-- Task Card -->
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <!-- Card Header -->
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-800 truncate">{{ $task->company }}</h3>
                                            @if($task->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    In Progress
                                                </span>
                                            @elseif($task->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Supervisor: {{ $task->supervisor }}</p>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="px-6 py-4">
                                        <!-- Task Description -->
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Task Description:</h4>
                                            <p class="text-gray-700 text-sm leading-relaxed">{{ $task->task_assigned }}</p>
                                        </div>

                                        <!-- Task Dates -->
                                        <div class="space-y-3">
                                            <!-- Date Assigned -->
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v14a2 2 0 002 2z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500">Date Assigned</p>
                                                    <p class="text-sm font-medium text-gray-800">{{ $task->date->format('M j, Y') }}</p>
                                                </div>
                                            </div>

                                            <!-- Due Date -->
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-500' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500">Due Date</p>
                                                    <p class="text-sm font-medium {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600' : 'text-gray-800' }}">
                                                        {{ $task->due_date->format('M j, Y') }}
                                                        @if($task->due_date->isPast() && $task->status !== 'completed')
                                                            <span class="text-xs text-red-500 font-normal">(Overdue)</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- File Attachment -->
                                        @if($task->task_file_path)
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <a href="{{ asset('storage/' . $task->task_file_path) }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800 text-sm">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    {{ $task->task_file_name ?: 'View Attachment' }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-lg">
                                        @if($task->status === 'pending')
                                            <form action="{{ route('student.assigned-tasks.start', $task) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                    Start Task
                                                </button>
                                            </form>
                                        @elseif($task->status === 'in_progress')
                                            <form action="{{ route('student.assigned-tasks.complete', $task) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                    Mark Complete
                                                </button>
                                            </form>
                                        @else
                                            <div class="text-center text-sm text-gray-500">
                                                Task Completed
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- No Tasks State -->
                        <div class="text-center py-16">
                            <div class="mx-auto max-w-md">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No Tasks Assigned</h3>
                                <p class="mt-2 text-gray-500">You don't have any tasks assigned to you yet. Check back later or contact your coordinator.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
