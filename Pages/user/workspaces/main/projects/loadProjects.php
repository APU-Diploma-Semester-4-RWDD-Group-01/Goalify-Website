<?php
session_start();
require '../../../../includes/db.php';
require_once '../../workspace.php';
require_once '../../../../includes/session.php';

if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');

if (isset($_SESSION['workspace_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    $allProjects = getProjects($pdo, $workspaceId);
    $pendingProjects = getPendingProjects($pdo, $workspaceId);
    $ongoingProjects = getOngoingProjects($pdo, $workspaceId);
    $searchProjects = null;
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search-projects-input'])) {
        $keyword = $_POST['search-projects-input'];
        $searchProjects = searchProjectByKeyWord($pdo, $keyword, $workspaceId);
    }
    $ownerId = getOwner($pdo, $workspaceId)[0]['userId'];
    $owner = ($ownerId == $userId);
}


?>

<script>
    // Load Projects
    document.addEventListener('DOMContentLoaded', () => {
        const isOwner = <?php echo $owner ? 'true' : 'false'; ?>;

        // Hide add project button if is not owner
        const addProjectButton = document.querySelector('.search-add-projects #add-projects-button');
        if (!isOwner) {
            addProjectButton.style.display = 'none';
        } else {
            addProjectButton.style.display = 'flex';
        }

        loadOngoingProjects();
        loadPendingProjects();
        const searchProjects = <?php echo json_encode($searchProjects) ?>;
        // console.log(searchProjects);
        if (searchProjects) {
            loadSearchProjects();
        } else {
            loadAllProjects();
        }
    })

    function loadOngoingProjects() {
        const heading = document.getElementById('ongoing-project-heading');
        const hr = document.getElementById('ongoing-project-hr');
        const projectWrapper = document.querySelector('.content .project-wrapper.ongoing');
        const scrollOngoingProject = document.querySelector('.content .project-wrapper.ongoing .projects');
        const ongoingProjects = <?php echo json_encode($ongoingProjects) ?>;
        if (Array.isArray(ongoingProjects)) {
            if (ongoingProjects.length == 0) {
                const pendingHr = document.getElementById('pending-project-heading');
                pendingHr.style.marginTop = '0';
                heading.style.display = 'none';
                hr.style.display = 'none';
                projectWrapper.style.display = 'none';
            }
            for (const project of ongoingProjects) {
                projectDiv = document.createElement('div');
                // let projectId = project['projectName'].replace(' ', '-');
                let projectId = project['projectId'];
                projectDiv.setAttribute('data-project-id', projectId)
                // projectDiv.id = `${projectId}`;
                projectDiv.classList.add('project');
                var divContent1 = ``;
                if (project['projectDeadline']) {
                    const deadline = new Date(project['projectDeadline']); // Convert timestamp to Date object
                    const formattedDDLDate = deadline.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                            });
                    const formattedDDLTime = deadline.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            hour12: true  // Use 24-hour time
                                            }).toUpperCase();
                    divContent1 = `
                    <ul>
                        <li>${project['projectName']}</li>
                        <li class="deadline">DDL: ${formattedDDLDate}, at ${formattedDDLTime}</li>
                    </ul>
                    `;
                } else {
                    divContent1 = `
                    <ul>
                        <li>${project['projectName']}</li>
                        <li class="deadline">DDL: Undefined</li>
                    </ul>
                    `;
                }
                
                projectDivContent = divContent1 + `
                <div class="project-actions">
                    <button class="complete"><span class="material-symbols-rounded">check</span><p>End</p></button>
                    <span class="divider"></span>
                    <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                    <span class="divider"></span>
                    <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                </div>
                `;
                projectDiv.innerHTML = projectDivContent;
                scrollOngoingProject.appendChild(projectDiv);
            }

            // Scrolling
            const scrollAmount = 1000;
            const scrollLeft = projectWrapper.querySelector('.scroll-left');
            const scrollRight = projectWrapper.querySelector('.scroll-right');
            scrollLeft.addEventListener('click', () => {
                scrollOngoingProject.scrollBy({left: -scrollAmount, behavior: 'smooth'});
            })
            scrollRight.addEventListener('click', () => {
                scrollOngoingProject.scrollBy({left: scrollAmount, behavior: 'smooth'});
            })
            if (ongoingProjects.length <= 3) {
                scrollLeft.style.display = 'none';
                scrollRight.style.display = 'none';
            }
        } else {
            console.log('no projects?');
        }
    }

    function loadPendingProjects() {
        const heading = document.getElementById('pending-project-heading');
        const hr = document.getElementById('pending-project-hr');
        const projectWrapper = document.querySelector('.content .project-wrapper.pending');
        const scrollPendingProject = document.querySelector('.content .project-wrapper.pending .projects');
        const pendingProjects = <?php echo json_encode($pendingProjects) ?>;
        if (Array.isArray(pendingProjects)) {
            if (pendingProjects.length == 0) {
                heading.style.display = 'none';
                hr.style.display = 'none';
                projectWrapper.style.display = 'none';
            }
            for (const project of pendingProjects) {
                projectDiv = document.createElement('div');
                // let projectId = project['projectName'].replace(' ', '-');
                let projectId = project['projectId'];
                // projectDiv.id = `${projectId}`;
                projectDiv.setAttribute('data-project-id', projectId);
                projectDiv.classList.add('project');
                // created date time
                const createdDateTime = new Date(project['projectCreatedDateTime']); // Convert timestamp to Date object
                const formattedDate = createdDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric'
                                        });
                const formattedTime = createdDateTime.toLocaleTimeString('en-GB', {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: true  // Use 24-hour time
                                        }).toUpperCase();
                projectDivContent = `
                <ul>
                    <li>${project['projectName']}</li>
                    <li class="created-date-time">Added on ${formattedDate}, at ${formattedTime}</li>
                </ul>
                <div class="project-actions">
                    <button class="start"><span class="material-symbols-rounded">play_arrow</span><p>Start</p></button>
                    <span class="divider"></span>
                    <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                </div>
                `;
                projectDiv.innerHTML = projectDivContent;
                scrollPendingProject.appendChild(projectDiv);
            }

            // Scrolling
            const scrollAmount = 1000;
            const scrollLeft = projectWrapper.querySelector('.scroll-left');
            const scrollRight = projectWrapper.querySelector('.scroll-right');
            scrollLeft.addEventListener('click', () => {
                scrollPendingProject.scrollBy({left: -scrollAmount, behavior: 'smooth'});
            })
            scrollRight.addEventListener('click', () => {
                scrollPendingProject.scrollBy({left: scrollAmount, behavior: 'smooth'});
            })
            if (pendingProjects.length <= 3) {
                scrollLeft.style.display = 'none';
                scrollRight.style.display = 'none';
            }
        } else {
            console.log('no projects?');
        }
    }

    function loadAllProjects() {
        const numProjects = document.querySelector('#num-projects');
        const listProjects = document.querySelector('.content .projects-list')
        const allProjects = <?php echo json_encode($allProjects) ?>;
        // all projects
        if (Array.isArray(allProjects)) {
            numProjects.textContent = `( ${allProjects.length} )`;
            for (const project of allProjects) {
                projectDiv = document.createElement('div');
                // let projectId = project['projectName'].replace(' ', '-');
                projectDiv.classList.add('project');
                let projectId = project['projectId'];
                // projectDiv.id = `${projectId}`;
                projectDiv.setAttribute('data-project-id', projectId);
                if (project['projectEnd']) {
                    // completed date time
                    const endDateTime = new Date(project['projectEnd']); // Convert timestamp to Date object
                    const formattedDate = endDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                            });
                    const formattedTime = endDateTime.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            hour12: true  // Use 24-hour time
                                            }).toUpperCase();
                    projectDivContent = `
                        <div class="all-project-details">
                            <p>${project['projectName']}</p>
                            <p class="ended">Ended on ${formattedDate}, at ${formattedTime}</p>
                        </div>
                        <div class="project-actions">
                            <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                            <span class="divider"></span>
                            <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                        </div>
                    `;
                } else if (project['projectStart']) {
                    // deadline
                    if (project['projectDeadline']) {
                        const deadline = new Date(project['projectDeadline']); // Convert timestamp to Date object
                        const formattedDDLDate = deadline.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                                day: '2-digit',
                                                month: 'short',
                                                year: 'numeric'
                                                });
                        const formattedDDLTime = deadline.toLocaleTimeString('en-GB', {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                second: '2-digit',
                                                hour12: true  // Use 24-hour time
                                                }).toUpperCase();
                        projectDivContent = `
                            <div class="all-project-details">
                                <p>${project['projectName']}</p>
                                <p class="deadline">DDL: ${formattedDDLDate}, at ${formattedDDLTime}</p>
                            </div>
                            <div class="project-actions">
                                <button class="complete"><span class="material-symbols-rounded">check</span><p>End</p></button>
                                <span class="divider"></span>
                                <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                                <span class="divider"></span>
                                <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                            </div>
                        `;
                    } else {
                        projectDivContent = `
                            <div class="all-project-details">
                                <p>${project['projectName']}</p>
                                <p class="deadline">DDL: Undefined</p>
                            </div>
                            <div class="project-actions">
                                <button class="complete"><span class="material-symbols-rounded">check</span><p>End</p></button>
                                <span class="divider"></span>
                                <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                                <span class="divider"></span>
                                <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                            </div>
                        `;
                    }
                } else {
                    // created date time
                    const createdDateTime = new Date(project['projectCreatedDateTime']); // Convert timestamp to Date object
                    const formattedDate = createdDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                            });
                    const formattedTime = createdDateTime.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            hour12: true  // Use 24-hour time
                                            }).toUpperCase();
                    projectDivContent = `
                        <div class="all-project-details">
                            <p>${project['projectName']}</p>
                            <p class="created-date-time">Added on ${formattedDate}, at ${formattedTime}</p>
                        </div>
                        <div class="project-actions">
                            <button class="start"><span class="material-symbols-rounded">play_arrow</span><p>Start</p></button>
                            <span class="divider"></span>
                            <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                        </div>
                    `;
                }
                projectDiv.innerHTML = projectDivContent;
                listProjects.appendChild(projectDiv);
                listProjects.appendChild(document.createElement('hr'));
            }
            if (allProjects.length == 0) {
                listProjects.innerHTML = `
                <hr>
                <div class="null-msg" style="height: 30%;">
                    <span style="opacity: 0.7; font-style: italic;">No Projects Available :D</span>
                </div>
                `;
            }
        } else {
            listProjects.appendChild(document.createElement('hr'));
            listProjects.innerHTML = `
            <div class="null-msg" style="height: 30%;">
                <span style="opacity: 0.7; font-style: italic;">No Projects Available :D</span>
            </div>
            `;
        }
    }

    function loadSearchProjects() {
        const numProjects = document.querySelector('#num-projects');
        const listProjects = document.querySelector('.content .projects-list')
        const searchProjects = <?php echo json_encode($searchProjects) ?>;
        listProjects.innerHTML = `<hr>`;
        // all projects
        if (Array.isArray(searchProjects)) {
            numProjects.textContent = `( ${searchProjects.length} )`;
            for (const project of searchProjects) {
                projectDiv = document.createElement('div');
                let projectId = project['projectId'];
                projectDiv.classList.add('project');
                projectDiv.setAttribute('data-project-id', projectId);
                if (project['projectEnd']) {
                    // completed date time
                    const endDateTime = new Date(project['projectEnd']); // Convert timestamp to Date object
                    const formattedDate = endDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                            });
                    const formattedTime = endDateTime.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            hour12: true  // Use 24-hour time
                                            }).toUpperCase();
                    projectDivContent = `
                        <div class="all-project-details">
                            <p>${project['projectName']}</p>
                            <p class="ended">Ended on ${formattedDate}, at ${formattedTime}</p>
                        </div>
                        <div class="project-actions">
                            <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                            <span class="divider"></span>
                            <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                        </div>
                    `;
                } else if (project['projectStart']) {
                    // deadline
                    if (project['projectDeadline']) {
                        const deadline = new Date(project['projectDeadline']); // Convert timestamp to Date object
                        const formattedDDLDate = deadline.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                                day: '2-digit',
                                                month: 'short',
                                                year: 'numeric'
                                                });
                        const formattedDDLTime = deadline.toLocaleTimeString('en-GB', {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                second: '2-digit',
                                                hour12: true  // Use 24-hour time
                                                }).toUpperCase();
                        projectDivContent = `
                            <div class="all-project-details">
                                <p>${project['projectName']}</p>
                                <p class="deadline">DDL: ${formattedDDLDate}, at ${formattedDDLTime}</p>
                            </div>
                            <div class="project-actions">
                                <button class="complete"><span class="material-symbols-rounded">check</span><p>End</p></button>
                                <span class="divider"></span>
                                <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                                <span class="divider"></span>
                                <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                            </div>
                        `;
                    } else {
                        projectDivContent = `
                            <div class="all-project-details">
                                <p>${project['projectName']}</p>
                                <p class="deadline">DDL: Undefined</p>
                            </div>
                            <div class="project-actions">
                                <button class="complete"><span class="material-symbols-rounded">check</span><p>End</p></button>
                                <span class="divider"></span>
                                <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                                <span class="divider"></span>
                                <button class="open"><span class="material-symbols-rounded">folder_open</span><p>Open</p></button>
                            </div>
                        `;
                    }
                } else {
                    // created date time
                    const createdDateTime = new Date(project['projectCreatedDateTime']); // Convert timestamp to Date object
                    const formattedDate = createdDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                            });
                    const formattedTime = createdDateTime.toLocaleTimeString('en-GB', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            hour12: true  // Use 24-hour time
                                            }).toUpperCase();
                    projectDivContent = `
                        <div class="all-project-details">
                            <p>${project['projectName']}</p>
                            <p class="created-date-time">Added on ${formattedDate}, at ${formattedTime}</p>
                        </div>
                        <div class="project-actions">
                            <button class="start"><span class="material-symbols-rounded">play_arrow</span><p>Start</p></button>
                            <span class="divider"></span>
                            <button class="delete"><span class="material-symbols-rounded">close</span><p>Delete</p></button>
                        </div>
                    `;
                }
                projectDiv.innerHTML = projectDivContent;
                listProjects.appendChild(projectDiv);
                console.log(projectDiv);
                listProjects.appendChild(document.createElement('hr'));
            }
            if (searchProjects.length == 0) {
                listProjects.appendChild(document.createElement('hr'));
                listProjects.innerHTML = `
                <div class="null-msg" style="height: 30%;">
                    <span style="opacity: 0.7; font-style: italic;">No Projects Found :D</span>
                </div>
                `;
            }
        } else {
            listProjects.appendChild(document.createElement('hr'));
            listProjects.innerHTML = `
            <div class="null-msg" style="height: 30%;">
                <span style="opacity: 0.7; font-style: italic;">No Projects Found :D</span>
            </div>
            `;
        }
    }

    // Add New Projects - not done
    document.addEventListener('DOMContentLoaded', () => {
        // Add New Project
            const addProjectButton = document.querySelector('.content .all-projects-section .search-add-projects #add-projects-button');
            const body = document.body;

            addProjectButton.addEventListener('click', () => {
                window.createOverlay(
                    'new-project-form',
                    `
                    <label for="new-project-name">New Project</label>
                    <input type="text" name="new-project-name" id="new-project-name" autofocus placeholder="project_01" autocomplete="off">
                    <div class="button">
                        <button type="button" id="close-new-project-form">Cancel</button>
                        <button type="submit" id="confirm-new-project-form">Create</button>
                    </div>
                    `,
                    ''
                );

                const newProjectForm = document.querySelector('form#new-project-form');
                newProjectForm.addEventListener('submit', () => {
                    const newProjectFormData = new FormData(newProjectForm);
                    const workspaceId = document.querySelector('.content .all-projects').getAttribute('data-workspace-id');
                    // newProjectFormData.append('workspace-id', workspaceId);
                    fetch('/Goalify/Pages/user/workspaces/main/projects/projectHandler.php', {
                        method: 'POST',
                        body: newProjectFormData
                    })
                    .then(response => response.json())
                    .then(response => {
                        const successMsg = response.success;
                        if (successMsg) {
                            console.log(successMsg);
                            alert(successMsg);
                            location.reload()
                        } else {
                            const failMsg = response.fail;
                            console.log(failMsg);
                            alert(failMsg);
                        }
                    })
                })

                const closeProjectForm = document.querySelector('#close-new-project-form');
                closeProjectForm.addEventListener('click', () => {
                    window.removeOverlay('new-project-form');
                })
            })

            // End Project
            const endProjectButtons = document.querySelectorAll('button.complete');
            for (const button of endProjectButtons) {
                button.addEventListener('click', () => {
                    const projectDiv = button.closest('div.project');
                    const projectId = projectDiv.getAttribute('data-project-id'); // changed
                    const endProjectForm = new FormData();
                    endProjectForm.append('action', 'endProject');
                    endProjectForm.append('project-id', projectId);
                    fetch('/Goalify/Pages/user/workspaces/main/projects/projectHandler.php', {
                        method: 'POST',
                        body: endProjectForm
                    })
                    .then(response => response.json())
                    .then(response => {
                        const successMsg = response.success;
                        if (successMsg) {
                            console.log(successMsg);
                            alert(successMsg);
                            location.reload();
                        } else {
                            const failMsg = response.fail;
                            alert(failMsg);
                            console.log(failMsg);
                        }
                    });
                })
            }

            // Delete Project
            const deleteProjectButtons = document.querySelectorAll('button.delete');
            for (const button of deleteProjectButtons) {
                button.addEventListener('click', () => {
                    const projectDiv = button.closest('div.project');
                    // const projectId = projectDiv.id;
                    const projectId = projectDiv.getAttribute('data-project-id');
                    const deleteProjectForm = new FormData();
                    deleteProjectForm.append('action', 'deleteProject');
                    deleteProjectForm.append('project-id', projectId);
                    fetch('/Goalify/Pages/user/workspaces/main/projects/projectHandler.php', {
                        method: 'POST',
                        body: deleteProjectForm
                    })
                    .then(response => response.json())
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

            // Start Project
            const startProjectButtons = document.querySelectorAll('button.start');
            for (const button of startProjectButtons) {
                button.addEventListener('click', () => {
                    const projectDiv = button.closest('div.project');
                    const projectId = projectDiv.getAttribute('data-project-id');
                    const startProjectForm = new FormData();
                    startProjectForm.append('action', 'startProject');
                    startProjectForm.append('project-id', projectId);
                    fetch('/Goalify/Pages/user/workspaces/main/projects/projectHandler.php', {
                        method: 'POST',
                        body: startProjectForm
                    })
                    .then(response => response.json())
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

            // Open Project
            const openProjectButtons = document.querySelectorAll('button.open');
            for (const button of openProjectButtons) {
                button.addEventListener('click', () => {
                    const projectDiv = button.closest('div.project');
                    // const projectId = projectDiv.id;
                    const projectId = projectDiv.getAttribute('data-project-id');
                    const openProjectForm = new FormData();
                    openProjectForm.append('action', 'openProject');
                    openProjectForm.append('project-id', projectId);
                    fetch('/Goalify/Pages/user/workspaces/main/projects/projectHandler.php', {
                        method: 'POST',
                        body: openProjectForm
                    })
                    .then(response => response.json())
                    .then(response => {
                        const successMsg = response.success;
                        if (successMsg) {
                            console.log(successMsg);
                            window.location.href = "/Goalify/Pages/user/workspaces/projects/description/projectDescription.php";
                        } else {
                            const failMsg = response.fail;
                            console.log(failMsg);
                        }
                    });
                })
            }
        })

    document.addEventListener('DOMContentLoaded', () => {
        const mediaQuery = window.matchMedia("(min-width: 541px)");

        // Set Initial State for Project Action Bar
        const setActionBar = (screenSize) => {
            const actionDropDownBar = document.querySelectorAll('.projects-list div div.project-actions');
            const projects = document.querySelectorAll('div.projects-list div.project');
            const root = document.documentElement;
            actionDropDownBar.forEach(bar => {
            if (screenSize.matches) {
                bar.classList.remove('close');
                projects.forEach(project => {
                    project.onclick = null;
                })
            } else {
                bar.classList.add('close');
                projects.forEach(project => {
                    const actionBar = project.querySelector('div.project-actions')
                    project.onclick =() => {
                        // toggle adds when does not exist and removes when exist
                        actionBar.classList.toggle('close');
                    };
                })
            }
        });
        }
        setActionBar(mediaQuery);

        mediaQuery.addEventListener('change', (e) => { // e is event object
            setActionBar(mediaQuery); // update the class list of the project action bar
        });
    })
</script>