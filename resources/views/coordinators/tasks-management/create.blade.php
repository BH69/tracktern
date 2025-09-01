<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackTern - Create New Task</title>
    
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
                <h2 class="text-lg font-semibold">CREATE NEW TASK</h2>
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
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Create New Task</h1>
                        <p class="text-gray-600">Assign a new task to an intern</p>
                    </div>
                    <a href="{{ route('coordinators.tasks-management') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Back to Tasks</span>
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Create Task Form -->
            <form method="POST" action="{{ route('coordinators.tasks-management.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg overflow-hidden">
                @csrf
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                            <input type="date" name="date" id="date" required
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Assigned To (Student Dropdown) -->
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To *</label>
                            <select name="assigned_to" id="assigned_to" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Select Student</option>
                                @isset($students)
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('assigned_to') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <!-- Sample students - replace with dynamic data -->
                                    <option value="1" {{ old('assigned_to') == '1' ? 'selected' : '' }}>John Doe</option>
                                    <option value="2" {{ old('assigned_to') == '2' ? 'selected' : '' }}>Sarah Johnson</option>
                                    <option value="3" {{ old('assigned_to') == '3' ? 'selected' : '' }}>Alex Martinez</option>
                                    <option value="4" {{ old('assigned_to') == '4' ? 'selected' : '' }}>Maria Rodriguez</option>
                                    <option value="5" {{ old('assigned_to') == '5' ? 'selected' : '' }}>David Kim</option>
                                @endisset
                            </select>
                        </div>

                        <!-- Company -->
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                            <input type="text" name="company" id="company" required
                                   value="{{ old('company') }}"
                                   placeholder="e.g., TechCorp Inc."
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Supervisor -->
                        <div>
                            <label for="supervisor" class="block text-sm font-medium text-gray-700 mb-2">Supervisor *</label>
                            <input type="text" name="supervisor" id="supervisor" required
                                   value="{{ old('supervisor') }}"
                                   placeholder="e.g., Jane Smith"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                            <input type="date" name="due_date" id="due_date" required
                                   value="{{ old('due_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Task File Attachment -->
                        <div>
                            <label for="task_file" class="block text-sm font-medium text-gray-700 mb-2">Task File Attachment</label>
                            <input type="file" name="task_file" id="task_file" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG, GIF (Max: 10MB)</p>
                        </div>

                        <!-- Task Assigned (Full Width) -->
                        <div class="md:col-span-2">
                            <label for="task_assigned" class="block text-sm font-medium text-gray-700 mb-2">Task Assigned *</label>
                            <textarea name="task_assigned" id="task_assigned" required rows="4"
                                      placeholder="Describe the task to be assigned..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('task_assigned') }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-8">
                        <a href="{{ route('coordinators.tasks-management') }}" 
                           class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                            Create Task
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
