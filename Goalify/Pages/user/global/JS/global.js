/*
Author: Phang Shea Wen
Date: 2025/02/03
Filename: global.js
Description: globally used functions
*/

function createSelectWorkspaceOverlay(overlayContentId, overlayContent) {
    const body = document.body;
    const overlay = document.createElement('div');
    overlay.id = 'overlay';
    body.appendChild(overlay);

    const content = document.createElement('div');
    content.id = overlayContentId;
    content.innerHTML = overlayContent
    body.appendChild(content);
}

function createOverlay(formId, formContent, formAction) {
    const body = document.body;
    const overlay = document.createElement('div');
    overlay.id = 'overlay';
    body.appendChild(overlay);
    
    const formWrapper = document.createElement('div');
    formWrapper.classList.add('form-wrapper');

    const createForm = document.createElement('form');
    createForm.method = 'POST';
    createForm.action = formAction;
    createForm.id = formId;
    createForm.innerHTML = formContent;

    formWrapper.appendChild(createForm);
    body.appendChild(formWrapper);
}

function createOverlayHTML(formId, element, formAction) {
    const body = document.body;
    const overlay = document.createElement('div');
    overlay.id = 'overlay';
    body.appendChild(overlay);

    const createForm = document.createElement('form');
    createForm.method = 'POST';
    createForm.action = formAction;
    createForm.id = formId;
    createForm.appendChild(element);
    body.appendChild(createForm);
}

function removeOverlay(overlayId) {
    const body = document.body;
    const overlay = document.getElementById('overlay');
    const overlayContent = document.getElementById(overlayId);
    const formWrapper = document.querySelector('div.form-wrapper');
    body.removeChild(overlay);
    overlayContent.parentNode.removeChild(overlayContent);
    if (formWrapper) {
        body.removeChild(formWrapper);
    }
}

function createErrorOverlay(errorMsg) {
    const body = document.body;

    const overlay = document.createElement('div');
    overlay.id = 'overlay';
    body.appendChild(overlay);

    const createDiv = document.createElement('div');
    createDiv.id = 'error-overlay';

    const divHeading = document.createElement('div');
    divHeading.id = 'error-overlay-heading';

    const errorIcon = document.createElement('span');
    errorIcon.classList.add('material-symbols-rounded');
    errorIcon.textContent = 'warning';

    const errorHeading = document.createElement('p');
    errorHeading.textContent = 'W A R N I N G';

    divHeading.appendChild(errorIcon);
    divHeading.appendChild(errorHeading);

    const errorContent = document.createElement('p');
    errorContent.textContent = errorMsg;

    const closeErrorButton = document.createElement('button');
    closeErrorButton.id = 'close-error-overlay';
    closeErrorButton.textContent = 'Close';

    createDiv.appendChild(divHeading);
    createDiv.appendChild(errorContent);
    createDiv.appendChild(closeErrorButton)

    body.appendChild(createDiv);
}

// export {createOverlay, removeOverlay};
// attach function to global window object
window.createOverlay = createOverlay;
window.createOverlayHTML = createOverlayHTML;
window.removeOverlay = removeOverlay;
window.createErrorOverlay = createErrorOverlay;