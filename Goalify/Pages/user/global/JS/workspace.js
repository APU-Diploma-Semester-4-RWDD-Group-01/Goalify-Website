document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const workspace = document.querySelector('#top-nav-desktop div.left ul li a#workspace-nav-desktop');
    const dropDown = document.querySelector('#top-nav-desktop div.left ul li a#workspace-nav-desktop span');
    const dropDownBar = document.querySelector('#top-nav-desktop div.left ul li ul.drop-down');
    const mediaQuery = window.matchMedia("(min-width: 1181px)");

    workspace.addEventListener('click', () => {
        const content = dropDown.textContent === 'keyboard_arrow_down'? 'keyboard_arrow_up': 'keyboard_arrow_down'
        dropDown.textContent = content;
        if (content === 'keyboard_arrow_down' && dropDownBar.classList.contains('active')) {
            dropDownBar.classList.remove('active')
        } else {
            dropDownBar.classList.add('active');
        }
    })

    const setWorkspaceNav = (screenSize) => {
        if (!screenSize.matches) {
            const workspaceMobileButton = document.querySelector('#bottom-nav-mobile ul li a#workspace-nav-mobile')
            workspaceMobileButton.addEventListener('click', () => {
                if (document.getElementById('workspace-drop-down')) {
                    window.removeOverlay('workspace-drop-down');
                }
                window.createSelectWorkspaceOverlay('workspace-drop-down', `
                <div id="workspace-drop-down-close-div"><span id="drop-down-close" class="material-symbols-rounded">close</span></div>
                <ul class="drop-down">
                    <li id="create-workspace-button-mobile"><span class="material-symbols-rounded">add_box</span>Create Workspace</li>
                    <hr>
                    <li id="select-workspace-button-mobile"><span class="material-symbols-rounded">arrow_selector_tool</span>Select Workspace</li>
                    <hr>
                    <li id="join-workspace-button-mobile"><span class="material-symbols-rounded">tag</span>Join Workspace</li>
                </ul>
                `)
                document.addEventListener("click", (event) => {
                    if (event.target.closest("#join-workspace-button-mobile")) {
                        console.log("Join Workspace Clicked");
                        joinWorkspace();
                    }
                
                    if (event.target.closest("#select-workspace-button-mobile")) {
                        console.log("Select Workspace Clicked");
                        selectWorkspace();
                    }
                
                    if (event.target.closest("#create-workspace-button-mobile")) {
                        console.log("Create Workspace Clicked");
                        createWorkspace();
                    }
                });

                const closeWorkspaceDropDown = document.getElementById('drop-down-close');
                closeWorkspaceDropDown.addEventListener('click', () => {
                    window.removeOverlay('workspace-drop-down');
                })
            })
        } else {
            const workspaceDropDownMobile = document.getElementById('workspace-drop-down');
            if (workspaceDropDownMobile) {
                window.removeOverlay('workspace-drop-down');
            }
            document.addEventListener("click", (event) => {
                if (event.target.closest("#join-workspace-button")) {
                    console.log("Join Workspace Clicked");
                    joinWorkspace();
                }
            
                if (event.target.closest("#select-workspace-button")) {
                    console.log("Select Workspace Clicked");
                    selectWorkspace();
                }
            
                if (event.target.closest("#create-workspace-button")) {
                    console.log("Create Workspace Clicked");
                    createWorkspace();
                }
            });
        }
    }

    setWorkspaceNav(mediaQuery);

    mediaQuery.addEventListener('change', (e) => { // e is event object
        setWorkspaceNav(mediaQuery); // update the class list of the side-nav
    });
})

function joinWorkspace() {
    const body = document.body;
    // if (!body.contains(document.querySelector('#overlay'))) {
        const workspaceDropDownMobile = document.getElementById('workspace-drop-down');
        if (workspaceDropDownMobile) {
            window.removeOverlay('workspace-drop-down');
        }
        const checkForm = document.getElementById('join-workspace-form');
        if (checkForm) {
            window.removeOverlay('join-workspace-form');
        }
        createOverlay(
            'join-workspace-form',
            `
            <label for="join-workspace-id">Enter Workspace Code</label>
            <input type="text" name="join-workspace-id" id="join-workspace-id" autofocus placeholder="#WSPC00001" autocomplete="off">
            <div class="button">
                <button type="button" id="close-join-workspace-form">Cancel</button>
                <button type="submit" id="confirm-join-workspace-form">Join</button>
            </div>
            `
        );

        const joinWorkspaceForm = document.querySelector('#join-workspace-form');
        joinWorkspaceForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const joinWorkspaceId = new FormData(joinWorkspaceForm);
            fetch('/Goalify/Pages/user/global/PHP/joinWorkspace.php', {
                method: 'POST',
                body: joinWorkspaceId
            })
            .then (response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                    removeOverlay('join-workspace-form');
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                }
            });
        })

        const closeWorkspaceForm = document.querySelector('#close-join-workspace-form');
        closeWorkspaceForm.addEventListener('click', () => {
            removeOverlay('join-workspace-form');
        })
    // }
}

async function selectWorkspace() {
    const body = document.body;
    const workspaceDropDownMobile = document.getElementById('workspace-drop-down');
    if (workspaceDropDownMobile) {
        window.removeOverlay('workspace-drop-down');
    }
    const checkForm = document.getElementById('select-workspace-div');
    if (checkForm) {
        window.removeOverlay('select-workspace-div');
    }
    window.createSelectWorkspaceOverlay('select-workspace-div',
        `
        <label for="new-project-name">Available Workspace</label>
        <div class="workspaces">
            <ul>
                <li id="loading">Loading...</li>
            </ul>
        </div>
        <div class="button">
            <button type="button" id="close-select-workspace-div">Cancel</button>
        </div>
        `
    )
    const selectWorkspaceDiv = document.getElementById('select-workspace-div');
    const workspaceUl = selectWorkspaceDiv.querySelector('div.workspaces ul');
    await loadWorkspace(workspaceUl);

    const allWorksapceLi = document.querySelectorAll('div.workspaces ul li');
    for (const li of allWorksapceLi) {
        if (li.hasAttribute('data-workspace-id')) {
            li.addEventListener('click', () => {
                const selectedWorkspaceId = new FormData;
                selectedWorkspaceId.append('workspace-id', li.getAttribute('data-workspace-id'));
                fetch('/Goalify/Pages/user/workspaces/main/projects/getProjects.php', {
                    method: 'POST',
                    body: selectedWorkspaceId
                })
                .then (response => response.json())
                .then(response => {
                    const successMsg = response.success;
                    if (successMsg) {
                        console.log(successMsg);
                        window.location.href = '/Goalify/Pages/user/workspaces/main/projects/projects.php';
                    } else {
                        const failMsg = response.fail;
                        console.log(failMsg);
                    }
                });
            })
        } else {
            li.style.cursor = 'default';
            li.addEventListener('mouseenter', () => {
                li.style.backgroundColor = '';
            })
        }
    }

    const closeSelectWorkspaceDiv = selectWorkspaceDiv.querySelector('#close-select-workspace-div');
    closeSelectWorkspaceDiv.addEventListener('click', () => {
        window.removeOverlay('select-workspace-div');
    })

    async function loadWorkspace(element) {
        const response = await fetch('/Goalify/Pages/user/global/PHP/loadWorkspace.php');
        const data = await response.json();
        const availableWorkspaces = data.availableWorkspaces;
        if (availableWorkspaces.length > 0) {
            element.innerHTML = ``;
            for (const workspace of availableWorkspaces) {
                const workspaceLi = document.createElement('li');
                workspaceLi.setAttribute('data-workspace-id', workspace['workspaceId']);
                workspaceLi.textContent = workspace['workspaceName'];
                workspaceHr = document.createElement('hr');
                element.appendChild(workspaceLi);
                element.appendChild(workspaceHr);
            }
        } else {
            const loadingLi = element.querySelector('#loading');
            loadingLi.textContent = 'No Available Workspace';
            workspaceHr = document.createElement('hr');
            element.appendChild(workspaceHr);
        }
    }
}

function createWorkspace() {
    const body = document.body;
    // if (!body.contains(document.querySelector('#overlay'))) {
        const workspaceDropDownMobile = document.getElementById('workspace-drop-down');
        if (workspaceDropDownMobile) {
            window.removeOverlay('workspace-drop-down');
        }
        const checkForm = document.querySelector('#create-workspace-form');
        if (checkForm) {
            window.removeOverlay('create-workspace-form');
        }
        createOverlay(
            'create-workspace-form',
            `
            <label for="new-workspace-name">New Workspace</label>
            <input type="text" name="new-workspace-name" id="new-workspace-name" autofocus placeholder="workspace_01" autocomplete="off">
            <div class="button">
                <button type="button" id="close-workspace-form">Cancel</button>
                <button type="submit" id="confirm-workspace-form">Create</button>
            </div>
            `
        );

        const createWorkspaceForm = document.querySelector('#create-workspace-form');
        createWorkspaceForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const newWorkspaceName = new FormData(createWorkspaceForm);
            fetch('/Goalify/Pages/user/global/PHP/createWorkspace.php', {
                method: 'POST',
                body: newWorkspaceName
            })
            .then (response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    alert(successMsg);
                    removeOverlay('create-workspace-form');
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                }
            });
        })

        const closeWorkspaceForm = document.querySelector('#close-workspace-form');
        closeWorkspaceForm.addEventListener('click', () => {
            removeOverlay('create-workspace-form');
        })
    // }
}