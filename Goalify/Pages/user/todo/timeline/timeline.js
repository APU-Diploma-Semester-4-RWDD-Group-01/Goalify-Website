document.addEventListener("DOMContentLoaded", () => {

    const today = new Date();

    loadTaskByDate(today);

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
        current_date.setDate(current_date.getDate() + 1);
        display_date.textContent = formatDate(current_date);
        pick_date.setDate(current_date, true);
        loadTaskByDate(current_date);
        
    });   

    // ------------------------------------------- load task by date ----------------------------------------------------

    function loadTaskByDate(date) {
        const selected_date_format = date.getFullYear() + "-" + String(date.getMonth() + 1).padStart(2, "0") + "-" + String(date.getDate()).padStart(2, "0");
        
        console.log("fetched date: ", selected_date_format); 
        
        fetch(`timelineHandler.php?date=${selected_date_format}`)
        .then(response => response.text())
        .then(tasks => {
            return JSON.parse(tasks);
        })
        .then(tasks => {
            console.log("Fetched all tasks:", tasks);

            const timeline_body = document.getElementById("timeline-body");
            const no_task_message = document.querySelector(".no-task-condition");
            
            timeline_body.innerHTML = "";

            if (no_task_message) {
                no_task_message.style.display = "none";
            }           

            let hasTask = false;

            tasks.forEach(task => {
                
                addTaskToTable(task.task_name, task.time_plan);
                hasTask = true;
                
            });

            if (!hasTask) {
                no_task_message.style.display = "block";
            }

                   
        })
        .catch(error => console.error("Error fetching tasks:", error));                
    }
   

    const schedule_task_button = document.querySelector(".schedule-task-button");
    schedule_task_button.addEventListener("click", () => {
        const chosen_date = document.querySelector(".chosen_date");
        const chosen_date_input = new Date(chosen_date.value);
        const format_chosen_date = chosen_date_input.toLocaleDateString("en-CA");
        if(format_chosen_date >= today.toLocaleDateString("en-CA")) {
            createOverlay(
                'task-selection',
                `
                <div class = "select-container">
                    <label for="task_selected"><strong>Select task</strong></label>
                    <select id="task_selected" required></select>
                    
                    <label for="schedule-time"><strong>Schedule time</strong></label>
                    <input type="time" id="schedule-time" required></input>

                    <label for="deadline"><strong>Deadline</strong></label>
                    <input type="date" id="deadline" required></input>
                    
                    <div class="button">
                        <button type="button" id="close-task-selection">Back</button>
                        <button type="submit" id="save-selection">Save</button>
                    </div>
                </div>
                `
            );

            document.getElementById("deadline").setAttribute("min", new Date().toISOString().split("T")[0]);

            const taskDropdown = document.getElementById("task_selected");
            const closeTaskSelection = document.querySelector('#close-task-selection');
            const saveSelection = document.getElementById("save-selection");
                
            fetch('timelineHandler.php?unscheduled=true')
            .then(response => response.json())
            .then(data => {
                console.log(data);
                
                taskDropdown.innerHTML = '<option value="" disabled selected>Choose a task</option>';

                if (!Array.isArray(data) || data.length === 0) {
                    console.warn("No tasks available.");
                    return;
                }

                data.forEach(task => {
                    const option = document.createElement("option");
                    option.value = task.task_id;
                    option.textContent = task.task_name;
                    taskDropdown.appendChild(option);
                });
                console.log("Dropdown updated.");
            })
            .catch(error => console.error("Error: ", error));        
            
            saveSelection.addEventListener("click", () => {
                const selected_id = taskDropdown.value;
                const selected_name = taskDropdown.options[taskDropdown.selectedIndex].text;
                const selected_time = document.getElementById("schedule-time").value;
                const selected_deadline = document.getElementById("deadline").value;
                const selected_plan_date = document.querySelector(".chosen_date").value;
            
                if (!selected_id || !selected_time || !selected_deadline) {
                    return;
                }
                
                console.log(`ID: ${selected_id}, Task: ${selected_name}, Time: ${selected_time}, Deadline: ${selected_deadline}, Plan date: ${selected_plan_date}`);

                const newTaskform = document.querySelector("#task-selection");
                newTaskform.addEventListener("submit", (event) => {
                    event.preventDefault();

                    const form = new FormData(newTaskform);
                    form.append("task_id", selected_id);
                    form.append("task_name", selected_name);
                    form.append("time_plan", selected_time);
                    form.append("deadline", selected_deadline);
                    form.append("plan_date", selected_plan_date);
                    form.forEach((value, key) => {
                        console.log(key + ": " + value);
                    });
                    fetch('timelineHandler.php', {
                        method: "POST",
                        body: form
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        return JSON.parse(data);
                    })
                    .then(data => {
                        
                        if (data.message) {
                            addTaskToTable(selected_name, selected_time);
                            removeOverlay("task-selection");
                        }
                    })
                    .catch(error => console.error("Error: ", error));    
                })         
                        
            })
            closeTaskSelection.addEventListener('click', () => {
                removeOverlay("task-selection");          
            
            });      
        } else {
            alert("Cannot schedule task for previous date !!");
        }
    });        

    
    function addTaskToTable(task_name, task_time) {
        const timeline_body = document.getElementById("timeline-body");
        const no_task_message = document.querySelector(".no-task-condition"); 
        const row = document.createElement("tr");
        
        if (no_task_message) {
            no_task_message.style.display = "none";
        }
    
        const [hours, minutes] = task_time.split(":");                        
        const format_time = new Date(0, 0, 0, hours, minutes).toLocaleTimeString("en-GB", {
            hour: "numeric",
            minute: "2-digit",
            hour12: false
        });

        
        if (format_time.trim() !== null) {
            no_task_message.style.display = "none";
            row.innerHTML = `
                <td class="col-1"><div class="dot"></div>${format_time}</td>
                <td class="col-2">
                    ${task_name}
                    <span title="View details" class="material-symbols-rounded arrowdown"  data-task="${task_name}">keyboard_arrow_down</span>
                    <p class="task-desc" style="display: none;"></p>
                </td>
            `;

            if (timeline_body.innerHTML === "") {
                timeline_body.appendChild(row);
            }

            let inserted = false;
            const rows = timeline_body.querySelectorAll("tr");

            for (let i = 0; i < rows.length; i++) {
                const existing_time = rows[i].querySelector("td.col-1").textContent.trim().replace(/[^0-9:]/g, "");
                if (format_time < existing_time) {
                    timeline_body.insertBefore(row, rows[i]);
                    inserted = true;
                    break;
                }
            }
        
            if (!inserted) {
                timeline_body.appendChild(row);
            }    
            sortTimeline();        
        }

        function sortTimeline() {
            const timeline_body = document.getElementById("timeline-body");
            let rows = Array.from(timeline_body.querySelectorAll("tr"));

            rows.sort((a, b) => {
                const timeA = a.querySelector("td.col-1").textContent.trim();
                const timeB = b.querySelector("td.col-1").textContent.trim();
                return timeA.localeCompare(timeB);
            });

            rows.forEach(row => timeline_body.appendChild(row));
        }

        
                      
                    
        // ------------------------------------------- customize dot color ----------------------------------------------------

        const dots = document.querySelectorAll(".dot");
        const startColor = [0, 255, 247];
        const endColor = [57, 140, 255];  

        dots.forEach((dot, index) => {
            let ratio = 0;
            if (dots.length > 1) {
                ratio = index / (dots.length - 1);
            }else {
                ratio = 0.5;
            }
            
            const r = Math.round(startColor[0] + ratio * (endColor[0] - startColor[0]));
            const g = Math.round(startColor[1] + ratio * (endColor[1] - startColor[1]));
            const b = Math.round(startColor[2] + ratio * (endColor[2] - startColor[2]));

            dot.style.backgroundColor = `rgb(${r}, ${g}, ${b})`;
        });

        // ------------------------------------------- view description details ----------------------------------------------------

        row.querySelectorAll(".arrowdown").forEach(btn => {
            btn.addEventListener("click", () => {
                const task_name = btn.getAttribute("data-task");
                const task_desc = row.querySelector(".task-desc");
                fetchDesc(task_name, task_desc);
                if (task_desc.style.display === "none") {
                    task_desc.style.display = "block";
                } else {
                    task_desc.style.display = "none";
                }
            });
        });        

    }  

    function fetchDesc(taskName, taskDesc) {
        fetch(`timelineHandler.php?task_name=${taskName}`)
        .then(response => response.json())
        .then(desc => {
            if (desc.task_description) {
                taskDesc.textContent = desc.task_description;
            }else {
                taskDesc.textContent = "No description.";
            }
            
        })
        .catch(error => console.error("Error fetching description:", error));
    }

});