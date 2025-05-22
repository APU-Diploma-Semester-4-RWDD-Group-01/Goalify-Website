document.addEventListener('DOMContentLoaded', () => {
    const chartContainer = document.getElementById("chart-container");
    if (chartContainer && chartContainer.style.maxWidth) {
        chartContainer.style.removeProperty("max-width");
    }

    const userList = document.querySelector('.registered-users-list');
    const totalUsersCount = document.querySelector("#total-users-count");
    const moreUsersLink = document.querySelector('.more-users');
    const searchInput = document.querySelector('#search-users-input');
    const searchForm = document.querySelector('#search-users-bar');
    const registeredUser = document.querySelector("#registered-title");

    let showAll = false;
    let users = [];

    fetch('userMetricsHandler.php?getUser=true')
        .then(response => response.json())
        .then(data => {
            users = data;
            registeredUser.innerHTML = `Registered Users (${users.length})`;
            if (totalUsersCount) {
                totalUsersCount.textContent = users.length;
            }            
            const displayUsers = (usersToShow) => {
                userList.innerHTML = '';
                if (usersToShow.length > 0) {
                    usersToShow.forEach(user => {
                        let userDiv = document.createElement('div');
                        userDiv.classList.add('user-item');
                        let userContent = `
                            <div class="profile-img"><img src="${user.profile_img ? '../../user/profile/' + user.profile_img : '/Goalify/Img/default_profile.png'}" alt="User profile"></div>
                            <span class="name">${user.name}</span>
                            <a href="userDetails.php?id=${encodeURIComponent(user.id)}" class="view-details">View Details</a>
                        `;
                        userDiv.innerHTML = userContent;
                        userList.appendChild(userDiv);
                    });
                } else {
                    userList.innerHTML = '<p>No users found.</p>';
                }
            };
            displayUsers(users.slice(0, 3));
            // click more/less
            moreUsersLink.addEventListener('click', (e) => {
                e.preventDefault();
                showAll = !showAll;
                if (showAll) {
                    displayUsers(users);
                    moreUsersLink.textContent = 'Less';
                } else {
                    displayUsers(users.slice(0, 3));
                    moreUsersLink.textContent = 'More';
                }
            });
            // search
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    fetch('userMetricsHandler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `search-users-input=${encodeURIComponent(searchTerm)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        displayUsers(data);
                        searchInput.value = "";
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
                } else {
                    displayUsers(users.slice(0, 3));
                }
            });
        })
        .catch(error => {
            console.error('Error loading user list:', error);
            registeredUser.innerHTML = 'Registered Users (0)';
            userList.innerHTML = '<p>Error loading users.</p>';
        });

    fetch('userMetricsHandler.php?getMetrics=true')
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
            // line chart (active users per week)
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
