document.addEventListener("DOMContentLoaded", () => {

    // ------------------------------------------- task status dropdown ----------------------------------------------------
    const icons = document.querySelectorAll(".dropdown");
    const labels = document.querySelectorAll("#task-status strong");
    const content = document.querySelectorAll(".task");

    icons.forEach((icon, index) => {
        icon.addEventListener("click", () => toggleDropdown(index));
    });
    labels.forEach((label, index) => {
        label.addEventListener("click", () => toggleDropdown(index));
    });

    function toggleDropdown(index) {
        icons[index].classList.toggle("rotate");
        content[index].classList.toggle("show");
    }

    // ------------------------------------------- fetch data from database ----------------------------------------------------

    const pastdate_container = document.querySelector("#past-date .task");
    const doing_container = document.querySelector("#doing .task");
    const done_container = document.querySelector("#done .task");

    fetch('statusHandler.php')
    .then(response => response.json())
    .then(data => {
        console.log(data);       
        
        pastdate_container.innerHTML = "";
        doing_container.innerHTML = "";
        done_container.innerHTML = ""; 
                
        data.forEach(task => {
            let duedate = " ";
            if (task.deadline) {
                duedate = new Date(task.deadline).toLocaleDateString("en-GB");
            
                let container;
                if (task.complete_status === "past_date") {
                    container = pastdate_container;
                }else if (task.complete_status === "doing") {
                    container = doing_container;
                }else if (task.complete_status === "done") {
                    container = done_container;
                }             
                
                if (container.innerHTML.trim() !== "") {
                    container.innerHTML += "<hr>";
                }
                container.innerHTML += `
                    <span>${task.task_name}</span>
                    <span class="duedate"><strong>Due: ${duedate}</strong></span>
                `;

                update_count();
            }else {
                let container;
                if (task.complete_status === "past_date") {
                    container = pastdate_container;
                }else if (task.complete_status === "doing") {
                    container = doing_container;
                }else if (task.complete_status === "done") {
                    container = done_container;
                }             
                
                if (container.innerHTML.trim() !== "") {
                    container.innerHTML += "<hr>";
                }
                container.innerHTML += `
                    <span class="taskname">${task.task_name}</span>
                    <span class="duedate"><strong>${duedate}</strong></span>
                `;
                update_count();
                
            }     
            function update_count() {
                let pastCount = pastdate_container.querySelectorAll("span").length/2;
                let doingCount = doing_container.querySelectorAll("span").length/2;
                let doneCount = done_container.querySelectorAll("span").length/2;

                console.log("Past Date Count:", pastCount, "Doing Count:", doingCount, "Done Count:", doneCount); 
                
                document.querySelector("#past-date .item_count").textContent = `(${pastCount} items)`;
                document.querySelector("#doing .item_count").textContent = `(${doingCount} items)`;
                document.querySelector("#done .item_count").textContent = `(${doneCount} items)`;
            }              
            
        });  
        
    })
    .catch(error => console.error("Error fetching tasks:", error));
    
});
