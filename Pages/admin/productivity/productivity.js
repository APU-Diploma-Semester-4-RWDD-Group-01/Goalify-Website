document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ JS Loaded");
    const form = document.querySelector('#search-users-bar');
    const input = document.querySelector('#search-users-input');

    const userList = document.querySelector('.user-list');
    const registeredUser = document.querySelector("#registered-title");
    const moreUsersLink = document.querySelector('.more-users');
    const searchInput = document.querySelector('#search-users-input');
    const searchForm = document.querySelector('#search-users-bar');

    let showAll = false;
    let users = [];

    // display registered user list
    const displayUsers = (usersToShow) => {
        userList.innerHTML = '';
        if (usersToShow.length > 0) {
            usersToShow.forEach(user => {
                let userDiv = document.createElement('div');
                userDiv.classList.add('user');
                userDiv.innerHTML = `
                    <div class="profile-img"><img src="${user.profile_img ? '../../user/profile/' + user.profile_img : '/Goalify/Img/default_profile.png'}" alt="User profile"></div>
                    <span class="name">${user.name}</span>
                    <button class="view-details" data-id="${user.userId}">View Details</button>
                `;
                userList.appendChild(userDiv);
            });

            document.querySelectorAll(".view-details").forEach(button => {
                button.addEventListener("click", function () {
                    let userId = this.getAttribute("data-id");
                    window.location.href = `productivity_details.php?userId=${encodeURIComponent(userId)}`;
                });
            });
        } else {
            userList.innerHTML = '<p>No users found.</p>';
        }
    };

    fetch('productivityHandler.php?getUser=true')
        .then(response => response.json())
        .then(data => {
            users = data;
            registeredUser.innerHTML = `Registered Users (${users.length})`;
            displayUsers(users.slice(0, 3));
            // click more/less
            moreUsersLink.addEventListener('click', (e) => {
                e.preventDefault();
                showAll = !showAll;
                displayUsers(showAll ? users : users.slice(0, 3));
                moreUsersLink.textContent = showAll ? 'Less' : 'More';
            });
            // search
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    fetch('productivityHandler.php', {
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

    // get workspace overview
    let workspaceData = document.querySelector(".workspace-data");
    let workspaceGrowthRate = document.querySelector(".workspace-growth-rate");

    fetch('productivityHandler.php?getWorkspaceOverview=true')
        .then(response => response.json())
        .then(data => {
            let currentMonth = data.currentMonth || 0;
            let lastMonth = data.lastMonth || 0;

            workspaceData.innerHTML = `${data.totalWorkspace ? data.totalWorkspace : 0}`;

            if (lastMonth === 0) {
                workspaceGrowthRate.innerHTML = `New workspaces started this month!`;
            } else if(lastMonth === 0 && currentMonth === 0) {
                workspaceGrowthRate.innerHTML = `- No Data -`;
            } else {
                let growth = ((currentMonth - lastMonth) / lastMonth) * 100;
                workspaceGrowthRate.innerHTML = `+${growth}% from last month`;
            }
        })
        .catch(error => console.error("Error fetching tasks:", error));

    // get project overview
    let projectData = document.querySelector(".project-data");
    let projectGrowthRate = document.querySelector(".project-growth-rate");

    fetch('productivityHandler.php?getProjectOverview=true')
        .then(response => response.json())
        .then(data => {
            let currentMonth = data.currentMonth || 0;
            let lastMonth = data.lastMonth || 0;

            projectData.innerHTML = `${data.totalProject ? data.totalProject : 0}`;

            if (lastMonth === 0 && currentMonth != 0) {
                projectGrowthRate.innerHTML = `New projects started this month!`;
            } else if(lastMonth === 0 && currentMonth === 0) {
                projectGrowthRate.innerHTML = `- No Data -`;
            } else {
                let growth = ((currentMonth - lastMonth) / lastMonth) * 100;
                projectGrowthRate.innerHTML = `+${growth}% from last month`;
            }
        })
        .catch(error => console.error("Error fetching tasks:", error));
})