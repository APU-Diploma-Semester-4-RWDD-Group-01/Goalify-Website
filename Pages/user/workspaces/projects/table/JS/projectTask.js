// Author: Phang Shea Wen
// Date: 2025/02/06
// Filename: table.php
// Description: in-line script for adding and deleting project task
document.addEventListener('ProjectTaskLoaded', () => {
    const body = document.body;
    const editTaskButtons = document.querySelectorAll('button.material-symbols-rounded.edit-task');
    const deleteTaskButtons = document.querySelectorAll('button.material-symbols-rounded.delete-task');
    const taskList = document.querySelector('div.task-list');

    // edit and update project task
    editTaskButtons.forEach(button => {
        button.addEventListener('click', () => {
            const taskNameDateTimeDiv = button.closest('div.task-name-datetime-edit');
            const taskNameForm = taskNameDateTimeDiv.querySelector('div form.project-task-name-form');
            const taskDiv = taskNameDateTimeDiv.closest('div.task');
            const projectTaskId = taskDiv.getAttribute('data-project-task-Id'); // get project task id from attribute

            const taskNameInput = taskNameForm.querySelector('input.project-task-name');
            const doneButton = taskNameForm.querySelector('button.submit-project-task-name-button');
            
            if (taskNameInput.hasAttribute('readonly')) {
                taskNameInput.removeAttribute('readonly');
                taskNameInput.focus();
                doneButton.style.display = 'inline';
                button.style.display = 'none';
            }

            taskNameInput.addEventListener('input', function () { // function declaration can use 'this'
                this.style.width = (this.value.length + 0.5) + "ch"; // ch is character unit
            })

            doneButton.addEventListener('click', () => {
                if (!taskNameInput.hasAttribute('readonly')) {
                    taskNameInput.setAttribute('readonly', true);
                    doneButton.style.display = 'none';
                    button.style.display = 'inline';
                }
            })

            // Using XML HTTP Request (traditional AJAX request) =D
            taskNameForm.addEventListener('submit', (event) => {
                event.preventDefault(); // prevent default form submission

                // retrieve form data
                const taskNameFormData = new FormData(taskNameForm);
                taskNameFormData.append('project-task-id', projectTaskId);

                // create new XML HTTP request
                const xhr = new XMLHttpRequest();

                // set up request
                xhr.open('POST', 'PHP/taskUpdate.php', true);

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        taskNameInput.value = response.projectTaskName;
                        taskNameInput.classList.add('fit-content');
                        console.log(response.projectTaskName);
                        alert(`Project Task is renamed to '${response.projectTaskName}'`);
                    } else {
                        // display an error message
                        console.log('Form submission failed: ', xhr.statusText);
                    }
                }

                xhr.send(taskNameFormData);
            })
        })
    })

    // delete project task
    deleteTaskButtons.forEach(button => {
        const taskDiv = button.closest('div.task');
        const projectTaskId = taskDiv.getAttribute('data-project-task-Id');

        button.addEventListener('click', () => {
            // Using fetch (modern AJAX request) =D
            fetch('PHP/taskUpdate.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'delete-project-task-id=' + encodeURIComponent(projectTaskId)
            })
            .then(response => response.text()) // convert response to text
            .then(text => {
                const response = JSON.parse(text); // JS object containing parsed JSON response
                const successMsg = response.success;
                console.log(successMsg);
                if (successMsg) {
                    alert(successMsg);
                    taskList.removeChild(taskDiv);
                }
            })
            .catch(error => console.log('Error: ', error));
        })
    })

    // edit and update project sub-task name
    const subTaskNameForms = document.querySelectorAll('td form.sub-task-name');
    subTaskNameForms.forEach(form => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const tableRow = form.closest('table tr');
            const projectSubTaskId = tableRow.getAttribute('data-project-sub-task-id');
            // const projectSubTaskId = '#P-ST51125'; // testing
            const formInput = form.querySelector('input');
            const projectSubTaskNameForm = new FormData(form);
            projectSubTaskNameForm.append("project-sub-task-id", projectSubTaskId);

            fetch('PHP/taskUpdate.php', {
                method: 'POST',
                // headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: projectSubTaskNameForm
            })
            .then(response => response.json()) // convert response to JSON
            .then(response => { // JS object containing parsed JSON response
                const newProjectSubTaskName = response.newProjectSubTaskName;
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                }
                if (newProjectSubTaskName) {
                    formInput.value = newProjectSubTaskName;
                }
            })
        })
    })

    // delete project sub-task
    const deleteSubTaskButtons = document.querySelectorAll('button.delete-sub-task');
    deleteSubTaskButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tableRow = button.closest('table tr');
            const parentNode = tableRow.parentNode;
            const projectSubTaskId = tableRow.getAttribute('data-project-sub-task-id');
            // const projectSubTaskId = '#P-ST51125'; // testing
            
            const deleteProjectSubTaskForm = new FormData();
            deleteProjectSubTaskForm.append('delete-project-sub-task-id', projectSubTaskId);

            fetch('PHP/taskUpdate.php', {
                method: 'POST',
                body: deleteProjectSubTaskForm
            })
            .then(response => response.json()) // convert response to JSON
            .then(data => { //JSON object containing parsed JSON response
                const successMsg = data.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                    parentNode.removeChild(tableRow);
                }
            })
        })
    })
})