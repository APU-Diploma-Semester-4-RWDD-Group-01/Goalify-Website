document.addEventListener("DOMContentLoaded", () => {

    // -------------------------------------------task status-----------------------------------------------------

    // task status dropdown
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
    
    // -------------------------------------------timeline-----------------------------------------------------

    // color gradient for timeline dots
    const dots = document.querySelectorAll(".dot");
    // Define start and end colors (cyan to blue)
    const startColor = [0, 255, 247];  // #00FFF7
    const endColor = [57, 140, 255];   //rgb(57, 140, 255)

    dots.forEach((dot, index) => {
        const ratio = index / (dots.length - 1); // Normalize index (0 to 1)
        
        // Linearly interpolate between startColor and endColor
        const r = Math.round(startColor[0] + ratio * (endColor[0] - startColor[0]));
        const g = Math.round(startColor[1] + ratio * (endColor[1] - startColor[1]));
        const b = Math.round(startColor[2] + ratio * (endColor[2] - startColor[2]));

        dot.style.backgroundColor = `rgb(${r}, ${g}, ${b})`;
    });

    // -------------------------------------------task overview-----------------------------------------------------

    // const addTodoButton = document.querySelector('.content #task-categories .to-do-button');
    // const body = document.body;

    // addTodoButton.addEventListener('click', () => {
    //     createOverlay(
    //         'todo-insertion',
    //         `
    //         <form action="task-overview.php" method="POST">
    //             <div class = "insert-container">
    //                 <label for="task-name"><strong>Title</strong></label>
    //                 <input name="task-name" id="task-name" maxlength="50" required autocomplete="off">
    //                 <br>
    //                 <label for="task-description"><strong>Description</strong></label>
    //                 <textarea name="task-description" id="task-description" autocomplete="off"></textarea>
    //                 <div class="button">
    //                     <button type="button" id="close-todo-insertion">Back</button>
    //                     <button type="submit" id="save-task">Save</button>
    //                 </div>
    //             </div>
    //         </form>
    //         `
    //     );
        
    //     const task_name = document.getElementById("task-name");
    //     task_name.focus();
    //     const task_desc = document.getElementById("task-description");
    //     const task_category = document.getElementById("important-urgent");
    //     const closeTaskInsertion = document.querySelector('#close-todo-insertion');
        
    //     closeTaskInsertion.addEventListener('click', () => {
    //         removeOverlay('todo-insertion');
    //     })

    //     // document.querySelector("#save-task").addEventListener('click', () => {
    //     //     fetch("task-overview.php", {
    //     //         method: "POST",
    //     //         headers: {"Content-Type": "application/x-www-form-urlencoded"},
    //     //         body: `task_name=${encodedURLIComponent(task_name)}&task_description=${encodedURLIComponent(task_desc)}&category=${encodedURLIComponent(task_category)}`
    //     //     })
    //     //     .then (response => response.text())
    //     //     .then(data => {
    //     //         alert(data);
    //     //         location.reload();
                
    //     //         removeOverlay("todo-insertion");
    //     //     })
    //     //     .catch(error => console.error("Error: ", error));
    //     // })
    //     form = new FormData();
    //     form.append("task_name", task_name);
    //     form.append("task_description", task_desc);
    //     form.append("category", task_category);
    //     fetch("/Goalify/Pages/user/todo/task-overview/taskHandler.php", {
    //         method: "POST",
    //         body: form
    //     })
    //     .then(response => response.text())
    //     .then(data => {
    //         console.log(data);
    //     })
    //     .catch(error => console.error("Error: ", error));
    // })
});
