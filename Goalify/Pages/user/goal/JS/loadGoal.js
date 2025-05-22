document.addEventListener('DOMContentLoaded', loadGoals);

async function loadGoals() {
    const response = await fetch('PHP/loadGoal.php');
    const data = await response.json();

    const shortTermGoals = data.shortTermGoals;
    const longTermGoals = data.longTermGoals;
    const completedGoals = data.completedGoals;

    const shortTermList = document.querySelector('div.short-term-list');
    shortTermList.textContent = '';

    if (shortTermGoals.length !== 0) {
        shortTermGoals.forEach(goal => {
            const goalDiv = document.createElement('div');
            goalDiv.classList.add('goal');
            goalDiv.setAttribute('data-goal-id', goal.goalId);
            goalDiv.innerHTML = `
            <span class="material-symbols-rounded check">circle</span>
            <p>${goal.goalName}</p>
            <div class="delete-div">
                <span class="material-symbols-rounded delete">close</span>
            </div>
            `
            shortTermList.append(goalDiv);
        });
    } else {
        shortTermList.textContent = 'No Short-Term Goals :D';
        shortTermList.style.textAlign = "center";
        shortTermList.style.justifyContent = "center";
        shortTermList.style.opacity = "0.7";
        shortTermList.style.fontStyle = "italic";
    }
    

    const longTermList = document.querySelector('div.long-term-list');
    longTermList.textContent = '';

    if (longTermGoals.length !== 0) {
        longTermGoals.forEach(goal => {
            const goalDiv = document.createElement('div');
            goalDiv.classList.add('goal');
            goalDiv.setAttribute('data-goal-id', goal.goalId);
            goalDiv.innerHTML = `
            <span class="material-symbols-rounded check">circle</span>
            <p>${goal.goalName}</p>
            <div class="delete-div">
                <span class="material-symbols-rounded delete">close</span>
            </div>
            `;
            longTermList.append(goalDiv);
        });
    } else {
        longTermList.textContent = 'No Long-Term Goals :D';
        longTermList.style.textAlign = "center";
        longTermList.style.justifyContent = "center";
        longTermList.style.opacity = "0.7";
        longTermList.style.fontStyle = "italic";
    }
    

    const completedList = document.querySelector('div.completed-list');
    completedList.textContent = '';
    
    if (completedGoals.length !== 0) {
        completedGoals.forEach(goal => {
            if (goal.goalType == 'short-term') {
                var backgroundColor = '#1e87c869';
            } else if (goal.goalType == 'long-term') {
                var backgroundColor = '#1ebdc869';
            }
            const goalDiv = document.createElement('div');
            goalDiv.classList.add('goal');
            goalDiv.setAttribute('data-goal-id', goal.goalId);

            const completedDateTime = new Date(goal['completedDateTime']); // Convert timestamp to Date object
            const formattedDate = completedDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric'
                                    });
            const formattedTime = completedDateTime.toLocaleTimeString('en-GB', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true  // Use 12-hour time
                                    }).toUpperCase();

            goalDiv.innerHTML = `
            <p>${goal.goalName}</p>
            <p class="completed-date-time">Completed on ${formattedDate}, at ${formattedTime}</p>
            `;
            //wiiii
            goalDiv.style.backgroundColor = backgroundColor;
            completedList.append(goalDiv);
        });
    } else {
        completedList.textContent = 'No Goals Completed :D';
        completedList.style.textAlign = "center";
        completedList.style.justifyContent = "center";
        completedList.style.opacity = "0.7";
        completedList.style.fontStyle = "italic";
    }
    window.dispatchEvent(new Event('GoalsLoaded'));
}