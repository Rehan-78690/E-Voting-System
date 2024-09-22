<?php
include 'config.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}
$election_check = "SELECT status FROM elections";
$result = mysqli_query($conn, $election_check);
if (mysqli_num_rows($result) > 0) {
    $row = $result->fetch_assoc();
    $election_status = $row['status'];
    if ($election_status === 'completed') {
        echo "Election is completed.See voting history??";
        $delay=2;
        header("refresh:$delay;url=welcome.php");
       
        exit();
    }
    if($election_status===('inactive')){
        echo"Elections have not started yet.Activate elections now";
        $delay=2;
        header("refresh:$delay;url=welcome.php");
       
        exit();
    }
    if($election_status===('upcoming')){
        echo"Elections are starting soon.Wait for a few days to see the live voting results";
        $delay=2;
        header("refresh:$delay;url=welcome.php");
        //view candidates page
       
        exit();
    }
    }
?>
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
            <!-- Chart.js Canvas for Bar Chart -->
            <div class="chart-container">
                <canvas id="liveVotingChart"></canvas>
            </div>

            <!-- Chart.js Canvas for Pie Chart -->
            <div class="chart-container mt-5">
                <canvas id="votePieChart"></canvas>
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
    let liveVotingChart;
    let votePieChart;
    let pieChartColors = [];  // Array to store the colors for the pie chart

    // Function to generate a shade based on votes (lighter shades for fewer votes)
    function getShade(baseColor, votePercentage) {
        const shadeIntensity = Math.floor(255 * (1 - votePercentage)); // Darker with higher votes
        return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${shadeIntensity / 255})`;
    }

    // Predefined base colors for each candidate (RGB)
    const baseColors = [
        'rgba(255, 99, 132, 0.8)',   // Red
        'rgba(54, 162, 235, 0.8)',   // Blue
        'rgba(255, 206, 86, 0.8)',   // Yellow
        'rgba(75, 192, 192, 0.8)',   // Green
        'rgba(153, 102, 255, 0.8)',  // Purple
        'rgba(255, 159, 64, 0.8)'    // Orange
    ];

    // Function to update the pie chart colors every minute
    function updatePieChartColors() {
        pieChartColors = votePieChart.data.labels.map(() => `#${Math.floor(Math.random() * 16777215).toString(16)}`);
        votePieChart.data.datasets[0].backgroundColor = pieChartColors;
        votePieChart.update();
    }

    // Chart.js Configuration for the Bar Chart
    const barChartConfig = {
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
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };

    // Chart.js Configuration for the Pie Chart
    const pieChartConfig = {
        type: 'pie',
        data: {
            labels: [], // Candidate names as labels
            datasets: [{
                label: 'Votes',
                data: [], // Vote counts
                backgroundColor: baseColors, // Start with base colors
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10  // Enlarge on hover
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw || '0';
                            return label + ' votes';
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                intersect: true
            }
        }
    };

    // Function to update both charts with new data and gradients based on votes
    function updateCharts(data) {
        const labels = data.map(candidate => candidate.candidate_name);
        const votes = data.map(candidate => candidate.total_votes);
        const totalVotes = votes.reduce((a, b) => a + b, 0);

        // Update Bar Chart
        liveVotingChart.data.labels = labels;
        liveVotingChart.data.datasets[0].data = votes;
        liveVotingChart.update();

        // Update Pie Chart Data
        votePieChart.data.labels = labels;
        votePieChart.data.datasets[0].data = votes;
        votePieChart.update();
    }

    // Function to fetch live voting data from the server
    function fetchLiveVotingData() {
        fetch('get_live_voting_data.php')
            .then(response => response.json())
            .then(data => {
                updateCharts(data);

                // Update the total votes
                const totalVotes = data.reduce((acc, candidate) => acc + parseInt(candidate.total_votes), 0);
                document.getElementById('totalVotes').textContent = totalVotes;

                // Update the last updated time
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const formattedTime = `${hours}:${minutes}`;
                document.getElementById('lastUpdated').textContent = `Last updated: ${formattedTime}`;
            });
    }

    // Initialize both charts on page load
    window.onload = function () {
        const barCtx = document.getElementById('liveVotingChart').getContext('2d');
        const pieCtx = document.getElementById('votePieChart').getContext('2d');

        liveVotingChart = new Chart(barCtx, barChartConfig);
        votePieChart = new Chart(pieCtx, pieChartConfig);

        // Fetch live data every 10 seconds
        setInterval(fetchLiveVotingData, 10000);

        // Update pie chart colors every minute (60000 milliseconds)
        setInterval(updatePieChartColors, 60000);

        // Initial fetch to populate the charts
        fetchLiveVotingData();
    };
</script>

</body>
</html>
