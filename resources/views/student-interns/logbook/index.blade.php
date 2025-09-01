<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackTern - Daily Logbook</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Initialize logbook data for Alpine.js
        window.logbookData = {
            currentTimeIn: @json($todayEntry && $todayEntry->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $todayEntry->time_in)->format('g:i A') : null),
            currentTimeOut: @json($todayEntry && $todayEntry->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $todayEntry->time_out)->format('g:i A') : null),
            totalHoursToday: @json($todayEntry ? ($todayEntry->hours_logged ?? 0) : 0),
            isCompleted: @json($todayEntry && ($todayEntry->status === 'completed' || $todayEntry->status === 'approved' || $todayEntry->status === 'rejected')),
            totalLoggedHours: @json($totalLoggedHours ?? 0),
            requiredHours: @json($requiredHours ?? 486),
            logEntries: @json($formattedEntries ?? []),
            isLoading: false,
            isEntryFinalized: @json($isEntryFinalized ?? false),
            originalEntryStatus: @json($originalTodayEntry ? $originalTodayEntry->status : null),
            statusMessage: null
        };
        
        // Alpine.js component function
        function logbookComponent() {
            return {
                ...window.logbookData,
                
                init() {
                    // Set initial status message based on entry status
                    if (this.originalEntryStatus === 'completed') {
                        this.statusMessage = {
                            type: 'warning',
                            text: 'Your time entry for today is complete and pending coordinator approval.'
                        };
                    } else if (this.originalEntryStatus === 'approved') {
                        this.statusMessage = {
                            type: 'success',
                            text: 'Your time entry for today has been approved by the coordinator.'
                        };
                    } else if (this.originalEntryStatus === 'rejected') {
                        this.statusMessage = {
                            type: 'error',
                            text: 'Your time entry for today has been rejected. Please contact your coordinator.'
                        };
                    }
                },
                
                async timeIn() {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    
                    try {
                        const response = await fetch('{{ route('student.logbook.time-in') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.currentTimeIn = this.formatTime(data.time_in);
                            this.currentTimeOut = null;
                            this.totalHoursToday = 0;
                            this.isCompleted = false;
                        } else {
                            alert(data.message || 'Failed to record time in');
                        }
                    } catch (error) {
                        console.error('Error recording time in:', error);
                        alert('Error recording time in. Please try again.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                async timeOut() {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    
                    try {
                        const response = await fetch('{{ route('student.logbook.time-out') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Update current time out and status
                            this.currentTimeOut = this.formatTime(data.time_out);
                            this.totalHoursToday = parseFloat(data.hours_logged);
                            this.isCompleted = true;
                            
                            // Show success message
                            this.statusMessage = {
                                type: 'success',
                                text: data.message || 'Time out recorded successfully. Entry is now pending coordinator approval.'
                            };
                            
                            // Don't reset the display - keep it visible for the rest of the day
                            // The entry will be moved to previous entries automatically on the next day
                            
                        } else {
                            alert(data.message || 'Failed to record time out');
                        }
                    } catch (error) {
                        console.error('Error recording time out:', error);
                        alert('Error recording time out. Please try again.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                formatTime(timeString) {
                    const [hours, minutes] = timeString.split(':');
                    const hour24 = parseInt(hours, 10);
                    const hour12 = hour24 % 12 || 12;
                    const ampm = hour24 >= 12 ? 'PM' : 'AM';
                    return `${hour12}:${minutes} ${ampm}`;
                },
                
                calculateTotalHours() {
                    if (this.currentTimeIn && this.currentTimeOut) {
                        const timeInDate = new Date();
                        const timeOutDate = new Date();
                        
                        const [timeInHour, timeInMinute, timeInPeriod] = this.currentTimeIn.replace(/(\d+):(\d+)\s?(AM|PM)/, '$1,$2,$3').split(',');
                        const [timeOutHour, timeOutMinute, timeOutPeriod] = this.currentTimeOut.replace(/(\d+):(\d+)\s?(AM|PM)/, '$1,$2,$3').split(',');
                        
                        timeInDate.setHours(
                            timeInPeriod === 'PM' && timeInHour !== '12' ? parseInt(timeInHour) + 12 : 
                            timeInPeriod === 'AM' && timeInHour === '12' ? 0 : parseInt(timeInHour),
                            parseInt(timeInMinute), 0, 0
                        );
                        
                        timeOutDate.setHours(
                            timeOutPeriod === 'PM' && timeOutHour !== '12' ? parseInt(timeOutHour) + 12 : 
                            timeOutPeriod === 'AM' && timeOutHour === '12' ? 0 : parseInt(timeOutHour),
                            parseInt(timeOutMinute), 0, 0
                        );
                        
                        const diffMs = timeOutDate - timeInDate;
                        this.totalHoursToday = Math.max(0, diffMs / (1000 * 60 * 60));
                    }
                },
                
                get progressPercentage() {
                    return ((this.totalLoggedHours / this.requiredHours) * 100).toFixed(2);
                },
                
                get currentDate() {
                    return new Date().toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                }
            };
        }
    </script>
    
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
                <h2 class="text-lg font-semibold">DAILY LOGBOOK</h2>
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
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center border-2 border-white">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
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
                            <a href="{{ route('student.assigned-tasks') }}" class="flex items-center px-4 py-3 text-blue-100 rounded-lg transition-colors" style="color: #e0e7ff;" onmouseover="this.style.backgroundColor='#2a3866'; this.style.color='white';" onmouseout="this.style.backgroundColor=''; this.style.color='#e0e7ff';">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Assigned Tasks
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.logbook') }}" class="flex items-center px-4 py-3 text-white rounded-lg font-semibold" style="background-color: #2a3866;">
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
        <div class="flex-1 bg-blue-50 p-8" x-data="logbookComponent()">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Daily Logbook</h1>
                <p class="text-gray-600">Track your internship hours and daily activities</p>
            </div>

            <!-- Internship Hours Summary Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Internship Hours Summary</h2>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Hours -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600" x-text="`${totalLoggedHours}/${requiredHours}`">438/486</div>
                        <div class="text-sm text-gray-500">Total Hours</div>
                    </div>
                    <!-- Progress Percentage -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600" x-text="`${progressPercentage}%`">94.32%</div>
                        <div class="text-sm text-gray-500">Completion</div>
                    </div>
                    <!-- Remaining Hours -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600" x-text="`${requiredHours - totalLoggedHours}`">48</div>
                        <div class="text-sm text-gray-500">Hours Left</div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>Progress</span>
                        <span x-text="`${progressPercentage}%`">94.32%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300" :style="`width: ${progressPercentage}%`"></div>
                    </div>
                </div>
            </div>

            <!-- Today's Log Entry Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Today's Log Entry</h3>
                    <div class="text-sm text-gray-500" x-text="currentDate">{{ date('l, F j, Y') }}</div>
                </div>
                
                <!-- Status Message for Finalized Entries -->
                <div x-show="isEntryFinalized" class="mb-6 p-4 rounded-lg border" 
                     :class="{
                        'bg-yellow-50 border-yellow-200': originalEntryStatus === 'completed',
                        'bg-green-50 border-green-200': originalEntryStatus === 'approved',
                        'bg-red-50 border-red-200': originalEntryStatus === 'rejected'
                     }">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg x-show="originalEntryStatus === 'completed'" class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="originalEntryStatus === 'approved'" class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="originalEntryStatus === 'rejected'" class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" 
                               :class="{
                                  'text-yellow-800': originalEntryStatus === 'completed',
                                  'text-green-800': originalEntryStatus === 'approved',
                                  'text-red-800': originalEntryStatus === 'rejected'
                               }">
                                <span x-show="originalEntryStatus === 'completed'">Entry Completed - Pending Coordinator Review</span>
                                <span x-show="originalEntryStatus === 'approved'">Entry Approved by Coordinator</span>
                                <span x-show="originalEntryStatus === 'rejected'">Entry Rejected by Coordinator</span>
                            </p>
                            <p class="text-xs mt-1" 
                               :class="{
                                  'text-yellow-600': originalEntryStatus === 'completed',
                                  'text-green-600': originalEntryStatus === 'approved',
                                  'text-red-600': originalEntryStatus === 'rejected'
                               }">
                                <span x-show="originalEntryStatus === 'completed'">You cannot modify your time entry until it's reviewed.</span>
                                <span x-show="originalEntryStatus === 'approved'">Your time entry has been approved and counted toward your total hours.</span>
                                <span x-show="originalEntryStatus === 'rejected'">Please contact your coordinator for more information.</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-center">
                    <!-- Date -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 mb-1">Date</div>
                        <div class="text-lg font-semibold text-gray-800">{{ date('M j, Y') }}</div>
                    </div>
                    
                    <!-- Time In -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 mb-1">Time In</div>
                        <div class="text-lg font-semibold text-gray-800" x-text="currentTimeIn || '--:--'">--:--</div>
                        <button @click="timeIn()" :disabled="isLoading || currentTimeIn || isCompleted || isEntryFinalized" 
                                class="mt-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :title="isEntryFinalized ? 'Time entry already completed for today' : (currentTimeIn ? 'Already timed in' : 'Click to time in')">
                            <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Time Out -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 mb-1">Time Out</div>
                        <div class="text-lg font-semibold text-gray-800" x-text="currentTimeOut || '--:--'">--:--</div>
                        <button @click="timeOut()" :disabled="isLoading || !currentTimeIn || isCompleted || isEntryFinalized" 
                                class="mt-2 bg-red-500 hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :title="isEntryFinalized ? 'Time entry already completed for today' : (!currentTimeIn ? 'Time in first' : 'Click to time out')">
                            <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Total Hours Today -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 mb-1">Hours Today</div>
                        <div class="text-lg font-semibold text-blue-600" x-text="totalHoursToday.toFixed(2) + ' hrs'">0.00 hrs</div>
                    </div>
                    
                    <!-- Status -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 mb-1">Status</div>
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                             :class="currentTimeIn && !currentTimeOut ? 'bg-green-100 text-green-800' : 
                                     currentTimeIn && currentTimeOut ? 'bg-blue-100 text-blue-800' : 
                                     'bg-gray-100 text-gray-800'">
                            <span x-text="currentTimeIn && !currentTimeOut ? 'Logged In' : 
                                         currentTimeIn && currentTimeOut ? 'Completed' : 
                                         'Not Started'">Not Started</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Log Entries -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Previous Log Entries</h3>
                
                <!-- Dynamic entries from today's completed logs -->
                <div class="space-y-4" x-show="logEntries.length > 0">
                    <template x-for="entry in logEntries" :key="entry.date + entry.timeIn">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-center">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-1">Date</div>
                                    <div class="text-lg font-semibold text-gray-800" x-text="entry.date"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-1">Student</div>
                                    <div class="text-sm font-medium text-blue-600" x-text="entry.user_name || '{{ Auth::user()->name }}'"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-1">Time In</div>
                                    <div class="text-lg font-semibold text-gray-800" x-text="entry.timeIn"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-1">Time Out</div>
                                    <div class="text-lg font-semibold text-gray-800" x-text="entry.timeOut"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-1">Hours Logged</div>
                                    <div class="text-lg font-semibold text-blue-600" x-text="entry.hoursLogged + ' hrs'"></div>
                                </div>
                                <div class="text-center">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" x-text="entry.status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Empty state when no entries -->
                <div x-show="logEntries.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h4 class="text-lg font-medium text-gray-500 mb-2">No log entries yet</h4>
                    <p class="text-gray-400">Complete your first time-in and time-out to see entries here</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>