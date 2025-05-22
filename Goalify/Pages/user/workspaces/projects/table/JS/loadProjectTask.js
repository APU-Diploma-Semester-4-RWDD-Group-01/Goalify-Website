document.addEventListener('DOMContentLoaded', loadTable());

document.addEventListener('AddNewProjectTaskTable', async () => {
    const taskList = document.querySelector('div.task-list');
    taskList.replaceChildren();
    await loadTable();
})

document.addEventListener('AddNewSubProjectTaskTable', async () => {
    const taskList = document.querySelector('div.task-list');
    taskList.replaceChildren();
    await loadTable();

})

async function loadTable() {
    const response = await fetch('/Goalify/Pages/user/workspaces/projects/table/PHP/loadProjectTask.php');
    const data = await response.json();

    const projectId = data.selectedProjectId;
    const projectName = data.projectName;
    const allProjectTasks = data.allProjectTasks;
    const allMembers = data.ownerMembers;
    const projectDeadline = data.projectDeadline;

    // Load Project Name
    const projectNameHeading = document.querySelector('.content .task-heading h1');
    projectNameHeading.textContent = projectName;
    projectNameHeading.setAttribute('data-project-id', projectId);

    // List project task and project sub task
    const taskList = document.querySelector('div.task-list');
    for (const task of allProjectTasks) {
        // Create task div
        const taskDiv = document.createElement('div');
        taskDiv.classList.add('task');
        taskDiv.setAttribute('data-project-task-id', task['projectTaskId']);


        // Child of task div 01: 'task-name-datetime-edit'
        const taskNameDateTimeDiv = document.createElement('div');
        taskNameDateTimeDiv.classList.add('task-name-datetime-edit');
            // get task created date and time
        const createdDateTime = new Date(task['createdDateTime']); // Convert timestamp to Date object
        const formattedDate = createdDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                                });
        const formattedTime = createdDateTime.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true  // Use 24-hour time
                                }).toUpperCase();
            // load 'task-name-datetime-edit' div html elements
        taskNameDateTimeDiv.innerHTML = `
        <div>
            <form class="project-task-name-form">
                <input name="project-task-name" class="project-task-name" type='text' readonly autocomplete="off" value="${task['projectTaskName']}">
                <button type="submit" class="submit-project-task-name-button" style="display: none;"><span class="material-symbols-rounded expand">check</span></button>
            </form>
            <p class="task-created-datetime">Added on ${formattedDate}, at ${formattedTime}</p>
        </div>
        <div class="edit">
            <button class="material-symbols-rounded edit-task">edit</span>
            <button class="material-symbols-rounded delete-task">delete</span>
        </div>
        `
        const inputName = taskNameDateTimeDiv.querySelector('form.project-task-name-form input');
        inputName.style.width = (inputName.value.length + 0.5) + 'ch';

        // Child of task div 01: 'task-name-datetime-edit'
        const table = document.createElement('table');
        // create header row
        const headerRow = document.createElement('thead');
        const headers =  ['Sub-Tasks', 'Member In-Charge', 'Priority', 'Estimate', 'Assigned Date', 'Due Date', 'Status', ''];
        var colNum = 1;
        for (const value of headers) {
            const header = document.createElement('th');
            if (colNum !== 8) {
                header.textContent = `${value}`;
                header.classList.add(`col-${colNum}`);
                headerRow.appendChild(header);
                colNum++;
            } else {
                header.classList.add('col-8');
                headerRow.appendChild(header);
            }
        }
        table.appendChild(headerRow);

        for (const subTask of task['sub-tasks']) {
            // create row
            const row = document.createElement('tr');
            row.setAttribute('data-project-sub-task-id', subTask['projectSubTaskId']);
            const dataValues = [subTask['projectSubTaskName'], subTask['assignedMemberName'], subTask['projectSubTaskPriority'], subTask['projectSubTaskEstimate'], subTask['projectSubTaskAssignedDate'], subTask['projectSubTaskDueDate'], subTask['projectSubTaskStatus']];
            var i = 0;
            const now = new Date();
            const ddl = new Date(projectDeadline);
            console.log(now);
            console.log(ddl);
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
            // isoDate = "2025-03-12T15:30:45.123Z"
            const minDateTime = now.toISOString().slice(0, 16); // YYYY-MM-DD HH:MM
            const maxDateTime = ddl.toISOString().slice(0, 16);
            for (const data of dataValues) {
                const dataCell = document.createElement('td');
                if (i == 0) {
                    dataCell.innerHTML = `
                    <form action="" method="POST" class="sub-task-name">
                        <input type="text" name="project-sub-task-name" readonly autocomplete="off">
                        <button type="button" onclick="toggleEditButton(this, 'open-input')">Edit</button>
                        <button type="submit" style="display: none;" onclick="toggleEditButton(this, 'close-input');">Done</button>
                    </form>
                    `;
                    dataCell.classList.add('col-1');
                    dataCell.classList.add('s-col-1');
                    dataCell.setAttribute('data-header', 'Sub-Tasks');
                    dataCell.querySelector('input').value = data;
                    row.appendChild(dataCell);
                } else if (i == 1) {
                    const selectMember = document.createElement('select');
                    selectMember.classList.add('assigned-members');
                    selectMember.addEventListener('change', () => getData(selectMember, 'member'));
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = 'Select Member';
                    selectMember.appendChild(defaultOption);

                    if (Array.isArray(allMembers)) {
                        for (const member of allMembers) {
                            memberOption = document.createElement('option');
                            memberOption.value = member['userId'];
                            memberOption.textContent = member['name'];
                            selectMember.appendChild(memberOption);
                        }
                    }
                    if (data != null) {
                        selectMember.value = subTask['assignedMemberId'];
                    }
                    dataCell.classList.add('col-2');
                    dataCell.classList.add('s-col-2');
                    dataCell.setAttribute('data-header', 'Member In-Charge');
                    dataCell.appendChild(selectMember);
                    row.appendChild(dataCell);
                } else if (i == 2) {
                    const selectPriority = document.createElement('select');
                    selectPriority.classList.add('priority', 'priority-none');
                    selectPriority.addEventListener('change', () => {
                        priorityColor(selectPriority);
                        getData(selectPriority, 'priority');
                    })
                    if (data == null) {
                        selectPriority.innerHTML = `
                        <option value="" selected disabled>None</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        `;
                    } else {
                        selectPriority.innerHTML = `
                        <option value="" selected disabled>None</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        `;
                        selectPriority.value = data;
                        if (data == 'high') {
                            selectPriority.classList.remove('priority-none');
                            selectPriority.classList.add('priority-high');
                        } else if (data == 'medium') {
                            selectPriority.classList.remove('priority-none');
                            selectPriority.classList.add('priority-medium');
                        } else if (data == 'low') {
                            selectPriority.classList.remove('priority-none');
                            selectPriority.classList.add('priority-low');
                        }
                    }
                    dataCell.classList.add('col-3');
                    dataCell.classList.add('s-col-3');
                    dataCell.setAttribute('data-header', 'Priority');
                    dataCell.appendChild(selectPriority);
                    row.appendChild(dataCell);
                } else if (i == 3) {
                    dataCell.classList.add('estimate');
                    if (data !== null) {
                        dataCell.textContent = `${data} hours`
                    } else {
                        dataCell.textContent = '_ hours';
                    }
                    dataCell.classList.add('col-4');
                    dataCell.classList.add('s-col-4');
                    dataCell.setAttribute('data-header', 'Estimate');
                    row.appendChild(dataCell);
                } else if (i == 4) {
                    dataCell.classList.add('assigned-date');
                    dataCell.addEventListener('click', () => {
                        showDatePicker(dataCell);
                    });
                    const inputDate = document.createElement('input');
                    inputDate.type = 'datetime-local';
                    inputDate.name = 'assigned-date';
                    inputDate.min = minDateTime;
                    inputDate.max = maxDateTime;
                    inputDate.addEventListener('change', () => {
                        getData(inputDate, 'assigned-date');
                    });
                    if (data != null) {
                        inputDate.value = data;
                    }
                    dataCell.classList.add('col-5');
                    dataCell.classList.add('s-col-5');
                    dataCell.setAttribute('data-header', 'Assigned Date');
                    dataCell.appendChild(inputDate);
                    row.appendChild(dataCell);
                } else if (i == 5) {
                    dataCell.classList.add('due-date');
                    dataCell.addEventListener('click', () => {
                        showDatePicker(dataCell);
                    })
                    const inputDate = document.createElement('input');
                    inputDate.type = 'datetime-local';
                    inputDate.name = 'due-date';
                    inputDate.min = minDateTime;
                    inputDate.max = maxDateTime;
                    inputDate.addEventListener('change', () => {
                        getData(inputDate, 'due-date');
                    })
                    if (data != null) {
                        inputDate.value = data;
                    }
                    dataCell.classList.add('col-6');
                    dataCell.classList.add('s-col-6');
                    dataCell.setAttribute('data-header', 'Due Date');
                    // dataCell.classList.add('due-date');
                    dataCell.appendChild(inputDate);
                    row.appendChild(dataCell);
                } else if (i == 6) {
                    const selectStatus = document.createElement('select');
                    selectStatus.classList.add('progress', 'progress-pending');
                    selectStatus.addEventListener('change', () => {
                        progressColor(selectStatus);
                        getData(selectStatus, 'status');
                    })
                    if (data == null) {
                        selectStatus.innerHTML = `
                        <option value="pending" selected disabled>Pending</option>
                        <option value="in progress">In progress</option>
                        <option value="completed">Completed</option>
                        `;
                    } else {
                        selectStatus.innerHTML = `
                        <option value="pending" selected disabled>Pending</option>
                        <option value="in progress">In progress</option>
                        <option value="completed">Completed</option>
                        `;
                        selectStatus.value = data;
                        if (data == 'in progress') {
                            selectStatus.classList.remove('progress-pending');
                            selectStatus.classList.add('progress-in-progress');
                        } else if (data == 'completed') {
                            selectStatus.classList.remove('progress-pending');
                            selectStatus.classList.add('progress-completed');
                        }
                    }
                    dataCell.classList.add('col-7');
                    dataCell.classList.add('s-col-7');
                    dataCell.setAttribute('data-header', 'Status');
                    dataCell.appendChild(selectStatus);
                    row.appendChild(dataCell);
                }
                i++;
            }
            // last cell of the row (delete button)
            deleteCell = document.createElement('td');
            deleteCell.innerHTML = `
            <button type="button" class="delete-sub-task"><span class="material-symbols-rounded">delete</span></button>
            `
            deleteCell.setAttribute('data-header', '');
            deleteCell.classList.add('col-8')
            deleteCell.classList.add('s-col-8');
            row.appendChild(deleteCell);
            table.appendChild(row);
        }
        if (task['sub-tasks'].length == 0) {
            emptyTr = document.createElement('tr')
            emptyTr.innerHTML = `
            <td colspan="7" style="text-align: center; font-style: italic; opacity: 0.7">No Sub-Tasks Available :D</td>
            `
            table.appendChild(emptyTr);
        }
        // add table row button
        const addTableRowDiv = document.createElement('div');
        addTableRowDiv.classList.add('add-table-row-div');
        addTableRowDiv.innerHTML = `
        <button class="add-table-row"><span class="material-symbols-rounded">add</span></button>
        `;

        taskDiv.appendChild(taskNameDateTimeDiv);
        taskDiv.appendChild(table);
        taskDiv.appendChild(addTableRowDiv);
        taskList.appendChild(taskDiv);
    }
    if (allProjectTasks.length == 0) {
        taskList.innerHTML = `
        <div class="null-msg" style="height: 80%;">
            <span style="opacity: 0.7; font-style: italic;">No Project Tasks Available :D</span>
        </div>
        `;
    }

    const startDate = document.querySelectorAll('td.assigned-date input[type="datetime-local"]');

    // When start-date is picked, update end-date's min value
    for (const date of startDate) {
        date.addEventListener('change', () => {
            const due = date.closest('tr').querySelector('td.due-date input[type="datetime-local"]');
            due.min = date.value;
        });
    }

    const endDate = document.querySelectorAll('td.due-date input[type="datetime-local"]');
    for (const date of endDate) {
        date.addEventListener('change', () => {
            const start = date.closest('tr').querySelector('td.assigned-date input[type="datetime-local"]');
            start.max = date.value;
        });
    }
    document.dispatchEvent(new Event('ProjectTaskLoaded'));
}

// Toggle Edit Button (Update Project Sub Task Name)
function toggleEditButton(element, action) {
    // const form = document.getElementById(formId);
    const form = element.closest('form');
    const inputSubTask = form.querySelector('input');
    const editButton = form.querySelector('button[type="button"]');
    const doneButton = form.querySelector('button[type="submit"]');
    if (action == 'open-input') {
        if (inputSubTask.hasAttribute('readonly')) {
            inputSubTask.removeAttribute('readonly');
            inputSubTask.focus();
            editButton.style.display = 'none';
            doneButton.style.display = 'inline';

            // inputSubTask.addEventListener('input', function () { // function declaration can use 'this'
            //     this.style.width = (this.value.length + 1.7) + "ch"; // ch is character unit
            // })
        }
    } else if (action == 'close-input') {
        if (!inputSubTask.hasAttribute('readonly')) {
            inputSubTask.setAttribute('readonly', true);
            editButton.style.display = 'inline';
            doneButton.style.display = 'none';
        }
    }
}

// Get Data functions
function getData(element, type) {
    const tableRow = element.closest('table tr');
    const projectSubTaskId = tableRow.getAttribute('data-project-sub-task-id');
    const formData = new FormData();
    formData.append('project-sub-task-id', projectSubTaskId);
    if (type == 'member') {
        formData.append('assigned-member', element.value);
    } else if (type == 'priority') {
        formData.append('priority', element.value);
    } else if (type == 'status') {
        formData.append('status', element.value);
        formData.append('time-zone', deviceTimeZone); // later remove, after create session and then save as session data
    } else if (type == 'assigned-date') {
        const assignedDate = element.value.slice(0, 19).replace('T', ' '); // YYYY-MM-DD HH:MM:SS
        formData.append('assigned-date', assignedDate);
    } else if (type == 'due-date') {
        const dueDate = element.value.slice(0, 19).replace('T', ' ');
        formData.append('due-date', dueDate);
    }
    fetch('PHP/taskUpdate.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        const response = JSON.parse(text);
        const successMsg = response.success;
        if (successMsg) {
            console.log(successMsg);
        } else {
            const failMsg = response.fail;
            console.log(failMsg);
        }
    })
    .catch(error => console.log('Error: ', error));
}

// Show Date Picker function (Assigned Date, Due Date)
function showDatePicker(element) {
    element.querySelector('input').showPicker();
}

// Alter Priority Color
function priorityColor(selectElement) {
    selectElement.classList.remove('priority-none', 'priority-high', 'priority-medium', 'priority-low');
    const value = selectElement.value;
    switch(value) {
        case 'none':
            selectElement.classList.add('priority-none');
            break;
        case 'high':
            selectElement.classList.add('priority-high');
            break;
        case 'medium':
            selectElement.classList.add('priority-medium');
            break;
        case 'low':
            selectElement.classList.add('priority-low');
            break;
    }
}

// Alter Progress Color
function progressColor(selectElement) {
    selectElement.classList.remove('progress-pending', 'progress-in-progress', 'progress-completed');
    const value = selectElement.value;
    switch(value) {
        case 'pending':
            selectElement.classList.add('progress-pending');
            break;
        case 'in progress':
            selectElement.classList.add('progress-in-progress');
            break;
        case 'completed':
            selectElement.classList.add('progress-completed');
            break;
    }
}
