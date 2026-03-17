'use strict';


/*
*navbar toggle in mobile;
*/

const /** {NodeElement} */$navbar = document.querySelector("[data-navbar]");
const /** {NodeElement} */$navToggler = document.querySelector("[data-nav-toggler]");

$navToggler.addEventListener("click", () => $navbar.classList.toggle("active"));


/*
*Header scroll state
*/

const /** {NodeElement} */ $header = document.querySelector("[data-header]");

window.addEventListener("scroll", e => {
    $header.classList[window.scrollY > 50 ? "add" : "remove"]("active");
});


/*
*Add to favourite button toggle
*/
const /** {NodeList} */ $toggleBtns = document.querySelectorAll("[data-toggle-btn]");

$toggleBtns.forEach($toggleBtn => {
    $toggleBtn.addEventListener("click", () => {
    $toggleBtn.classList.toggle("active");
 });
});



















/*
*Selling page
*/
document.addEventListener("DOMContentLoaded", () => {
    const categorySelect = document.getElementById("category");
    const imagePopup = document.getElementById("imagePopup");
    const closePopup = document.getElementById("closePopup");
    const imageUpload = document.getElementById("imageUpload");
    const preview = document.getElementById("preview");

    // Show popup when selecting a category
    categorySelect.addEventListener("change", () => {
        if (categorySelect.value) {
            imagePopup.style.display = "block";
        }
    });

    // Close popup
    closePopup.addEventListener("click", () => {
        imagePopup.style.display = "none";
    });

    // Handle image preview
    imageUpload.addEventListener("change", function () {
        preview.innerHTML = ""; // Clear previous previews
        const files = imageUpload.files;

        if (files.length < 2) {
            alert("Please upload at least 2 images.");
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                preview.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    });

    // Handle form submission
    document.getElementById("adForm").addEventListener("submit", function (e) {
        e.preventDefault();
        alert("Ad Posted Successfully!");
    });
});