document.addEventListener('DOMContentLoaded', () => {
    // const editButtons = document.querySelectorAll('.actions .material-symbols-rounded .modify-user');
    const deleteButtons = document.querySelectorAll('span.delete-user');
    console.log(deleteButtons);
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.closest('div.user').getAttribute('data-user-id');
            fetch('PHP/deleteUser.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: "delete-user-id=" + encodeURIComponent(userId)
            })
            .then(response => response.json())
            .then(response => {
                if (response.delete) {
                    console.log(response.msg);
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.msg);
                }
            })
        })
    });
})