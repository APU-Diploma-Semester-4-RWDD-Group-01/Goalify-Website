<?php
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';

$allUsers = getAllUsers($pdo);
$searchUsers = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search-users-input'])) {
    $keyword = $_POST['search-users-input'];
    $searchUsers = searchUserByKeyWord($pdo, $keyword);
}
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const allUsers = <?php echo json_encode($allUsers); ?>;
    const searchUsers = <?php echo json_encode($searchUsers); ?>;
    var totalPages;
    if (searchUsers) {
        totalPages = Math.ceil(searchUsers.length / 10);
        loadSearchUsers();
    } else {
        totalPages = Math.ceil(allUsers.length / 10);
        loadAllUsers();
    }
    element(totalPages, 1);
})

function loadAllUsers() {
    const allUsers = <?php echo json_encode($allUsers); ?>;
    var totalPages = Math.ceil(allUsers.length / 10);
    const pageDiv = document.querySelector('.content .user-list');
    var page = pageDiv.getAttribute('data-page');
    const listUsers = document.querySelector('.content .user-list');
    listUsers.replaceChildren();
    const numUsers = document.querySelector('#num-users');
    if (Array.isArray(allUsers)) {
        var j = page == 1 ? 0 : 10 * (page - 1);
        for (x = j; x < j + 10 && x < allUsers.length; x++) {
            userDiv = document.createElement('div');
            const userId = allUsers[x]['userId'];
            userDiv.setAttribute('data-user-id', userId);
            userDiv.classList.add('user')
            const img = allUsers[x]['profile_img'];
            if (img == null) {
                imgPath = '/Goalify/Img/default_profile.png'
            } else {
                imgPath = `/Goalify/Pages/user/profile/${allUsers[x]['profile_img']}`;
            }
            userDivContent = `
            <div class="profile-img"><img src="${imgPath}" alt="user-profile"></div>
            <span class="name">${allUsers[x]['name']}</span>
            <div class="actions">
                <a href="modifyUser/modifyUser.php?userId=${userId.replace("#", "%23")}" class="material-symbols-rounded modify-user">edit</a>

                <span class="material-symbols-rounded delete-user">delete</span>
            </div>
            `;
            userDiv.innerHTML = userDivContent;
            listUsers.appendChild(userDiv);
        }
        numUsers.textContent = `( ${allUsers.length} )`;
    } else {
        listUsers.innerHTML = `
        <div class="null-msg" style="height: 80%;">
            <span style="opacity: 0.7; font-style: italic;">No Users Available :D</span>
        </div>
        `;
    }
}

function loadSearchUsers() {
    const searchUsers = <?php echo json_encode($searchUsers); ?>;
    var totalPages = Math.ceil(searchUsers.length / 10);
    const pageDiv = document.querySelector('.content .user-list');
    var page = pageDiv.getAttribute('data-page');
    const listUsers = document.querySelector('.content .user-list');
    listUsers.replaceChildren();
    const numUsers = document.querySelector('#num-users');
    if (Array.isArray(searchUsers)) {
        var j = page == 1 ? 0 : 10 * (page - 1);
        for (x = j; x < j + 10 && x < searchUsers.length; x++) {
            userDiv = document.createElement('div');
            const userId = searchUsers[x]['userId'];
            userDiv.setAttribute('data-user-id', userId);
            userDiv.classList.add('user')
            const img = searchUsers[x]['profile_img'];
            if (img == null) {
                imgPath = '/Goalify/Img/default_profile.png'
            } else {
                imgPath = `/Goalify/Pages/user/profile/${searchUsers[x]['profile_img']}`;
            }
            userDivContent = `
            <div class="profile-img"><img src="${imgPath}" alt="user-profile"></div>
            <span class="name">${searchUsers[x]['name']}</span>
            <div class="actions">
                <a href="modifyUser/modifyUser.php?userId=${userId.replace("#", "%23")}" class="material-symbols-rounded modify-user">edit</a>
                
                <span class="material-symbols-rounded delete-user">delete</span>
            </div>
            `;
            userDiv.innerHTML = userDivContent;
            listUsers.appendChild(userDiv);
        }
        numUsers.textContent = `( ${searchUsers.length} )`;
    } else {
        listUsers.innerHTML = `
        <div class="null-msg" style="height: 80%;">
            <span style="opacity: 0.7; font-style: italic;">No Users Found :D</span>
        </div>
        `;
    }
}

function element(totalPages, page) {
    const pagingUl = document.querySelector('.pagination ul');
    // page id attribute
    var pageDiv = document.querySelector('.user-list');
    pageDiv.setAttribute('data-page', page);

    const searchUsers = <?php echo json_encode($searchUsers); ?>;
    if (searchUsers) {
        loadSearchUsers();
    } else {
        loadAllUsers();
    }

    let pagingLi = '';
    let activeLi;
    let beforePages = page == 1? 1 : page - 1;
    let afterPages = page == totalPages ? totalPages : page + 1;
    // let beforePages = page - 1;
    // let afterPages = page + 1;

    if (page == totalPages && page > 4) { // if page = total pages, show two more before pages
        beforePages = beforePages - 2; // show 3 before pages (7, 8, 9) e.g. for 10
    } else if (page == totalPages - 1 && page > 3) { // if page = total pages - 1, show one more before pages
        beforePages = beforePages - 1; // show 2 before pages (7, 8) e.g. for 10, (page = 10 -1 = 9)
    }
    if (page == 1 && page !== totalPages && page <= totalPages - 3) { // if page = 1, show 2 more after pages
        afterPages = afterPages + 2; // show 3 after pages (2, 3, 4)
    } else if (page == 2 && page !== totalPages && page <= totalPages - 2) { // if page = 2. show 1 more after page
        afterPages = afterPages + 1; // show 2 after pages, (3, 4)
    }


    if (page > 1) {
        pagingLi += `<li class="button previous" onclick="element(${totalPages}, ${page - 1})"><span class="material-symbols-rounded">chevron_left</span></li>`;
    }
    if (page > 2) { // if page value > 2, add new li tag with 1 value
        pagingLi += `<li class="num" onclick="element(${totalPages}, 1)"><span>1</span></li>`;
        if (page > 3) { // if page value > 3, add new li tag with ...
            pagingLi += `<li class="dots"><span>...</span></li>`;
        }
    }
    for(let pageLength = beforePages; pageLength <= afterPages && pageLength <= totalPages; pageLength++) {
        if (pageLength == 0) {
            pageLength = pageLength + 1;
        }
        console.log(pageLength);
        activeLi = page == pageLength? "active" : "";
        pagingLi += `<li class="num ${activeLi}" onclick="element(${totalPages}, ${pageLength})"><span>${pageLength}</span></li>`;
    }
    if (page < totalPages - 1 && totalPages > 4) { // if page value < totalPages - 1, show last page (li)
        if (page < totalPages - 2) { // if page value < totalPages - 2, add new li tag with last ... before last page
            pagingLi += `<li class="dots"><span>...</span></li>`;
        }
        pagingLi += `<li class="num" onclick="element(${totalPages}, ${totalPages})"><span>${totalPages}</span></li>`;
    }
    if (page < totalPages) {
        pagingLi += `<li class="button next" onclick="element(${totalPages}, ${page + 1})"><span class="material-symbols-rounded">chevron_right</span></li>`;
    }
    pagingUl.innerHTML = pagingLi;
}
</script>