<!DOCTYPE html>
<html>
<head>
    <title>Report Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        .category { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
<div class="title">Dashboard Report</div>

<div class="section">
    <strong>Total Tasks:</strong> {{ $totalTasks }}<br>
    <strong>Completed (%):</strong> {{ $completionRate }}%<br>
    <strong>Overdue Tasks:</strong> {{ $overdue }}
</div>

<div class="section">
    <strong>Tasks by Category:</strong>
    <ul>
        @foreach ($distribution as $data)
            @php $category = $categories[$data->category_id] ?? null; @endphp
            @if ($category)
                <li>{{ $category->name }} - {{ $data->count }} task(s)</li>
            @endif
        @endforeach

    </ul>

    <strong>User Performance:</strong>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead>
        <tr>
            <th>Name</th>
            <th>Total Tasks</th>
            <th>On-Time Completion (%)</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($userStats['tasksPerUser'] as $entry)
            @php
                $user = $users[$entry->user_id] ?? null;
                $onTime = $userStats['onTimePerUser']->firstWhere('user_id', $entry->user_id);
                $rate = $onTime && $onTime->total > 0 ? round(($onTime->on_time / $onTime->total) * 100, 2) : 0;
            @endphp
            @if ($user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $entry->total }}</td>
                    <td>{{ $rate }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
