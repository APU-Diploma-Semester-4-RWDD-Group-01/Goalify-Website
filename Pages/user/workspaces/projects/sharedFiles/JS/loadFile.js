document.addEventListener('DOMContentLoaded', loadProjectFiles);

document.addEventListener('LoadProjectFiles', async () => {
    const fileTableRows = document.querySelector('div.table-rows');
    const fileRows = fileTableRows.querySelector('div.rows');
    fileRows.replaceChildren();
    await loadProjectFiles();
})


async function loadProjectFiles() {
    const response = await fetch('PHP/loadProjectFiles.php');
    const data = await response.json();
    const fileTableRows = document.querySelector('div.table-rows');
    const fileRows = fileTableRows.querySelector('div.rows');

    if (data.projectFiles != null) {
        const allProjectFiles = data.projectFiles;

        for(const file of allProjectFiles) {
            const fileId = file['fileId'];
            const fileName = file['fileName'];
            // const fileType = file['fileType'];
            // const fileSize = file['fileSize'];
            // const fileData = file['fileData'];
            const uploadDateTime = file['uploadDateTime'];
            const formattedDate = new Date(uploadDateTime).toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                                                        day: '2-digit',
                                                                        month: 'long',
                                                                        year: 'numeric'
                                                                        });
            const userName = file['userName'];
            const rowContainer = document.createElement('div');
            rowContainer.classList.add('row-container');
            rowContainer.innerHTML = `
            <div class="row" data-file-id="${fileId}">
                <span class="col-1 s-col-1 material-symbols-rounded">draft</span>
                <div class="col-2 s-col-2 file-name">${fileName}</div>
                <div class="col-3 s-col-3"><span class="material-symbols-rounded">more_horiz</span></div>
                <div class="col-4 s-col-4 uploaded-by">${userName}</div>
                <div class="col-5 s-col-5 uploaded">${formattedDate}</div>
            </div>
            `
            fileRows.appendChild(rowContainer);
        }
        document.dispatchEvent(new Event('ProjectFilesLoaded'));
    } else {
        fileRows.innerHTML = `
        <div class="null-msg" style="height: 80%;">
            <span style="opacity: 0.7; font-style: italic;">No Project Files Available :D</span>
        </div>
        `;
    }
    
}