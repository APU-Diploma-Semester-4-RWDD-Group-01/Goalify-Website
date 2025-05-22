document.addEventListener('DOMContentLoaded', () => {
    // const createFolderButton = document.getElementById('create-folder-button');
    const uploadFileButton = document.getElementById('upload-file-button');

    
    uploadFileButton.addEventListener('click', () => {
        window.createOverlay(
            'upload-file-form',
            `
            <label for="new-file">Upload  File</label>
            <div class="file-upload">
                <label for="new-file" class="custom-file-upload">Choose File</label>
                <input type="file" name="new-file" id="new-file" onchange="updateFileName()">
                <span id="file-name">No file chosen</span>
            </div>

            <div class="button">
                <button type="button" id="close-upload-file-form" class="cancel-btn">Cancel</button>
                <button type="submit" id="confirm-upload-file-form" class="upload-btn">Upload</button>
            </div>
            `,
            'fileHandler.php'
        );

        const uploadFileForm = document.getElementById('upload-file-form');
        uploadFileForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const fileForm = new FormData(uploadFileForm);

            fetch('PHP/fileHandler.php', {
                method: 'POST',
                body: fileForm
            })
            .then(response => response.json()) // convert response to text
            .then(response => {
                const successMsg = response.success;
                console.log(successMsg);
                if (successMsg) {
                    alert(successMsg);
                    window.removeOverlay('upload-file-form');
                    document.dispatchEvent(new Event('LoadProjectFiles'));
                } else {
                    const failMsg = response.fail;
                    console.log(failMsg);
                    alert(failMsg);
                }
            })
            .catch(error => console.log('Error: ', error));
        })

        const closeUploadFileForm = document.getElementById('close-upload-file-form');
        closeUploadFileForm.addEventListener('click', () => {
            window.removeOverlay('upload-file-form');
        })
    })
})