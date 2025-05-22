document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');
    if (!userId) {
        console.error('User ID not found in URL');
        return;
    } else {
        document.getElementById("back-to-details").href = `userDetails.php?id=${encodeURIComponent(userId)}`;
    }
    fetch(`userMetricsHandler.php?getUserActivity=true&id=${encodeURIComponent(userId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error from server:", data.error);
                return;
            }
            document.getElementById('title').innerText = `${data.user.name}'s Activity Log`;
            document.getElementById('user-id').innerText = data.user.userId;
            document.getElementById('date-joined').innerText = data.user.joinedDate  || "N/A";
            populateActivityTable(data.activity);
        })
        .catch(error => console.error('Error fetching user activity:', error));
});


function populateActivityTable(activityData) {
    let activityTable = document.querySelector(".activity-table");
    activityTable.innerHTML = `
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Action Type</th>
                <th>Details</th>
                <th>IP Address</th>
                <th>Device/Browser</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;
    let tableBody = activityTable.querySelector("tbody");
    if (activityData && activityData.length > 0) {
        activityData.forEach(activity => {
            let row = document.createElement("tr");
            row.innerHTML = `
                <td data-label="Timestamp">${activity.timestamp}</td>
                <td data-label="Action Type">${activity.actionType}</td>
                <td data-label="Details">${activity.details}</td>
                <td data-label="IP Address">${activity.ipAddress}</td>
                <td data-label="Device/Browser">${activity.deviceBrowser}</td>
            `;
            tableBody.appendChild(row);
        });
    } else {
        let row = document.createElement("tr");
        row.innerHTML = `<td colspan="5" style="text-align:center; opacity: 0.7; font-style: italic;">No activity found :D</td>`;
        tableBody.appendChild(row);
    }
}
