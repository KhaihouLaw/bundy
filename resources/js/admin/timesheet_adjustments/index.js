$(() => {
    const totalRequests = $('.chart-data').attr('data-total-requests');
    const statusCounts = JSON.parse($('.chart-data').attr('data-status-counts'));
    const ctx = document.getElementById('timesheet-chart').getContext('2d');

    const data = {
        labels: [
            'Pending',
            'Approved',
            'Rejected',
            'Cancelled',
        ],
        datasets: [{
            data: [
                statusCounts.pending,
                statusCounts.approved,
                statusCounts.rejected,
                statusCounts.cancelled
            ],
            backgroundColor: [
                'rgb(255, 205, 86)',
                '#7de57d',
                'rgb(255, 99, 132)',
                '#c5c5c5',
            ],
            hoverOffset: 4,
        }]
    };

    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true, 
                    text: 'Total Adjustment Requests: ' + totalRequests
                },
                labels: {
                    render: (args) => {
                        return `${args.value} ${args.label} (${args.percentage}%)`;
                    },
                    fontSize: 15,
                }
            },
        }
    };

    const timesheet = new Chart(
        ctx,
        config
    );
});