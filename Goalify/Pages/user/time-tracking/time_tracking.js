document.addEventListener('DOMContentLoaded', () => {

    const exceptStartButton = document.querySelector('.otherbutton');
    const pauseButton = document.querySelector("#pause");
    const resetButton = document.querySelector("#reset");
    const resumeButton = document.querySelector("#resume");
    const taskList = document.querySelector('.tasklist');
    const wholeCircle = document.querySelector('#circle');

    // Initially hide other buttons
    exceptStartButton.classList.add("hidden");
    resetButton.classList.add("hidden");
    pauseButton.classList.add("hidden");
    resumeButton.classList.add("hidden");
    taskList.classList.add("active");
    wholeCircle.classList.add("hidden");

    // change the layout of task list based on window size
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1180) {
            taskList.classList.add("active");
        }
    });

    // initialize variable
    let hours, minutes, seconds, remainingTime, taskId, startTime, endTime, duration, timeTrackingDate;
    let countdown = null;
    let isTimerStarted = false;
    let isPaused = false;
    let totalDuration = 0;
    let pausedDuration = 0;
    let pausedStartTime = 0;
    let taskMap = {};
    let selectedTaskName = "";
    let selectedTaskId = "";
    let circleLength = 2 * Math.PI * 165;

    // get task name
    fetch('timeTrackingHandler.php?getTasks=1')
        .then(response => response.json())
        .then(data => {
            let taskList = document.querySelector(".task");

            if (data.length > 0) {
                taskList.innerHTML = "";
                data.forEach(task => {
                    taskMap[task.task_name] = task.task_id;  // map the task id and name for record purpose
                    let li = document.createElement("li");
                    li.innerHTML = `<span>${task.task_name}</span>`;
                    taskList.appendChild(li);
                });
            } else {
                taskList.innerHTML = '<div id="no-task">- No task is available here :D -</div>';
            }
        })
        .catch(error => console.error("Error fetching tasks:", error));


    // get focus records data
    const durationNotifyMessage = document.querySelector("#duration-notify-message");
    const noRecord = document.querySelector("#no-record");
    function fetchRecords() {
        fetch('timeTrackingHandler.php?getRecords=true')
            .then(response => response.json())
            .then(data => {
                renderTable(data);
            })
            .catch(error => console.error("Error fetching tasks:", error));
    }

    // reder focus record table based on provided data
    function renderTable(data) {
        let recordList = document.querySelector("#record-list");
        let ranking = 1;

        recordList.innerHTML = '';

        if (data.length > 0) {
            noRecord.style.display = 'none';
            durationNotifyMessage.style.display = 'flex';

            // adjust the table layout based on different screen size
            if (window.innerWidth <= 1180 && window.innerWidth > 480) {
                let headerRow = document.createElement("tr");
                headerRow.innerHTML = `
                    <th>Ranking</th>
                    <th>Task Name</th>
                    <th>Duration</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                `;
                recordList.appendChild(headerRow);
            }

            data.forEach(record => {
                if (window.innerWidth > 1180 || window.innerWidth <= 480) {
                    let row1 = document.createElement("tr");
                    let row2 = document.createElement("tr");
                    let row3 = document.createElement("tr");
                    row3.classList.add("start-end-time");

                    row1.innerHTML = ` 
                        <td rowspan="3" class="ranking">${ranking}</td>
                        <td colspan="3" class="taskName">${record.task_name}</td>
                    `;

                    row2.innerHTML = `
                        <td colspan="3" class="duration">${record.duration}</td>
                    `;

                    row3.innerHTML = `
                        <td>${record.startTime}</td>
                        <td class="arrow"><span class="material-symbols-outlined">trending_flat</span></td>
                        <td>${record.endTime}</td>
                    `;

                    recordList.appendChild(row1);
                    recordList.appendChild(row2);
                    recordList.appendChild(row3);

                } else if (window.innerWidth <= 1180 && window.innerWidth > 480) {
                    let row1 = document.createElement("tr");

                    row1.innerHTML = `
                    <td class="ranking">${ranking}</td>
                    <td class="taskName">${record.task_name}</td>
                    <td class="duration">${record.duration}</td>
                    <td>${record.startTime}</td>
                    <td>${record.endTime}</td>
                    `;

                    recordList.appendChild(row1);
                }

                ranking++

            });
        } else {
            noRecord.style.display = 'flex';
            durationNotifyMessage.style.display = 'none';
        }
    }

    // render record data to change the table layout based on different screen size
    window.addEventListener('resize', () => {
        fetch('timeTrackingHandler.php?getRecords=true')
            .then(response => response.json())
            .then(data => {
                renderTable(data);
            })
            .catch(error => console.error("Error fetching tasks:", error));
    });

    fetchRecords();

    // get focus record date
    const format = { day: 'numeric', month: 'long', year: 'numeric' };
    let formattedDate = new Date().toLocaleDateString('en-GB', format);
    document.querySelector("#date-text").textContent = formattedDate;

    // show toast notification
    const timerTaskName = document.querySelector('.task-name');
    function showToastNoti(message, id) {
        if (document.querySelector(`.toast-noti[data-id="${id}"]`)) return;

        let existingToast = document.querySelectorAll('.toast-noti');

        if (existingToast.length >= 2) {
            existingToast[0].remove();
        }

        let toast = document.createElement("div");
        toast.className = "toast-noti";
        toast.setAttribute("data-id", id);
        toast.innerHTML = `<p id="toast-message">${message}</p>`;

        let noti1Top = 85;
        let spacing = 60;
        let actualTop = noti1Top + existingToast.length * spacing;
        toast.style.top = `${actualTop}px`;

        timerTaskName.insertAdjacentElement("afterend", toast);


        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => {
                toast.remove();
                existingToast = document.querySelectorAll('.toast-noti')
                existingToast[0].style.transition = "transform 0.3s ease-in-out";
                existingToast[0].style.transform = "translateY(-60px)";
            }, 500);
        }, 3000);
    }

    // get specified format local timestamp
    function getLocalTimestamp() {
        let now = new Date();
        return now.getFullYear() + '-' +
            ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
            ('0' + now.getDate()).slice(-2) + ' ' +
            ('0' + now.getHours()).slice(-2) + ':' +
            ('0' + now.getMinutes()).slice(-2) + ':' +
            ('0' + now.getSeconds()).slice(-2);
    }

    // progress ring
    const circle = document.querySelector('#progress-ring');
    function circleOffset() {
        let offset = circleLength * (1 - (remainingTime / totalDuration));;
        circle.style.strokeDashoffset = offset;
        circle.style.transition = "stroke-dashoffset 0.1s linear";
    }

    const startButton = document.getElementById("startfocus");
    const endButton = document.querySelector("#endfocus");
    const presetContainer = document.querySelector('#preset-container');
    const donut = document.querySelector('.donut');
    const donutPresetWrapper = document.querySelector('.donut-preset-container-wrapper');
    const timerInput = document.querySelectorAll('.timer');
    const hour = document.querySelector('#hour');
    const minute = document.querySelector('#minute');
    const second = document.querySelector('#second');

    // store the user's initial hour, minute, and second input to reset the countdown when the restart button is clicked.    
    let orihour = 0;
    let orimin = 0;
    let orisec = 0;

    // start focus mode
    function startFocusMode(reset = false) {
        if (reset) {
            hours = orihour;
            minutes = orimin;
            seconds = orisec;

        } else {
            hours = parseInt(hour.value) || 0;
            minutes = parseInt(minute.value) || 0;
            seconds = parseInt(second.value) || 0;

            orihour = hours;
            orimin = minutes;
            orisec = seconds;
        }

        startButton.readOnly = false;
        circle.style.transition = "none";
        circle.style.strokeDashoffset = 0;

        // if user did not input any time, show error
        if (hours === 0 && minutes === 0 && seconds === 0) {
            showToastNoti("Please <strong> enter a number </strong> before starting focus mode.", "error-no-time");
            startButton.readOnly = true;
            return;
        }

        // if user did not select any task, show error
        if (!selectedTaskName) {
            showToastNoti("Please <strong> select a task </strong> before starting focus mode.", "error-no-task");
            return;
        }

        if (window.innerWidth <= 768) {
            donutPresetWrapper.classList.add('start-clicked');
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                donutPresetWrapper.classList.add('start-clicked');
            }
        });

        startButton.classList.add("hidden");
        exceptStartButton.classList.remove("hidden");
        pauseButton.classList.remove("hidden");
        resetButton.classList.add("hidden");
        presetContainer.classList.add("hidden");
        donut.classList.add("start-clicked");
        wholeCircle.classList.remove("hidden");
        timerInput.forEach(input => {
            input.classList.add('start-clicked');
            input.readOnly = true;
        });

        recordStartTime = getLocalTimestamp();
        startTime = Date.now();
        console.log(startTime);
        totalDuration = (hours * 3600 + minutes * 60 + seconds) * 1000;
        endTime = startTime + totalDuration;
        console.log(endTime);

        if (seconds > 59) {
            minutes += parseInt(seconds / 60);
            seconds = seconds % 60;
        }

        if (minutes > 59) {
            hours += parseInt(minutes / 60);
            minutes = minutes % 60;
        }

        if (hours > 99) {
            hours = 99;
            minutes = 59;
            seconds = 59;
        }

        // display uer entered time
        hour.value = String(hours).padStart(2, "0");
        minute.value = String(minutes).padStart(2, "0");
        second.value = String(seconds).padStart(2, "0");

        isTimerStarted = true;
        startCountDown();
    }


    // count down 
    function startCountDown() {
        if (countdown) {
            clearInterval(countdown);
        }

        countdown = setInterval(() => {
            let now = Date.now();
            remainingTime = Math.max(0, endTime - now);
            circleOffset();

            // if the countown ends
            if (remainingTime === 0) {
                clearInterval(countdown);
                isTimerStarted = false;
                timeTrackingDate = new Date().toISOString().split('T')[0];
                recordEndTime = getLocalTimestamp();
                duration = calculateDuration(startTime, endTime);
                insertFocusRecord(taskId, recordStartTime, recordEndTime, duration, timeTrackingDate);
                pauseButton.classList.add("hidden");
                resetButton.classList.remove("hidden");
                pausedDuration = 0;
                hour.value = hour.defaultValue;
                minute.value = minute.defaultValue;
                second.value = second.defaultValue;
                return;
            }

            let displayHours = Math.floor(remainingTime / 3600000);
            let displayMinutes = Math.floor((remainingTime % 3600000) / 60000);
            let displaySeconds = Math.floor((remainingTime % 60000) / 1000);

            // display the remaining hour, minute and second
            hour.value = String(displayHours).padStart(2, "0");
            minute.value = String(displayMinutes).padStart(2, "0");
            second.value = String(displaySeconds).padStart(2, "0");

        }, 100)

        isPaused = false;
    }

    // pause timer
    function pauseCountDown() {
        if (!isPaused) {
            clearInterval(countdown);
            pausedStartTime = Date.now();
            isPaused = true;
        }

    }

    // resume timer
    function resumeCountDown() {
        if (isPaused) {
            let now = Date.now();
            pausedDuration += now - pausedStartTime;;
            endTime += now - pausedStartTime;;
            isPaused = false;
            startCountDown();
        }
    }

    // end countdown
    function endCountdown() {
        clearInterval(countdown);
        isTimerStarted = false;

        if (window.innerWidth <= 768) {
            donutPresetWrapper.classList.remove('start-clicked');
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                donutPresetWrapper.classList.remove('start-clicked');
            }
        });

        returnDefaultTimer();
    }

    // calculate total duration of the focus session (excluding paused time)
    function calculateDuration(startTime, endTime) {
        return Math.floor((endTime - startTime - pausedDuration) / 1000); // Duration in seconds
    }

    // insert focus record to database
    function insertFocusRecord(taskId, startTime, endTime, duration, timeTrackingDate) {
        let formData = new FormData();
        formData.append("taskId", taskId);
        formData.append("startTime", startTime);
        formData.append("endTime", endTime);
        formData.append("duration", duration);
        formData.append("timeTrackingDate", timeTrackingDate);

        fetch("timeTrackingHandler.php?insertFocusRecord=true", {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                console.log("Record Inserted:", data);
                insertActivityLog("A011", `Focus session for task ${taskId} completed!`);
                fetchRecords();
            }) 
            .catch(error => console.error("Error:", error)); 
    }

    // insert activity log
    function insertActivityLog(actionId, details) {
        let formData = new FormData();
        formData.append("actionId", actionId);
        formData.append("details", details);

        fetch("timeTrackingHandler.php?insertActivityLog=true", {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(data => console.log("Record Inserted:", data))
            .catch(error => console.error("Error:", error));
    }

    // return the timer to default
    const presetBtn = document.querySelectorAll('.preset');
    function returnDefaultTimer() {
        clearInterval(countdown);
        startButton.classList.remove("hidden");
        exceptStartButton.classList.add("hidden");
        presetContainer.classList.remove("hidden");
        donut.classList.remove("start-clicked");
        wholeCircle.classList.add("hidden");
        pausedDuration = 0;
        timerInput.forEach(input => {
            input.classList.remove('start-clicked');
            input.readOnly = false;
            input.value = input.defaultValue;
        })
        presetBtn.forEach(btn => btn.classList.remove("clicked"));
    }

    // when start button is clicked
    startButton.addEventListener("click", () => {
        startFocusMode();
        insertActivityLog("A010", `Start a focus session for task ${taskId}!`)
    });

    // when end button is clicked
    endButton.addEventListener("click", () => {
        endCountdown();
    });

    // when pause button is clicked
    pauseButton.addEventListener("click", () => {
        pauseCountDown();
        pauseButton.classList.add("hidden");
        resumeButton.classList.remove("hidden");
    })

    // when resume button is clicked
    resumeButton.addEventListener("click", () => {
        resumeCountDown();
        pauseButton.classList.remove("hidden");
        resumeButton.classList.add("hidden");
    })

    // when reset button is clicked
    resetButton.addEventListener("click", () => {
        startFocusMode(true);
    })

    // display confirm box
    const confirmBox = document.querySelector('#confirm-box');
    document.querySelector(".task").addEventListener('click', (event) => {
        let task = event.target.closest("li"); // check if clicked element is an <li>

        if (!task) return;

        if (confirmBox.parentNode) {
            confirmBox.parentNode.removeChild(confirmBox);
        }
        window.currentTask = task; // update the current task
        task.insertAdjacentElement('afterend', confirmBox); // insert confirmBox below the clicked task
        confirmBox.classList.add('active');
    });

    // close the confirm box
    function closeConfirmBox() {
        window.currentTask = null;
        if (confirmBox.parentNode) {
            confirmBox.parentNode.removeChild(confirmBox);
        }
    }

    // if user click yes to select the task
    const timerExpandNav = document.querySelector('.timer-expand-nav');
    const taskCheck = document.getElementById('task-check');
    const taskChevron = document.querySelector('#chevron-down');
    taskCheck.addEventListener('click', () => {
        if (window.innerWidth <= 1180) {
            timerExpandNav.classList.remove('show');
            taskChevron.classList.remove('rotate');
        }

        if (window.currentTask) {
            selectedTaskName = window.currentTask.querySelector('span').textContent;
            selectedTaskId = taskMap[selectedTaskName];
        }

        // if timer is started and user click yes to select task
        if (isTimerStarted) {

            // pause the countdown and show task change confirmation box
            pauseCountDown();

            window.createOverlay('task-change-confirmation',
                `
                    <p id="confirmation-message"> Do you want to switch tasks? Please note that this will <strong> reset </strong> the countdown.</p>
                    <div class="button">
                        <button type="button" id="cancel-changing-task">Cancel</button>
                        <button type="button" id="confirm-changing-task">Confirm</button>
                    </div>
                    `,
                '')

            const cancel = document.querySelector('#cancel-changing-task');
            const confirm = document.querySelector('#confirm-changing-task');

            cancel.addEventListener('click', () => {
                window.removeOverlay('task-change-confirmation');
                isTimerStarted = true;
                resumeCountDown();
            })

            confirm.addEventListener('click', () => {
                endCountdown();
                window.removeOverlay('task-change-confirmation');
                if (selectedTaskName) {
                    timerTaskName.childNodes[0].textContent = selectedTaskName + " ";
                    taskName = selectedTaskName;
                    taskId = selectedTaskId;
                    timerTaskName.classList.add("selected-task");
                }
            })

        } else {

            // if timer didnt start then directly change the task
            if (window.currentTask) {
                returnDefaultTimer();
                timerTaskName.childNodes[0].textContent = selectedTaskName + " ";
                taskName = selectedTaskName;
                taskId = selectedTaskId;
                timerTaskName.classList.add("selected-task");
            }
        }

        closeConfirmBox();
    });

    // if user click no, close the confirm box
    const taskXmark = document.getElementById('task-xmark');
    taskXmark.addEventListener('click', () => {
        closeConfirmBox();
    });

    // when records button is clicked
    const records = document.querySelector('.record');
    const recordbtn = document.querySelector('#records');
    recordbtn.addEventListener('click', () => {
        records.classList.add("active");
        taskList.classList.remove("active");
        recordbtn.classList.add("btn-clicked");
        taskbtn.classList.remove("btn-clicked");
    });

    // when tasks button is clicked
    const taskbtn = document.querySelector('#tasks');
    taskbtn.addEventListener('click', () => {
        taskList.classList.add("active");
        records.classList.remove("active");
        taskbtn.classList.add("btn-clicked");
        recordbtn.classList.remove("btn-clicked");
    });

    // when mouseenter timer-expand-navi 
    timerExpandNav.addEventListener('mouseenter', () => {
        xmark.classList.add('active')
    })

    // when mouseleave timer-expand-navi 
    timerExpandNav.addEventListener('mouseleave', () => {
        xmark.classList.remove('active')
    })

    // when user close the side bar
    const timerContent = document.querySelector('.timer-content');
    const xmark = document.querySelector('#xmark');
    xmark.addEventListener('click', () => {
        timerExpandNav.classList.add("hidden");
        taskList.classList.remove("active");
        records.classList.remove("active");
        taskbtn.classList.remove("btn-clicked");
        recordbtn.classList.remove("btn-clicked");
        timerContent.classList.add("expand-nav-closed");
        donut.classList.add("expand-nav-close");
        presetContainer.classList.add("expand-nav-close");
        donutPresetWrapper.classList.add("expand-nav-closed");

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1180) {
                donutPresetWrapper.classList.add('expand-nav-closed');
            } else if (window.innerWidth <= 1180) {
                donutPresetWrapper.classList.remove('expand-nav-closed');
            }
        });
    })

    // when side bar is closed and user click task button
    taskbtn.addEventListener('click', () => {
        taskList.classList.add("active");
        records.classList.remove("active");
        timerExpandNav.classList.remove("hidden");
        timerContent.classList.remove("expand-nav-closed");
        donut.classList.remove("expand-nav-close");
        presetContainer.classList.remove("expand-nav-close");
        donutPresetWrapper.classList.remove("expand-nav-closed");
    });

    // when side bar is closed and user click record button
    recordbtn.addEventListener('click', () => {
        records.classList.add("active");
        taskList.classList.remove("active");
        timerExpandNav.classList.remove("hidden");
        timerContent.classList.remove("expand-nav-closed");
        donut.classList.remove("expand-nav-close");
        presetContainer.classList.remove("expand-nav-close");
        donutPresetWrapper.classList.remove("expand-nav-closed");
    });

    // when user click preset button to select preset time
    presetBtn.forEach(button => {
        button.addEventListener('click', () => {
            const isAlreadyClicked = button.classList.contains("clicked");

            presetBtn.forEach(btn => btn.classList.remove("clicked"));

            if (!isAlreadyClicked) {
                button.classList.add("clicked");

                let [hh, mm, ss] = button.innerText.split(":");

                hour.value = hh;
                minute.value = mm;
                second.value = ss;
            } else {
                hour.value = "";
                minute.value = "";
                second.value = "";
            }
        });
    });

    // when screen size <= 1180 and task is clicked
    taskChevron.addEventListener('click', () => {
        taskChevron.classList.toggle("rotate");
        timerExpandNav.classList.toggle("show");
    });

    // move the .record element under .timer-content on small screens
    function moveRecordBelowContent() {
        const timerMain = document.querySelector('.timer-main');
        const timerContent = document.querySelector('.timer-content');
        const record = document.querySelector('.record');
        const taskChevron = document.querySelector('.task-name span');
        const timerExpandNav = document.querySelector('.timer-expand-nav');
        const timerSideNav = document.querySelector('.timer-side-nav');

        if (window.innerWidth <= 1180) {
            // place the record under timer content
            if (record && timerContent && record.previousElementSibling !== timerContent) {
                timerMain.appendChild(record);
                timerMain.appendChild(timerExpandNav);
            }

            record.classList.add("active");
            taskChevron.classList.add("active");
            xmark.classList.add("hidden");

        } else {
            // move the record back to its original position
            if (timerExpandNav && record && !timerExpandNav.contains(record)) {
                timerExpandNav.appendChild(record);
                timerSideNav.appendChild(timerExpandNav);
            }

            record.classList.remove("active");
            taskChevron.classList.remove("active");
            xmark.classList.remove("hidden");
            timerExpandNav.classList.remove("show");
            if (taskChevron.classList.contains("rotate")) {
                taskChevron.classList.toggle("rotate");
            }
        }
    }

    // run function on page load and when window resizes
    window.addEventListener('load', moveRecordBelowContent);
    window.addEventListener('resize', moveRecordBelowContent);

    timerInput.forEach((input, index) => {
        input.addEventListener("input", function () {

            let value = this.value.replace(/[^0-9]/g, "");

            if (value.length > 2) {
                value = value.slice(0, 2); // limit entered time to 2 digits
            }

            this.value = value;
        });

        input.addEventListener("keydown", function (event) {
            if (event.key === "-" || event.key === "e") {
                event.preventDefault(); // prevent the minus sign and exponential notation
            }

            // move to next input when right arrow is pressed
            if (event.key === "ArrowRight") {
                let nextInput = timerInput[index + 1];
                if (nextInput) {
                    nextInput.focus();
                    event.preventDefault();
                }
            }

            // move to previous input when left arrow is pressed
            if (event.key === "ArrowLeft") {
                let prevInput = timerInput[index - 1];
                if (prevInput) {
                    prevInput.focus();
                    event.preventDefault();
                }
            }
        })

        // check for paste value
        input.addEventListener("paste", function (event) {
            event.preventDefault();
            let pasteData = event.clipboardData.getData("text");
            let actualData = pasteData.replace(/[^0-9]/g, "").slice(0, 2);  // ensure only 2 digit no
            this.value = actualData;
        })
    });




})