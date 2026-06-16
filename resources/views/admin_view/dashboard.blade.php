<!DOCTYPE html>
<html>
<head>
    <title>Game Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #0f172a;
            color: white;
            margin: 0;
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
        }

        h2 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #334155;
            text-align: left;
        }

        th {
            color: #93c5fd;
        }
    </style>
</head>

<body>

<h1>🎮 Game Admin Dashboard</h1>

<div class="grid">

    <!-- PIE CHART -->
    <div class="card">
        <h2>Game Modes Distribution</h2>
        <canvas id="pieChart"></canvas>
    </div>

    <!-- LINE CHART -->
    <div class="card">
        <h2>Weekly Active Players</h2>
        <canvas id="lineChart"></canvas>
    </div>

</div>

<!-- TABLE -->
<div class="card" style="margin-top:20px;">
    <h2>Recent Matches</h2>

    <table>
        <thead>
            <tr>
                <th>Player</th>
                <th>Mode</th>
                <th>Result</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentMatches as $match)
                <tr>
                    <td>{{ $match['player'] }}</td>
                    <td>{{ $match['mode'] }}</td>
                    <td>{{ $match['result'] }}</td>
                    <td>{{ $match['score'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    // PIE CHART
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: @json(array_keys($gameModes)),
            datasets: [{
                data: @json(array_values($gameModes)),
                backgroundColor: ['#ff6384','#36a2eb','#ffcd56','#4bc0c0']
            }]
        }
    });

    // LINE CHART
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Players',
                data: @json($weeklyActive),
                borderColor: '#36a2eb',
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>