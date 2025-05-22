document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const userId = params.get("id");

    if (userId && userId !== "null") {
        getUserDetails(userId);
    } else {
        console.error("Invalid userId:", userId);
    }

    // user details
    fetch('userMetricsHandler.php?getUser=true')
        .then(response => response.json())
        .then(user => {
            document.getElementById('last-login').textContent = user.lastLogin;
            document.getElementById('tasks-completed').textContent = user.tasksCompleted;
            document.getElementById('workspaces-involved').textContent = user.workspacesInvolved;
            document.getElementById('projects-involved').textContent = user.projectsInvolved;
            document.getElementById('projects-completed').textContent = user.projectsCompleted;
            const activityLink = document.querySelector('.user-activity');
            activityLink.href = `userActivity.php?id=${encodeURIComponent(user.id)}`;
        })
        .catch(error => console.error('Error fetching user details:', error));

    // user activity
    fetch(`userMetricsHandler.php?getUserDetails=true&userId=${encodeURIComponent(userId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error from server:", data.error);
                return;
            }
            document.querySelector('.user-activity').href = `userActivity.php?id=${encodeURIComponent(userId)}`;
        })
        .catch(error => console.error("Error fetching user details:", error));

    // contact
    const contactButton = document.getElementById("contact-user-button");
    if (contactButton) {
        contactButton.addEventListener("click", contactUser);
    }
});

function getUserDetails(userId) {
    fetch(`userMetricsHandler.php?getUserDetails=true&userId=${encodeURIComponent(userId)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            let userContainer = document.querySelector(".user-container");
            if (!userContainer) {
                console.error("User container not found in DOM.");
                return;
            }
            if (data.error) {
                userContainer.innerHTML = `<p>${data.error}</p>`;
                return;
            }
            userContainer.innerHTML = "";
            if (Array.isArray(data.user) && data.user.length > 0) {
                data.user.forEach(user => {
                    let div = document.createElement("div");
                    div.classList.add("user");
                    div.innerHTML = `
                        <div class="profile-img">
                            <img src="${user.profile_img ? '../../user/profile/' + user.profile_img : '/Goalify/Img/default_profile.png'}" alt="User profile">
                        </div>
                        <div class="user-info">
                            <h2 class="name">${user.name}</h2>
                            <p class="email">Email: ${user.email}</p>
                            <p class="status">Account status: Active</p>
                        </div>
                    `;
                    userContainer.appendChild(div);
                });
            } else {
                userContainer.innerHTML = '<p>No user data found.</p>';
            }
            if (data.activity) {
                renderActivityData(data.activity);
            } else {
                console.error("No activity data found.");
            }
        })
        .catch(error => {
            console.error("Error fetching user details:", error);
        });
}

// user activity data section
function renderActivityData(activity) {
    if (!activity) {
        console.error("No activity data found.");
        return;
    }
    document.getElementById("last-login").textContent = activity.lastLogin || "N/A";
    document.getElementById("tasks-completed").textContent = activity.tasksCompleted || "0";
    document.getElementById("workspaces-involved").textContent = activity.workspacesInvolved || "0";
    document.getElementById("projects-involved").textContent = activity.projectsInvolved || "0";
    document.getElementById("projects-completed").textContent = activity.projectsCompleted || "0";
}

function contactUser() {
    const params = new URLSearchParams(window.location.search);
    const userId = params.get("id");

    fetch(`userMetricsHandler.php?getUserEmail=true&userId=${encodeURIComponent(userId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.email) {
                window.location.href = `mailto:${data.email}?subject=Message from Goalify Admin`;
            } else {
                alert("User email not found.");
            }
        })
        .catch(error => console.error("Error fetching user email:", error));
}