<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Report</h1>
            <div class="flex gap-4">
                <a href="{{ route('reports.export', ['type' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Export PDF
                </a>
                <a href="{{ route('reports.export', ['type' => 'csv']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Export CSV
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Total Tasks</p>
                <p class="text-4xl font-bold text-gray-800">{{ $totalTasks }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Completed (%)</p>
                <p class="text-4xl font-bold text-gray-800">{{ $completionRate }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Overdue Tasks</p>
                <p class="text-4xl font-bold text-gray-800">{{ $overdue }}</p>
            </div>
        </div>

        <div class="mt-10 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Tasks by Category</h2>

            <select id="category-select" class="w-full p-2 border rounded-lg mb-4">
                <option value="">-- Select a category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <div id="task-count-result" class="text-gray-700 mt-2 hidden">
                This category has <strong id="task-count">0</strong> task(s).
            </div>
        </div>

        <!-- Cards type button -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <a href="{{ route('reports.time-based') }}" class="bg-white border rounded-lg p-6 shadow hover:shadow-lg transition group">
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-10 h-10 text-indigo-600 group-hover:text-indigo-800 transition"
                         viewBox="0 0 24 24" fill="none">
                        <path d="M3 9H21M7 3V5M17 3V5M6 13H8M6 17H8M11 13H13M11 17H13M16 13H18M16 17H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z"
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <div>
                        <p class="text-gray-600">Detailed View</p>
                        <p class="text-lg font-semibold text-gray-800">Time-Based Report</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.trend') }}" class="bg-white border rounded-lg p-6 shadow hover:shadow-lg transition group">
                <div class="flex items-center gap-4">
                    <svg class="w-10 h-10 text-teal-600 group-hover:text-teal-800 transition" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 17l6-6 4 4 8-8M21 21H3"/>
                    </svg>
                    <div>
                        <p class="text-gray-600">Trends & Progress</p>
                        <p class="text-lg font-semibold text-gray-800">Trend Analysis</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.querySelector('#category-select');
            const result = document.querySelector('#task-count-result');
            const countSpan = document.querySelector('#task-count');

            select.addEventListener('change', () => {
                const categoryId = select.value;
                if (!categoryId) {
                    result.classList.add('hidden');
                    return;
                }

                fetch(`/reports/category-tasks/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        countSpan.textContent = data.count;
                        result.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching task count:', error);
                    });
            });
        });
    </script>
</x-app-layout>
