document.addEventListener('DOMContentLoaded', () => {
    const shortTermGoalButton = document.getElementById('add-short-term-goal');
    const longTermGoalButton = document.getElementById('add-long-term-goal');

    if (shortTermGoalButton) {
        shortTermGoalButton.addEventListener('click', () => {
            window.createOverlay('new-goal', `
            <label for="new-short-goal">New Goal</label>
            <input type="text" name="new-short-goal" id="new-short-goal" autofocus placeholder="Short-Term Goal" autocomplete="off">
            <div class="button">
                <button type="button" id="close-new-goal-form">Cancel</button>
                <button type="submit" id="confirm-new-goal-form">Create</button>
            </div>
            `);

            const newShortTermGoal = document.getElementById('new-goal');
            newShortTermGoal.addEventListener('submit', (event) => {
                event.preventDefault();
                const shortTermGoalForm = new FormData(newShortTermGoal);
                fetch('/Goalify/Pages/user/goal/PHP/goalHandler.php', {
                    method: 'POST',
                    body: shortTermGoalForm
                })
                .then (response => response.json())
                .then(response => {
                    const successMsg = response.success;
                    if (successMsg) {
                        console.log(successMsg);
                        alert(successMsg);
                        removeOverlay('new-goal');
                        location.reload();
                    } else {
                        const failMsg = response.fail;
                        console.log(failMsg);
                        alert(failMsg);
                    }
                });
            })

            const closeShortTermGoalFormButton = document.getElementById('close-new-goal-form');
            closeShortTermGoalFormButton.addEventListener('click', () => {
                removeOverlay('new-goal');
            })
        });
    }

    if (longTermGoalButton) {
        longTermGoalButton.addEventListener('click', () => {
            window.createOverlay('new-goal', `
                <label for="new-long-goal">New Goal</label>
                <input type="text" name="new-long-goal" id="new-long-goal" autofocus placeholder="Long-Term Goal" autocomplete="off">
                <div class="button">
                    <button type="button" id="close-new-goal-form">Cancel</button>
                    <button type="submit" id="confirm-new-goal-form">Create</button>
                </div>
                `);
    
                const newLongTermGoal = document.getElementById('new-goal');
                newLongTermGoal.addEventListener('submit', (event) => {
                    event.preventDefault();
                    const longTermGoalForm = new FormData(newLongTermGoal);
                    fetch('/Goalify/Pages/user/goal/PHP/goalHandler.php', {
                        method: 'POST',
                        body: longTermGoalForm
                    })
                    .then (response => response.json())
                    .then(response => {
                        const successMsg = response.success;
                        if (successMsg) {
                            console.log(successMsg);
                            alert(successMsg);
                            removeOverlay('new-goal');
                            location.reload();
                        } else {
                            const failMsg = response.fail;
                            console.log(failMsg);
                            alert(failMsg);
                        }
                    });
                })
    
                const closeShortTermGoalFormButton = document.getElementById('close-new-goal-form');
                closeShortTermGoalFormButton.addEventListener('click', () => {
                    removeOverlay('new-goal');
                })
        });
    }
});

window.addEventListener('GoalsLoaded', () => {
    // Check Goals
    const checkButton = document.querySelectorAll('span.material-symbols-rounded.check');
    for (const button of checkButton) {
        button.addEventListener('click', () => {
            button.textContent = 'task_alt';
            const goalDiv = button.closest('div.goal');
            const goalId = goalDiv.getAttribute('data-goal-id');
            const goalName = goalDiv.querySelector('p').textContent;
            const completedGoalForm = new FormData();
            completedGoalForm.append('completed-goal-id', goalId);
            completedGoalForm.append('completed-goal-name', goalName);
            fetch('/Goalify/Pages/user/goal/PHP/goalHandler.php', {
                method: 'POST',
                body: completedGoalForm
            })
            .then (response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                    location.reload();
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                    alert(failMsg);
                }
            });
        })
    }

    const deleteButton = document.querySelectorAll('span.material-symbols-rounded.delete');
    for (const button of deleteButton) {
        button.addEventListener('click', () => {
            const goalDiv = button.closest('div.goal');
            const goalId = goalDiv.getAttribute('data-goal-id');
            const goalName = goalDiv.querySelector('p').textContent;
            console.log(goalName);
            const deleteGoalForm = new FormData();
            deleteGoalForm.append('delete-goal-id', goalId);
            deleteGoalForm.append('delete-goal-name', goalName);
            fetch('/Goalify/Pages/user/goal/PHP/goalHandler.php', {
                method: 'POST',
                body: deleteGoalForm
            })
            .then (response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                    location.reload();
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                    alert(failMsg);
                }
            });
        })
    }
})