/*
Author: Phang Shea Wen
Date: 2025/02/03
Filename: table.js
Description: To handle estimate, assigned date, and due date overlay
*/
document.addEventListener('ProjectTaskLoaded', async () => {
    const response = await fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/loadProjectTask.php');
    const data = await response.json();
    console.log("Data from loadProjectTask.php:", data);

    const projectId = data.selectedProjectId;
    const projectName = data.projectName;
    const allProjectTasks = data.allProjectTasks;
    const allMembers = data.ownerMembers;

    const body = document.body;

    // Add New Table
    const addTable = document.querySelector('div#add-project-task-button');
    addTable.addEventListener('click', () => {
        if (document.querySelector('form#new-task')) {
            window.removeOverlay('new-task');
        }
        window.createOverlay(
            'new-task',
            `
            <label for="new-project-task-name">New Project Task</label>
            <input type="text" name="new-project-task-name" id="new-project-task-name" autofocus placeholder="task_01" autocomplete="off">
            <div class="button">
                <button type="button" id="close-new-task">Cancel</button>
                <button type="submit" id="confirm-new-task">Create</button>
            </div>
            `
        )

        const newTableForm = document.querySelector('form#new-task');
        newTableForm.addEventListener('submit', (event) => {
            event.preventDefault(); // prevent form submission
            const projectTaskForm = new FormData(newTableForm);
            projectTaskForm.append('project-id', projectId);
            console.log("ttets");
            // console.log(projectId);
            fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/taskUpdate.php', {
                method: 'POST',
                body: projectTaskForm
            })
            .then(response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    // const projectTaskName = response.projectTaskName;
                    // const projectTaskAddedDateTime = response.projectTaskAddedDateTime;
                    alert(successMsg);
                    document.dispatchEvent(new Event('AddNewProjectTaskTable'));
                    window.removeOverlay('new-task');
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                    alert(failMsg);
                }
            })
        })

        const closeNewSubTaskForm = document.querySelector('#close-new-task');
        closeNewSubTaskForm.addEventListener('click', () => {
            window.removeOverlay('new-task');
        })
    })

    // Add New Table Row
    const addTableRowButtons = document.querySelectorAll('button.add-table-row');
    addTableRowButtons.forEach(button => {
        button.addEventListener('click', () => {
            const taskDiv = button.closest('div.task');
            const projectTaskId = taskDiv.getAttribute('data-project-task-id');
            const deadlineForm = new FormData();
            deadlineForm.append('projectDetails', 'projectDeadline');
            fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/taskUpdate.php', {
                method: 'POST',
                body: deadlineForm
            })
            .then(response => response.json())
            .then(response => {
                successMsg = response.success;
                if (successMsg) {
                    const projectDeadline = response.projectDeadline;
                    console.log(projectDeadline);
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
                    // isoDate = "2025-03-12T15:30:45.123Z"
                    const minDateTime = now.toISOString().slice(0, 16); // YYYY-MM-DD HH:MM
                    var maxDateTime;
                    if (projectDeadline) {
                        const ddl = new Date(projectDeadline);
                        maxDateTime = ddl.toISOString().slice(0, 16);
                    } else {
                        maxDateTime = '';
                    }
                    
                    window.createOverlay(
                        'new-sub-task',
                        `
                        <label>New Sub Task</label>
                        <input type="hidden" name="project-task-id" value=${projectId}>
                        <table>
                            <tr>
                                <th>Title</th>
                                <td>
                                    <input type="text" name="sub-task-name" id="sub-task-name" autofocus placeholder="task_01" autocomplete="off" required>
                                </td>
                            </tr>
                            <tr>
                                <th>Member In-Charge</th>
                                <td>
                                    <select id="assigned-members" name="assigned-members">
                                        <option value="">Select member</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td>
                                    <select id="priority" name="priority">
                                        <option value="" selected disabled>None</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Estimate</th>
                                <td>
                                    <div class="estimate-hour">
                                        <input type="text" name="add-estimation" id="add-estimation" autofocus placeholder="00" autocomplete="off">
                                        <p>hour(s)</p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Assigned Date</th>
                                <td>
                                    <input id="assigned-date" type="datetime-local" name="assigned-date" onclick="this.showPicker()" min=${minDateTime} max=${maxDateTime}>
                                </td>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <td>
                                    <input id="due-date" type="datetime-local" name="due-date" onclick="this.showPicker()" min="${minDateTime}" max="${maxDateTime}">
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <select id="status" name="status">
                                        <option value="pending" selected disabled>Pending</option>
                                        <option value="in progress">In progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="button">
                            <button type="button" id="close-new-sub-task">Cancel</button>
                            <button type="submit" id="confirm-new-sub-task">Confirm</button>
                        </div>
                        `
                    )

                    const overlayMemberSelect = document.querySelector('select#assigned-members');

                    if (Array.isArray(allMembers)) {
                        for (const member of allMembers) {
                            memberOption = document.createElement('option');
                            memberOption.value = member['userId'];
                            memberOption.textContent = member['name'];

                            overlayMemberSelect.appendChild(memberOption);
                        }
                    }

                    const startDate = document.getElementById('assigned-date');
                    const endDate = document.getElementById('due-date');
            
                    // When start-date is picked, update end-date's min value
                    startDate.addEventListener('change', () => {
                        endDate.min = startDate.value;
                        });
            
                    // Optional: When end-date is picked, you can also restrict max of start-date
                    endDate.addEventListener('change', () => {
                        startDate.max = endDate.value;
                        });

                    const newTableRowForm = document.querySelector('form#new-sub-task');
                    newTableRowForm.addEventListener('submit', (event) => {
                        event.preventDefault(); // prevent form submission
                        const projectSubTaskForm = new FormData(newTableRowForm);
                        projectSubTaskForm.append('project-task-id', projectTaskId);
                        fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/taskUpdate.php', {
                            method: 'POST',
                            body: projectSubTaskForm
                        })
                        .then(response => response.json())
                        .then(response => {
                            const successMsg = response.success;
                            if (successMsg) {
                                // const projectTaskName = response.projectSubTaskName;
                                alert(successMsg);
                                document.dispatchEvent(new Event('AddNewSubProjectTaskTable'));
                                window.removeOverlay('new-sub-task');
                            } else {
                                const failMsg = response.fail;
                                console.log(failMsg);
                                alert(failMsg);
                            }
                        })
                    })

                    const closeNewSubTaskForm = document.querySelector('#close-new-sub-task');
                    closeNewSubTaskForm.addEventListener('click', () => {
                        window.removeOverlay('new-sub-task');
                    })
                } else {
                    failMsg = response.fail;
                    console.log(failMsg);
                }
            })
            
        })
    })

    // Edit estimation hour
    const estimations = document.querySelectorAll('td.estimate');

    estimations.forEach(estimation => {
        estimation.addEventListener('click', () => {
            window.createOverlay(
                'estimation-form',
                `
                <label for="sub-task-estimation">Estimated Time Taken</label>
                <div class="estimate-hour">
                    <input type="text" name="sub-task-estimation" id="sub-task-estimation" autofocus placeholder="00" autocomplete="off">
                    <p>hour(s)</p>
                </div>
                <div class="button">
                    <button type="button" id="close-estimation-form">Cancel</button>
                    <button type="submit" id="confirm-estimation-form">Confirm</button>
                </div>
                `
            );
            
            const estimationForm = document.querySelector('form#estimation-form');
            estimationForm.querySelector('input').focus();
            const tableRow = estimation.closest('table tr');
            const projectSubTaskId = tableRow.getAttribute('data-project-sub-task-id');

            estimationForm.addEventListener('submit', (event) => {
                event.preventDefault() // prevent form submission
                const estimationFormData = new FormData(estimationForm);
                estimationFormData.append('project-sub-task-id', projectSubTaskId);
                fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/taskUpdate.php', {
                    method: 'POST',
                    body: estimationFormData
                })
                .then(response => response.json())
                .then(response => {
                    const successMsg = response.success;
                    if (successMsg) {
                        console.log(successMsg);
                        alert(successMsg);
                        window.removeOverlay('estimation-form');
                        estimation.textContent = `${response.estimate} hours`;
                    } else {
                        const failMsg = response.fail;
                        console.log(failMsg);
                        alert(failMsg);
                    }
                })
            })

            const closeEstimationForm = document.querySelector('#close-estimation-form');
            closeEstimationForm.addEventListener('click', () => {
                window.removeOverlay('estimation-form');
            })
        })
    })
})