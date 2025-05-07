<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.summary') }}"
                   class="flex items-center gap-1 text-gray-600 hover:text-gray-900 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm hidden sm:inline">Back</span>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Trend Analysis</h2>
            </div>
            <form method="GET" action="{{ route('reports.trend') }}">
                <select name="type" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                    <option value="daily" {{ $type == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $type == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </form>
        </div>

        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-100 text-gray-700 uppercase">
            <tr>
                <th class="px-4 py-2">Period</th>
                <th class="px-4 py-2">Completed Tasks</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $row)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $row->formatted_period }}</td>
                    <td class="px-4 py-2">{{ $row->count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <canvas id="trendChart" class="mt-8 w-full max-h-[400px]"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('trendChart').getContext('2d');

        const labels = @json($data->pluck('period'));
        const counts = @json($data->pluck('count'));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tasks Completed',
                    data: counts,
                    fill: false,
                    borderColor: '#3B82F6',
                    backgroundColor: '#3B82F6',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
