document.addEventListener('ProjectFilesLoaded', () => {
    // const uploadFile = document.getElementById('upload-file-button');
    const moreButton = document.querySelectorAll('div.col-3 span.material-symbols-rounded');

    moreButton.forEach(more => {
        more.addEventListener('click', () => {
            const col3 = more.closest('div.col-3');
            const row = col3.closest('div.row');
            const rowContainer = row.closest('.row-container');
            if (!rowContainer.classList.contains('active')) {
                // const rowContainer = more.closest('div.row-container');
                const moreDiv = document.createElement('div');
                if (row.hasAttribute("data-file-id")) {
                    // var content = `
                    // <ul>
                    //     <li>
                    //         <span class="material-symbols-rounded">download</span>
                    //         <p>Download</p>
                    //     </li>
                    //     <hr>
                    //     <li>
                    //         <span class="material-symbols-rounded">delete</span>
                    //         <p>Delete</p>
                    //     </li>
                    // </ul>
                    // `
                    moreDiv.classList.add('file-actions');
                    var content = `
                        <button class="download"><span class="material-symbols-rounded">download</span><p>Download</p></button>
                        <span class="divider"></span>
                        <button class="delete"><span class="material-symbols-rounded">delete</span><p>Delete</p></button>
                    `
                }
                // else {
                //     moreDiv.classList.add('folder-actions');
                //     // var content = `
                //     // <ul>
                //     //     <li>
                //     //         <span class="material-symbols-rounded">delete</span>
                //     //         <p>Delete</p>
                //     //     </li>
                //     // </ul>
                //     // `
                //     var content = `
                //         <button class="delete"><span class="material-symbols-rounded">delete</span><p>Delete</p></button>
                //     `
                // }
                // row.style.borderBottom = "1px solid transparent";
                moreDiv.innerHTML = content;
                rowContainer.appendChild(moreDiv);
                rowContainer.classList.add('active');

                const downloadButton = rowContainer.querySelector('button.download');
                downloadButton.addEventListener('click', () => {
                    const getFileId = row.getAttribute('data-file-id');
                    window.location.href = `PHP/downloadFile.php?downloadFileId=${getFileId}`;
                })

                const deleteButton = rowContainer.querySelector('button.delete');
                deleteButton.addEventListener('click', () => {
                    const getFileId = row.getAttribute('data-file-id');
                    fetch('PHP/fileHandler.php', {
                        method: 'POST',
                        header: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                            deleteFileId: getFileId,
                        })
                    })
                    .then(response => response.json()) // convert response to text
                    .then(response => {
                        const successMsg = response.success;
                        console.log(successMsg);
                        if (successMsg) {
                            alert(successMsg);
                            document.dispatchEvent(new Event('LoadProjectFiles'));
                        } else {
                            const failMsg = response.fail;
                            console.log(failMsg);
                            alert(failMsg);
                        }
                    })
                    .catch(error => console.log('Error: ', error));
                })
            } else {
                if (row.hasAttribute("data-file-id")) {
                    const moreDiv = rowContainer.querySelector('.file-actions');
                    const node = moreDiv.parentNode;
                    node.removeChild(moreDiv);
                    rowContainer.classList.remove('active');
                }
                // else {
                //     const moreDiv = rowContainer.querySelector('.folder-actions');
                //     const node = moreDiv.parentNode;
                //     node.removeChild(moreDiv);
                //     rowContainer.classList.remove('active');
                // }
                // row.style.borderBottom = "1px solid var(--header-color)";
            }
        })
    });
})