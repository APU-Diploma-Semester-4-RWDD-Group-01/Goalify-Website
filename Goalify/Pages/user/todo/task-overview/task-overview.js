document.addEventListener("DOMContentLoaded", () => {
     
    let today = new Date();
    loadTaskByDate(today);

    // ------------------------------------------- save data ----------------------------------------------------

    
    document.querySelectorAll('.content #task-categories .to-do-button').forEach(button =>{
        
        button.addEventListener('click', () => {
            const chosen_date = document.querySelector(".chosen_date");
            const chosen_date_input = new Date(chosen_date.value);
            const format_chosen_date = chosen_date_input.toLocaleDateString("en-CA");
            if(format_chosen_date >= today.toLocaleDateString("en-CA")) {
                createOverlay(
                    'todo-insertion',
                    `
                    <div class = "insert-container">
                        <label for="task-name"><strong>Title</strong></label>
                        <input name="task-name" id="task-name" maxlength="50" required autocomplete="off">
                        <br>
                        <label for="task-description"><strong>Description</strong></label>
                        <textarea name="task-description" id="task-description" autocomplete="off"></textarea>
                        
                        <div class="button">
                            <button type="button" id="close-todo-insertion">Back</button>
                            <button type="submit" id="save-task">Save</button>
                        </div>
                    </div>
                    `
                );
                
                const task_name_input = document.getElementById("task-name");       
                
                task_name_input.focus();
                
                const task_category = button.closest("div.category").getAttribute("data-category");
                const closeTaskInsertion = document.querySelector('#close-todo-insertion');
        
                const newTodoForm = document.querySelector("#todo-insertion");
                newTodoForm.addEventListener("submit", (event) => {
                    event.preventDefault();
                    console.log(task_category);
        
                    const form = new FormData(newTodoForm);
                    form.append("category", task_category);
                    form.append("complete_status", "doing");
                    form.forEach((value, key) => {
                        console.log(key + ": " + value);
                    });
                    fetch('taskHandler.php', {
                        method: "POST",
                        body: form
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
        
                        const task_name = task_name_input.value;
                        displayTask(task_name, task_category);
        
                        removeOverlay('todo-insertion');
                    })
                    .catch(error => console.error("Error: ", error));
                })
                
                closeTaskInsertion.addEventListener('click', () => {
                    removeOverlay('todo-insertion');
                })
            }else {
                alert("Cannot add task for previous date !!");
            }
    
        })
    });   

    // ------------------------------------------- display data ----------------------------------------------------

    function displayTask(task, category, task_id, complete_status, completed_date = null) {
        const main_category = document.querySelector(`.category[data-category="${category}"]`);
        if (!main_category) return;
    
        const taskList = main_category.querySelector(".task-content");
        const newTask = document.createElement("p");
        newTask.classList.add("task-item");
        newTask.style.display = "flex";
        newTask.style.alignItems = "center";
        newTask.style.cursor = "pointer";

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.style.cursor = "pointer";
        checkbox.classList.add("checkbox");
        const taskText = document.createElement("span");
        taskText.textContent = task;

        
        
        newTask.addEventListener("click", (text) => {
            if (text.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }

            if (checkbox.checked) {
                taskText.style.color = "#8d8d8d";
                complete_status = "done";
                completed_date = new Date().toISOString().split("T")[0];
            }else{
                taskText.style.color = "inherit";
                complete_status = "doing";  
                completed_date = "";              
            }

            const form = new FormData();
            form.append("task_id", task_id);
            form.append("complete_status", complete_status);
            form.append("completed_date", completed_date);
            
            fetch("updateTaskStatus.php", {
                method: "POST",
                body: form
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Task updated:", task, "status: ", complete_status, "completed date: ",  completed_date);
                } else {
                    console.error("Failed to update task status.");
                }
            })
            .catch(error => console.error("Error:", error)); 

        });

        checkbox.checked = complete_status === "done";
        if (checkbox.checked) {
            taskText.style.color = "#8d8d8d";
        }else{
            taskText.style.color = "inherit";
        };        

        newTask.appendChild(checkbox);
        newTask.appendChild(taskText);
        taskList.append(newTask);
    }


    // ------------------------------------------- date picker ----------------------------------------------------

    function formatDate(date) {
        const format = {day: "2-digit", month: "long", year: "numeric"};
        return date.toLocaleDateString("en-GB", format); 
    }
    
    const display_date = document.querySelector(".chosen_date");
    let current_date = new Date();

    display_date.textContent = formatDate(current_date);

    const pick_date = flatpickr(display_date, {
        enableTime: false,
        dateFormat: "d F Y", 
        defaultDate: current_date,
        clickOpens: true,
        maxDate: today,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                current_date = selectedDates[0];
                display_date.textContent = formatDate(current_date);
                console.log("manually selected date: ", current_date);
                loadTaskByDate(current_date);
            }
        }
    });
    
    display_date.addEventListener("click", () => {
        pick_date.open();
    });
    
    const prev_date = document.querySelector(".arrowleft");
    const next_date = document.querySelector(".arrowright");

    prev_date.addEventListener("click", () => {
        current_date.setDate(current_date.getDate() - 1);
        display_date.textContent = formatDate(current_date);
        pick_date.setDate(current_date, true);
        loadTaskByDate(current_date);
    });

    next_date.addEventListener("click", () => {
        if (current_date.toDateString() !== today.toDateString()) {
            current_date.setDate(current_date.getDate() + 1);
            display_date.textContent = formatDate(current_date);
            pick_date.setDate(current_date, true);
            loadTaskByDate(current_date);
        }
        
    });
    
// ------------------------------------------- load task by date ----------------------------------------------------

    function loadTaskByDate(date) {
        const selected_date_format = date.toLocaleDateString("en-CA");
        console.log("fetched tasks for: ", selected_date_format);

        fetch("taskHandler.php")
        .then(response => response.json())
        .then(tasks => {

            document.querySelectorAll(".task-content").forEach(line => line.innerHTML = "");
            tasks.forEach(task => {
                console.log(task);
                const task_date = task.created_at.split(" ")[0];
                console.log(task_date);
                console.log(selected_date_format);

                if (task_date === selected_date_format) {
                    console.log("matched: ", task.task_name);
                    displayTask(task.task_name, task.category, task.task_id, task.complete_status);
                }
            });
        })
    }    

});
