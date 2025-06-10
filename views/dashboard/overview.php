<?php require_once 'views/dashboard/organizer_layout.php'; ?>

<div class="dashboard-section">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <div class="stat-info">
                <h3><?= $stats['total_events'] ?></h3>
                <p>Événements créés</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-info">
                <h3><?= $stats['total_participants'] ?></h3>
                <p>Participants totaux</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-user-clock"></i>
            <div class="stat-info">
                <h3><?= $stats['pending_requests'] ?></h3>
                <p>Demandes en attente</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-alt"></i>
            <div class="stat-info">
                <h3><?= $stats['upcoming_events'] ?></h3>
                <p>Événements à venir</p>
            </div>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3>Statistiques des événements</h3>
            <div class="chart-wrapper">
                <canvas id="eventsChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Participation par mois</h3>
            <div class="chart-wrapper">
                <canvas id="participantsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuration du premier graphique (Événements)
const eventsCtx = document.getElementById('eventsChart').getContext('2d');
new Chart(eventsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Événements ouverts', 'Sous réserve', 'Fermés'],
        datasets: [{
            data: [<?= $stats['events_open'] ?>, <?= $stats['events_pending'] ?>, <?= $stats['events_closed'] ?>],
            backgroundColor: [
                '#4CAF50',
                '#FFA726',
                '#EF5350'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Configuration du deuxième graphique (Participants par mois)
const participantsCtx = document.getElementById('participantsChart').getContext('2d');
new Chart(participantsCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($stats['months']) ?>,
        datasets: [{
            label: 'Participants',
            data: <?= json_encode($stats['monthly_participants']) ?>,
            borderColor: '#4CAF50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

<style>
.charts-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.chart-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-card h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
}

.chart-wrapper {
    height: 300px;
    position: relative;
}

@media (max-width: 768px) {
    .charts-container {
        grid-template-columns: 1fr;
    }
}
</style> 