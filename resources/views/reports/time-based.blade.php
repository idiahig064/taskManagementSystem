<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Reports Dashboard</h1>
            <div class="flex gap-4">
                <a href="{{ route('reports.summary') }}"
                   class="flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Summary
                </a>
                <a href="{{ route('reports.export', ['type' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Export PDF
                </a>
                <a href="{{ route('reports.export', ['type' => 'csv']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Filters</h2>
            <form method="GET" class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Users</option>
                        @foreach ($users as $id => $user)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select name="period" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                </div>

                <div class="md:col-span-4">
                    <button type="submit" class="bg-indigo-600 text-black px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Time-Based Reports -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Time-Based Reports</h2>

            @if ($dailyStats->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Daily</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <ul class="space-y-2">
                            @foreach ($dailyStats as $stat)
                                <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-gray-700">{{ $stat->date }}</span>
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $stat->count }} task(s) completed
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if ($weeklyStats->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Weekly</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <ul class="space-y-2">
                            @foreach ($weeklyStats as $stat)
                                <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-gray-700">Week {{ $stat->formatted_week }}</span>
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $stat->count }} task(s) completed
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if ($monthlyStats->isNotEmpty())
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Monthly</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <ul class="space-y-2">
                            @foreach ($monthlyStats as $stat)
                                <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-gray-700">{{ $stat->month }}</span>
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $stat->count }} task(s) completed
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- User Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">User Performance</h2>
                <div class="text-sm text-gray-500">{{ count($tasksPerUser) }} users</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks Completed</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On-Time Rate</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($tasksPerUser as $entry)
                        @php
                            $userData = $getUsersCompleted($entry);
                            $user = $userData['user'];
                            $onTimeRate = $userData['onTimeRate'];
                        @endphp
                        @if ($user)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 me-4">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($user->avatar_path) }}" alt="{{ $user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $entry->total }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $onTimeRate }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $onTimeRate }}%</span>
                                </div>
                            </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
