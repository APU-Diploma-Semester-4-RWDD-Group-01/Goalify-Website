document.addEventListener("DOMContentLoaded", () => {
    const top_bar_pic = document.querySelector(".profile-pic img");

    fetch('/Goalify/Pages/user/profile/profileHandler.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error:", data.error);
        } else {
            if (data.user && data.user.profile_img) {
                // top_bar_pic.src = `/Goalify/Pages/user/profile/${data.user.profile_img}`;
                top_bar_pic.src = `/Goalify/Pages/user/profile/${data.user.profile_img}`;
            } else {
                top_bar_pic.src = "/Goalify/Img/default_profile.png";
            }
        }
    })
    .catch(error => console.error("Fetch Error:", error));
});