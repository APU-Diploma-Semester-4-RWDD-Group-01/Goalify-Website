document.addEventListener('DOMContentLoaded', () => {

    const workspaceFilter = document.querySelector("#workspace-filter");
    if (workspaceFilter.options.length > 0) {
        workspaceFilter.selectedIndex = 0;
        fetchWorkspaceData();
    }

    function fetchWorkspaceData() {
        let selectedValue = workspaceFilter.value;
        let workspaceCanvas = document.querySelector("#workspace").getContext('2d');
        let bgc = 'rgba(54, 162, 235, 0.6)';
        let borderColour = 'rgba(54, 162, 235, 1)';
        let unit = "";
        if (selectedValue == 'week') {
            unit = "day";
        } else if (selectedValue == 'month') {
            unit = "week"
        } else if (selectedValue == 'year') {
            unit = "month"
        }
        let label = `total workspace per ${unit}`

        fetch("dashboardHandler.php?workspace=" + selectedValue)
            .then(response => response.json())
            .then(data => {
                let labels = [], values = [];
                for (let item of data) {
                    labels.push(item.date || item.week || item.month);
                    values.push(item.totalWorkspace);
                }
                displayChart(labels, values, workspaceCanvas, bgc, borderColour, label);
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    workspaceFilter.addEventListener("change", () => {
        fetchWorkspaceData();
    })


    const projectFilter = document.querySelector("#project-filter");
    if (projectFilter.options.length > 0) {
        projectFilter.selectedIndex = 0;
        fetchProjectData();
    }

    function fetchProjectData() {
        let selectedValue = projectFilter.value;
        let projectCanvas = document.querySelector("#project").getContext('2d');
        let bgc = 'rgba(255, 99, 132, 0.6)';
        let borderColour = 'rgba(255, 99, 132, 1)';
        let unit = "";
        if (selectedValue == 'week') {
            unit = "day";
        } else if (selectedValue == 'month') {
            unit = "week"
        } else if (selectedValue == 'year') {
            unit = "month"
        }
        let label = `total project per ${unit}`;

        fetch("dashboardHandler.php?project=" + selectedValue)
            .then(response => response.json())
            .then(data => {
                let labels = [], values = [];
                for (let item of data) {
                    labels.push(item.date || item.week || item.month);
                    values.push(item.totalProject);
                }

                console.log(labels);
                console.log(values);


                displayChart(labels, values, projectCanvas, bgc, borderColour, label);
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    projectFilter.addEventListener("change", () => {
        fetchProjectData();
    })

    let charts = {};
    function displayChart(labels, values, chart, bgc, borderColor, label) {
        let chartId = chart.canvas.id;

        if(charts[chartId]) {
            charts[chartId].destroy();
        }

        charts[chartId] = new Chart(chart, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: bgc,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
    }

    // for user summary
    const totalUsersCount = document.querySelector("#total-users-count");
    let users = [];
    fetch('dashboardHandler.php?getUser=true')
        .then(response => response.json())
        .then(data => {
            users = data;
            if (totalUsersCount) {
                totalUsersCount.textContent = users.length;
            }
        }).catch(error => {
            console.error('Error loading total registered user:', error);
        });
    fetch('dashboardHandler.php?getMetrics=true')
        .then(response => response.json())
        .then(data => {
            // bar chart (sign ups per month)
            const signupsLabels = data.months.length ? data.months : ['No Data'];
            const signupsData = data.signups.length ? data.signups : [0];
            const signupsCtx = document.getElementById('signupsChart').getContext('2d');
            new Chart(signupsCtx, {
                type: 'bar',
                data: {
                    labels: signupsLabels,
                    datasets: [{
                        label: 'Monthly Sign-ups',
                        data: signupsData,
                        backgroundColor: 'rgba(251, 173, 63, 0.6)',
                        borderColor: 'rgb(251, 173, 63)',
                        borderWidth: 1.8
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
            // line chart (axtive users per week)
            const weeksLabels = data.weeks.map(week => week.replace("/", "-"));
            const activeUsersData = data.activeUsers.length ? data.activeUsers : [0];
            const activeUsersCtx = document.getElementById('activeUsersChart').getContext('2d');
            new Chart(activeUsersCtx, {
                type: 'line',
                data: {
                    labels: weeksLabels,
                    datasets: [{
                        label: 'Weekly Active Users',
                        data: activeUsersData,
                        borderColor: 'rgb(99, 255, 167)',
                        backgroundColor: 'rgba(99, 255, 167, 0.2)',
                        borderWidth: 1.8,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        })
});
