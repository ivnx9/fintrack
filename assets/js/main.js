// FINTRACK Main JavaScript File

document.addEventListener("DOMContentLoaded", function () {

    console.log("FINTRACK System Loaded");

    // Highlight active sidebar tab
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar-menu a');

    navLinks.forEach(function(link) {
        const linkPath = new URL(link.href).pathname;
        if (currentPath.endsWith(linkPath) || currentPath.includes(linkPath.replace('..', ''))) {
            link.classList.add('active');
        }
    });

    // Auto close alert messages
    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function(alert) {

        setTimeout(function() {

            alert.style.display = "none";

        }, 3000);

    });

});


// Confirm delete function
function confirmDelete() {

    return confirm("Are you sure you want to delete this record?");

}


// Simple search filter
function searchTable(inputId, tableId) {

    let input = document.getElementById(inputId);

    let filter = input.value.toLowerCase();

    let table = document.getElementById(tableId);

    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {

        let td = tr[i].getElementsByTagName("td");

        let found = false;

        for (let j = 0; j < td.length; j++) {

            if (td[j]) {

                let textValue = td[j].textContent || td[j].innerText;

                if (textValue.toLowerCase().indexOf(filter) > -1) {

                    found = true;

                    break;

                }

            }

        }

        tr[i].style.display = found ? "" : "none";

    }

}