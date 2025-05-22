document.addEventListener('DOMContentLoaded', () => {
    // get userId from url
    let params = new URLSearchParams(window.location.search);
    let userId = params.get("userId");

    // encode user id
    if (userId) {
        getUserDetails(encodeURIComponent(userId));
    }
});

// get user details based on selected user id
function getUserDetails(userId) {
    fetch(`productivityHandler.php?getUserDetails=true&userId=${userId}`)
        .then(response => response.json())
        .then(data => {

            let userContainer = document.querySelector(".user-container");

            if (data.error) {
                userContainer.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            // Clear the container before appending new data
            userContainer.innerHTML = "";
            if (data.user) {
                data.user.forEach(user => {
                    let div = document.createElement("div");
                    div.classList.add("user");
                    div.innerHTML = `
                        <div class="profile-img"><img src="${user.profile_img ? '../../user/profile/' + user.profile_img : '/Goalify/Img/default_profile.png'}" alt="User profile"></div>
                        <div class="user-info">
                            <p class="name">${user.name}</p>
                            <p class="email">Email: ${user.email}</p>
                            <p class="status">Account status: Active</p>
                        </div>
                    `;
                    userContainer.appendChild(div);
                });
            }

            let workspaceContainer = document.querySelector(".workspace");
            let workspaceTable = document.querySelector(".workspace-table");

            workspaceContainer.innerHTML = "";
            workspaceTable.innerHTML = `
                <tr>
                    <th>Workspace</th>
                    <th>Date Joined</th>
                    <th>Role in Workspace</th>
                </tr>
            `;

            if (data.workspace && data.workspace.length > 0) {
                workspaceContainer.innerHTML = `
                    <h1 class="workspace-title">Workspace Involved (${data.workspace.length})</h1>
                    `;

                data.workspace.forEach(workspace => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${workspace.workspaceName}</td>
                        <td>${workspace.dateTime}</td>
                        <td>${workspace.role}</td>
                    `;
                    workspaceTable.appendChild(row);
                });

                workspaceContainer.appendChild(workspaceTable);
            } else {
                workspaceContainer.style.height = "180px";
                workspaceContainer.style.marginBottom = "0";
                workspaceContainer.innerHTML = `
                    <h1 class="workspace-title">Workspace Involved (0)</h1>
                    <div id="no-workspace">- No workspace is available here :D -</div>
                `;
            }

            let projectContainer = document.querySelector(".projects");
            let projectTable = document.querySelector(".project-table");

            projectContainer.innerHTML = "";
            projectTable.innerHTML = `
                <tr>
                    <th>Workspace</th>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Date Started</th>
                    <th>Deadline</th>
                </tr>
            `;

            if (data.project && data.project.length > 0) {
                projectContainer.innerHTML = `
                    <h1 class="project-title">Project Involved (${data.project.length})</h1>
                    `;

                data.project.forEach(project => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${project.workspaceName ? project.workspaceName : '-'}</td>
                        <td>${project.projectName ? project.projectName : '-'}</td>
                        <td>${project.projectStatus ? project.projectStatus : '-'}</td>
                        <td>${project.projectStart ? project.projectStart : '-'}</td>
                        <td>${project.projectDeadline ? project.projectDeadline : '-'}</td>

                    `;
                    projectTable.appendChild(row);
                });

                projectContainer.appendChild(projectTable);
            } else {
                projectContainer.style.height = "180px";
                projectContainer.style.marginBottom = "0";
                projectContainer.innerHTML = `
                    <h1 class="project-title">Project Involved (0)</h1>
                    <div id="no-project">- No project is available here :D -</div>
                `;
            }

            let taskContainer = document.querySelector(".personal-task");
            let taskTable = document.querySelector(".task-table");

            taskContainer.innerHTML = "";
            taskTable.innerHTML = `
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Date Time Created</th>
                    <th>Completion Date</th>
                    <th>Priority</th>
                </tr>
            `;

            if (data.task && data.task.length > 0) {
                taskContainer.innerHTML = `
                    <h1 class="task-title">Personal Task (${data.task.length})</h1>
                    `;

                data.task.forEach(task => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${task.task_name}</td>
                        <td>${task.complete_status}</td>
                        <td>${task.created_at}</td>
                        <td>${task.completed_date ? task.completed_date : '-'}</td>
                        <td>${task.category}</td>

                    `;
                    taskTable.appendChild(row);
                });

                taskContainer.appendChild(taskTable);
            } else {
                taskContainer.style.height = "180px";
                taskContainer.style.marginBottom = "0";
                taskContainer.innerHTML = `
                    <h1 class="task-title">Personal Task (0)</h1>
                    <div id="no-task">- No personal task is available here :D -</div>
                `;
            }

        })
        .catch(error => console.error("Error fetching tasks:", error));
}
