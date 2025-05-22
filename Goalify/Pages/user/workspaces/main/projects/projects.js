/*
Author: Phang Shea Wen
Date: 2025/01/29
Filename: projects.php in-line script
Description: To handle add new project pop-up window
*/

import { createOverlay, removeOverlay } from '/Goalify/Pages/user/global/JS/global.js';


document.addEventListener('DOMContentLoaded', () => {
    const addProjectButton = document.querySelector('.content .all-projects-section .search-add-projects #add-projects-button');
    const body = document.body;
    const formWrapper = document.createElement('div');
    formWrapper.id = 'form-wrapper';
    formWrapper.innerHTML = `
    
    `

    addProjectButton.addEventListener('click', () => {
        createOverlay(
            'new-project-form',
            `
            <div class="form-wrapper">
                <label for="new-project-name">New Project</label>
                <input type="text" name="new-project-name" id="new-project-name" autofocus placeholder="project_01" autocomplete="off">
                <div class="button">
                    <button type="button" id="close-new-project-form">Cancel</button>
                    <button type="submit" id="confirm-new-project-form">Create</button>
                </div>
            </div>
            `,
            ''
        );

        const newProjectForm = document.getElementById('new-project-form');
        newProjectForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const projectFormData = new FormData(newProjectForm);

            fetch('/Goalify/Pages/user/workspaces/main/projects/newProject.php', {
                method: 'POST',
                body: projectFormData
            })
            .then (response => response.json())
            .then(response => {
                const successMsg = response.success;
                if (successMsg) {
                    console.log(successMsg);
                    removeOverlay('new-project-form');
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                }
            });
        })

        const closeProjectForm = document.querySelector('#close-new-project-form');
        closeProjectForm.addEventListener('click', () => {
            removeOverlay('new-project-form');
        })
    })
})