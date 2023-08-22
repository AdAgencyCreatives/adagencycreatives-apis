@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function renderChart(id, labels, actual_data, chart_title, chart_subtitle) {

    const ctx = document.getElementById(id).getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: chart_title,
                borderColor: 'rgb(37, 151, 44)',
                data: actual_data,
                backgroundColor: [
                    'rgba(201, 203, 207, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgb(201, 203, 207)',
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    },
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: chart_subtitle
                    }
                }
            }
        }
    });
}

function populateWidgets(apiResponse) {
    for (let key in apiResponse) {
        if (apiResponse.hasOwnProperty(key)) {
            const element = document.getElementById(key);
            if (element) {
                element.textContent = apiResponse[key];
            }
        }
    }
}

function fetchData() {

    $.ajax({
        url: 'api/v1/stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            populateWidgets(response);
            console.log(response.chart);
            //Data for user chart
            renderChart('usersChart', response.users_chart.labels, response.users_chart.createdData,
                'New Users in last 7 days', 'Number of users');

            renderChart('jobsChart', response.jobs_chart.labels, response.jobs_chart.createdData,
                'New jobs in last 7 days', 'Number of jobs');


        },
        error: function() {
            alert('Failed to fetch stats from the API.');
        }
    });
}


$(document).ready(function() {
    fetchData();
});
</script>
@endsection
<h1 class="h3 mb-3">Dashboard</h1>

@include('pages.dashboard._inc.stats')

@endsection
