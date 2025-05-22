// ------------------------------------------- Quote Section -------------------------------------------
function fetchQuote() {
    fetch("achievementHandler.php?quote=true")
        .then(response => response.json())
        .then(data => {
            console.log("API Response:", data);
            if (!data || data.error) {
                throw new Error("Invalid quote data received");
            }
            const quoteElement = document.querySelector(".quote"); // select quote element in html element
            if (quoteElement) {
                quoteElement.textContent = `"${data.q}" - ${data.a}`; // display quote and author
            } else {
                console.warn("Quote element not found in the DOM.");
            }
        })
        .catch(error => {
            console.error("Error fetching quote:", error);
            document.querySelector(".quote").textContent = "Failed to load quote.";
        });
}

fetchQuote();

document.querySelectorAll(".round-category").forEach(category => {
    category.addEventListener("mouseenter", function (event) {
        const tooltip = document.createElement("div");
        tooltip.classList.add("tooltip");
        tooltip.textContent = this.getAttribute("data-category");
        document.body.appendChild(tooltip);
        const moveTooltip = (event) => {
            tooltip.style.left = `${event.pageX + 10}px`; // position tooltip near the cursor
            tooltip.style.top = `${event.pageY + 10}px`;
        };
        moveTooltip(event);
        category.addEventListener("mousemove", moveTooltip); // update position when mousemove
        category.addEventListener("mouseleave", () => {
            tooltip.remove(); // remove tooltip when mouse leaves
        }, { once: true }); // ensure event runs only once
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const userId = document.getElementById("user-id").value;
    function fetchAchievements(month, year) {
        fetch(`achievementHandler.php?month=${month}&year=${year}&userId=${encodeURIComponent(userId)}`)
            .then(response => response.json())
            .then(data => {
                updateRecordSection(data.focusData);  // update record section
                updateChart(data.completedTasks);     // update pie chart
                updateCategoryPercentage(data.completedTasks); // update task count per category
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    // ------------------------------------------- Record Section Formatting -------------------------------------------
    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1); // capitalize first letter
    }
    
    function updateRecordSection(data) {
        const recordList = document.querySelector(".record-list");
        recordList.innerHTML = ""; // clear previous records
        if (!data || data.length === 0) {
            recordList.innerHTML = "<li>No focus data found.</li>";
            return;
        }
        data.forEach(category => {
            const capitalizedCategory = capitalizeFirstLetter(category.category);
            const listItem = document.createElement("li"); // create list item
            listItem.innerHTML = `<strong>${capitalizedCategory}</strong><br>Average Focus: ${(category.avgTimeSpent / 60 / 60).toFixed(2)} Hours<br><br>`;
            recordList.appendChild(listItem); // add item to list
        });
    }

    // ------------------------------------------- Pie Chart Update -------------------------------------------
    function updateChart(data) {
        const pieChart = document.getElementById("pie-chart");
        const tooltip = document.getElementById("pie-tooltip");
        if (!pieChart || !tooltip) {
            console.error("Pie chart or tooltip element is missing.");
            return;
        }
        pieChart.innerHTML = "";
        if (!data || data.length === 0) {
            console.warn("No completed tasks found.");
            return;
        }
        let totalCompleted = data.reduce((sum, item) => sum + item.totalCompletedTasks, 0) || 1; // Sum up total tasks
        let angleStart = 0;
        let colors = {
            "urgent": "var(--red-color)",
            "plan ahead": "var(--orange-color)",
            "handle fast": "var(--yellow-color)",
            "on hold": "var(--green-color)"
        };
        console.log("Data for Pie Chart:", data);
        console.log("Creating pie slice for category:", data[0].category);
        // if only tasks completed within one category
        if (data.length === 1) {
            let category = data[0].category;
            let color = colors[category] || "gray";
            // create a full circle, not slice
            let circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
            circle.setAttribute("cx", "50");  // center x
            circle.setAttribute("cy", "50");  // center y
            circle.setAttribute("r", "50");   // radius
            circle.setAttribute("fill", color);
            circle.dataset.category = category;
            circle.dataset.count = data[0].totalCompletedTasks;
            circle.addEventListener("mouseenter", (event) => {
                const cat = capitalizeFirstLetter(event.target.dataset.category);
                tooltip.textContent = `${cat}: ${event.target.dataset.count} task${event.target.dataset.count > 1 ? 's' : ''}`;
                tooltip.style.display = "block";
            });
            circle.addEventListener("mousemove", (event) => {
                tooltip.style.left = `${event.pageX + 10}px`;
                tooltip.style.top = `${event.pageY + 10}px`;
            });
            circle.addEventListener("mouseleave", () => {
                tooltip.style.display = "none";
            });
            pieChart.appendChild(circle);
        } else {
            data.forEach((item) => {
                let angleSize = (item.totalCompletedTasks / totalCompleted) * 360;
                let angleEnd = angleStart + angleSize;
                let path = createPieSlice(angleStart, angleEnd, colors[item.category] || "gray");
                path.dataset.category = item.category;
                path.dataset.count = item.totalCompletedTasks;
                path.addEventListener("mouseenter", (event) => {
                    const category = capitalizeFirstLetter(event.target.dataset.category);
                    tooltip.textContent = `${category}: ${event.target.dataset.count} tasks`;
                    tooltip.style.display = "block";
                });
                path.addEventListener("mousemove", (event) => {
                    tooltip.style.left = `${event.pageX + 10}px`;
                    tooltip.style.top = `${event.pageY + 10}px`;
                });
                path.addEventListener("mouseleave", () => {
                    tooltip.style.display = "none";
                });
                pieChart.appendChild(path);
                angleStart = angleEnd;
            });
        }
    }

    // Function to create SVG pie slice - define by coordinate: start point(x1, y1) , end point(x2, y2), den arc connect between them
    function createPieSlice(startAngle, endAngle, color) {
        // x = centerX + radius * cos(theta)
        // y = centerY + radius * sin(theta)
        // calculate first point of the circle
        const x1 = 50 + 50 * Math.cos((Math.PI * startAngle) / 180);
        const y1 = 50 + 50 * Math.sin((Math.PI * startAngle) / 180);
        // calculate second point of the circle
        const x2 = 50 + 50 * Math.cos((Math.PI * endAngle) / 180);
        const y2 = 50 + 50 * Math.sin((Math.PI * endAngle) / 180);
        const largeArcFlag = endAngle - startAngle > 180 ? 1 : 0; // determine the arc is big (>180) or small slice (<180)
        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
        path.setAttribute(
            "d",
            `M 50 50 L ${x1} ${y1} A 50 50 0 ${largeArcFlag} 1 ${x2} ${y2} Z` 
            // M 50 50 - move to center (50,50)
            // L x1 y1 - line to the first point
            // A 50 50 0 largeArcFlag 1 x2 y2- arc to the second point
            // A rx ry x-axis-rotation large-arc-flag sweep-flag x y
            // rx, ry - X n Y radii of the ellipse (arc size); x-axis-rotation - rotation angle of the ellipse (usually 0); large-arc-flag - 0 (small) 1 (large); sweep-flag - 0 (counterclockwise) 1 (clockwise); x,y - end point of the arc
            // Z - close the shape
        );
        path.setAttribute("fill", color);
        path.setAttribute("stroke", "none"); // remove outline
        return path;
    }

    // ------------------------------------------- Update Number of Tasks Completed  -------------------------------------------
    function updateCategoryPercentage(data) {
        const categories = {
            "urgent": "urgent-count",
            "plan ahead": "plan-ahead-count",
            "handle fast": "handle-fast-count",
            "on hold": "on-hold-count"
        };
        // loop thru each category in the categories object
        Object.keys(categories).forEach(category => {
            const element = document.getElementById(categories[category]);
            if (element) {
                // find task data for the current category
                const taskData = data.find(item => item.category.toLowerCase() === category.toLowerCase());
                // update element with task count or set to 0 if not found
                element.textContent = taskData ? `${taskData.totalCompletedTasks} tasks` : "0 tasks";
            } else {
                console.warn(`Element not found for category: ${category}`);
            }
        });
    }

    // ------------------------------------------- Date Picker -------------------------------------------
    const display_date = document.getElementById("selected-date");
    const prev_date = document.getElementById("prev-month");
    const next_date = document.getElementById("next-month");
    let current_date = new Date();
    // Format Month-Year
    function formatMonthYear(date) {
        return date.toLocaleDateString("en-GB", { month: "long", year: "numeric" });
    }
    // Set Initial Display Date
    display_date.textContent = formatMonthYear(current_date);
    fetchAchievements(current_date.getMonth() + 1, current_date.getFullYear(), userId);
    // Handle Previous Month
    prev_date.addEventListener("click", () => {
        current_date.setMonth(current_date.getMonth() - 1);
        display_date.textContent = formatMonthYear(current_date);
        fetchAchievements(current_date.getMonth() + 1, current_date.getFullYear(), userId);
    });
    // Handle Next Month
    next_date.addEventListener("click", () => {
        current_date.setMonth(current_date.getMonth() + 1);
        display_date.textContent = formatMonthYear(current_date);
        fetchAchievements(current_date.getMonth() + 1, current_date.getFullYear(), userId);
    });

});