<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Voting Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body, html {
            height: 100%;
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 50px;
        }

        .card {
            margin-bottom: 30px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #2b3e50;
            color: #ffffff;
            padding: 20px;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }

        .card-body {
            padding: 30px;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .update-message {
            text-align: center;
            color: #28a745;
            margin-top: 10px;
        }

        .total-votes {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Voting Results Card -->
    <div class="card">
        <div class="card-header">
            Live Voting Results - Candidate Votes
        </div>
        <div class="card-body">
            <!-- Chart.js Canvas for Live Voting -->
            <div class="chart-container">
                <canvas id="liveVotingChart"></canvas>
            </div>

            <!-- Total Votes -->
            <div class="total-votes">
                Total Votes Cast: <span id="totalVotes">0</span>
            </div>

            <!-- Last Update Message -->
            <div class="update-message">
                Last updated: <span id="lastUpdated">0 seconds ago</span>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Chart.js Setup
    let liveVotingChart;
    let chartConfig = {
        type: 'bar',
        data: {
            labels: [],  // Candidate Names will be inserted here
            datasets: [{
                label: 'Votes',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                data: []  // Votes for each candidate will be inserted here
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true
        }
    };

    // Function to update the chart with new data
    function updateChart(data) {
        const labels = data.map(candidate => candidate.candidate_name);
        const votes = data.map(candidate => candidate.total_votes);

        liveVotingChart.data.labels = labels;
        liveVotingChart.data.datasets[0].data = votes;
        liveVotingChart.update();
    }

    // Function to fetch live voting data from the server
    function fetchLiveVotingData() {
        fetch('get_live_voting_data.php')  
            .then(response => response.json())
            .then(data => {
                updateChart(data);

                // Update the total votes
                const totalVotes = data.reduce((acc, candidate) => acc + parseInt(candidate.total_votes), 0);
                document.getElementById('totalVotes').textContent = totalVotes;
                const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const formattedTime = `${hours}:${minutes}`;

            // Update the last updated time with the formatted time
            document.getElementById('lastUpdated').textContent = `Last updated: ${formattedTime}`;
            });
    }

    // Initialize the chart on page load
    window.onload = function () {
        const ctx = document.getElementById('liveVotingChart').getContext('2d');
        liveVotingChart = new Chart(ctx, chartConfig);

        // Fetch live data every 10 seconds
        setInterval(fetchLiveVotingData, 10000);
    };
</script>
</body>
</html>
