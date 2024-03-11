<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>
    <style>
    .task {
        height: 100%;
        /* Set the height of the calendar container to 100% */
    }
    </style>
</head>

<body>
    <div id="header-container"></div>
    <div class="dashboard-container">
        <div class="reports-container">

            <div class="top-reports">
                <div class="report">
                    <h3>Clients</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Publishers</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Authors</h3>
                    <div class="amount"></div>
                </div>
                <div class="report">
                    <h3>Manufacturers</h3>
                    <div class="amount"></div>
                </div>
            </div>
        </div>

        <div class="task-panel">
            <!--Calendar-->
            <div class="task">
                <h3>Calendar</h3>
                <!-- <div id="calendar-container"></div> -->
            </div>
            <!--Pending tasks-->
            <div class="task">
                <h3>Pending tasks</h3>
            </div>
            <!--Notifications-->
            <h3>Notifications</h3>
            <div class="task"></div>
        </div>
    </div>

    <div id="footer-container"></div>

</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/Shared Components/header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });
    fetch('/Shared Components/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
    fetch('/Shared Components/calendar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('calendar-container').innerHTML = data;
        });
});
</script>

</html>