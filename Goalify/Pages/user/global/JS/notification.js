document.addEventListener("DOMContentLoaded", () => {
    const format = { day: 'numeric', month: 'short', year: 'numeric' };
    let formattedDate = new Date().toLocaleDateString('en-GB', format);

    // create warning for todo tasks/project tasks that are going to due
    function createDueWarning(item, type) {
        let notiContainer = document.querySelector("#noti-container");

        let taskWarning = document.createElement("div");
        taskWarning.classList.add("task-warning");

        let formatDaysLeft = item.daysLeft === 0 ? "today" : `${item.daysLeft} days`

        if (type == "task") {
            taskWarning.innerHTML = `
                <div class="due-task">
                    <span class="material-symbols-rounded due-date-warning">error</span>
                    <span class="title">Due date is approaching</span>
                    <span class="sent-date">${formattedDate}</span>
                </div>
                <div class="task-container">
                    <span class="desc">
                        ${item.name} is due in <span class="due-date">${formatDaysLeft}</span>
                    </span>
                </div>
            `;
        } else if (type == "project") {
            taskWarning.innerHTML = `
                <div class="due-task">
                    <span class="material-symbols-rounded due-date-warning">error</span>
                    <span class="title">Due date is approaching</span>
                    <span class="sent-date">${formattedDate}</span>
                </div>
                <div class="task-container">
                    <span class="desc">
                        <strong>${item.name}</strong>, part of <strong>${item.project}</strong> project in <strong>${item.workspace}</strong> workspace
                        is due in <span class="due-date">${formatDaysLeft}</span>
                    </span>
                </div>
            `;
        }

        notiContainer.appendChild(taskWarning);

        let dueDate = taskWarning.querySelector(".due-date");
        if (item.daysLeft === 0) {
            dueDate.style.color = "red";
        } else if (item.daysLeft === 1) {
            dueDate.style.color = "orange";
        } else if (item.daysLeft === 2) {
            dueDate.style.color = "#ffce1b";
        } else if (item.daysLeft === 3) {
            dueDate.style.color = "green";
        }
    }

    // fetch notification data
    function fetchNoti() {
        fetch('/Goalify/Pages/user/notification/notificationHandler.php')
            .then(response => response.json())
            .then(data => {
                let notiContainer = document.querySelector("#noti-container");
                notiContainer.innerHTML = "";

                if (data.task) {
                    data.task.forEach(task => {
                        createDueWarning({ name: task.task_name, daysLeft: task.daysLeft }, "task")
                    });
                }

                if (data.project) {
                    data.project.forEach(project => {
                        createDueWarning({
                            name: project.projectSubTaskName, daysLeft: project.daysLeft,
                            project: project.projectName, workspace: project.workspaceName
                        }, "project")
                    })
                }

                if (data.invitation) {
                    data.invitation.forEach(invitation => {
                        let workspaceInvatation = document.createElement("div");
                        workspaceInvatation.classList.add("invitation");

                        workspaceInvatation.innerHTML = `
                        <div class="inviter">
                            <span class="profile"><img src="${invitation.profile_img ? '/Goalify/Pages/user/profile/' + invitation.profile_img : '/Goalify/Img/default_profile.png'}" alt="user profile"></span>
                            <span class="userid">${invitation.name}</span>
                            <span class="invite-date">${invitation.sendDateTime}</span>
                        </div>
                        <div class="desc-container">
                            <span class="desc">has invited you to join workspace </span>
                            <span class="workspace-name">${invitation.workspaceName}</span>
                        </div>
                        <div class="response">
                            <button class="block" data-id="${invitation.invitationId}" workspace-id="${invitation.workspaceId}">Block</button>
                            <button class="accept" data-id="${invitation.invitationId}" workspace-id="${invitation.workspaceId}">Accept</button>
                        </div>
                    `;

                        notiContainer.appendChild(workspaceInvatation);
                    });
                }

                if (!data.task && !data.project && !data.invitation) {
                    let noNoti = document.createElement("div");
                    let todaysDate = document.createElement("div");
                    noNoti.id = "no-noti";
                    todaysDate.id = "todays-date";
                    noNoti.textContent = "- No noti is available here :D -";
                    todaysDate.textContent = `${formattedDate}`;
                    notiContainer.style.height = "150px";
                    notiContainer.appendChild(noNoti);
                    notiContainer.appendChild(todaysDate);
                    return;
                }


            })
            .catch(error => console.error("Error fetching tasks:", error));
    }

    // insert activity log
    function insertActivityLog(actionId, details) {
        let formData = new FormData();
        formData.append("actionId", actionId);
        formData.append("details", details);

        fetch("/Goalify/Pages/user/notification/notificationHandler.php?insertActivityLog=true", {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(data => console.log("Record Inserted:", data))
            .catch(error => console.error("Error:", error));
    }

    // toggle the display of notification panel
    const pointer = document.querySelector('#noti-pointer');
    const content = document.querySelector('#noti-container');
    const notiIcon = document.querySelector('.material-symbols-rounded.notifications');
    notiIcon.addEventListener('click', () => {
        content.classList.toggle("active");
        pointer.classList.toggle("active");
        fetchNoti();
    });

    // update invitation status 
    document.querySelector('#noti-container').addEventListener("click", function (event) {
        if (event.target.classList.contains("block") || event.target.classList.contains("accept")) {
            let invitationId = event.target.getAttribute("data-id");
            let workspaceId = event.target.getAttribute("workspace-id");
            let status;
            if (event.target.classList.contains("block")) {
                status = "reject";
                insertActivityLog("A018", `Rejected the invitation to workspace ${workspaceId}!`);

            } else {
                status = "accept";
                insertActivityLog("A017", `Accepted the invitation to workspace ${workspaceId}!`);
            }  

            let formData = new FormData();
            formData.append("invitationId", invitationId);
            formData.append("invitationStatus", status);

            fetch("/Goalify/Pages/user/notification/notificationHandler.php?updateInvitationStatus=true", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    console.log(`Invitation ${status}:`, data);
                    fetchNoti();
                })
                .catch(error => console.error("Error:", error));
        }
    });
});