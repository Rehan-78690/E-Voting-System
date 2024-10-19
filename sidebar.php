
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar with Navbar and Footer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            padding: 10px 15px;
            display: block;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar.closed {
            left: -250px;
        }

        /* Sidebar close button */
        .closebtn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            color: white;
            font-size: 18px;
            border: none;
            cursor: pointer;
        }

        /* Content area */
        .content {
            flex: 1;
            padding: 20px;
            margin-left: 250px; /* Leave space for the sidebar */
            transition: all 0.3s ease;
        }

        .content.with-sidebar {
            margin-left: 250px; /* Adjust margin when sidebar is visible */
        }

        .content.no-sidebar {
            margin-left: 0; /* No margin when sidebar is collapsed */
        }

        /* Navbar styles */
        .navbar {
            background-color: grey;
            z-index: 1000;
            width: 100%;
        }

        .navbar-collapse {
            justify-content: space-between;
        }

        /* Footer styles */
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: auto;
        }

        /* Overlay styles for mobile */
        .overlay {
    position: fixed;
    top: 0;
    left: 250px; /* This ensures the overlay doesn't cover the sidebar */
    width: calc(100% - 250px); /* Make the overlay cover the remaining width, excluding the sidebar */
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
}

.overlay.active {
    display: block;
}

/* If the sidebar is closed, the overlay should cover the entire screen */
.sidebar.closed + .overlay {
    left: 0;
    width: 100%;
}
        /* Remove dimming when sidebar is closed */
        .no-overlay {
            background-color: transparent;
        }

        /* Responsive behavior */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px; /* Sidebar hidden by default on mobile */
            }

            .sidebar.open {
                left: 0;
            }

            .content {
                margin-left: 0; /* No margin on mobile when sidebar is hidden */
            }

            .navbar {
                margin-left: 0;
            }

            footer {
                font-size: 14px; /* Adjust font size on smaller screens */
                padding: 15px;
            }

            .overlay {
                display: none;
            }

            .overlay.active {
                display: block;
            }
        }

        @media (min-width: 769px) {
            /* On larger screens, no overlay should be shown */
            .overlay {
                display: none !important;
            }

            .content.with-sidebar {
                margin-left: 250px;
            }

            .content.no-sidebar {
                margin-left: 0;
            }

            .sidebar {
                left: 0;
            }

            .sidebar.closed {
                left: -250px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar (Open by default) -->
    <div class="sidebar open" id="sidebar">
        <h2>Main Menu</h2>
        <button class="closebtn" id="closeSidebar">&times;</button>
        <a href="#">🏠 Dashboard</a>
        <a href="#">👤 Profile Management</a>
        <a href="#">📄 Document Submission</a>
        <a href="#">📊 View Symbol</a>
        <a href="#">💬 Report Issues</a>
        <a href="#">🔑 Sign Out</a>
    </div>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0);" id="navbarToggle">☰</a> <!-- Sidebar toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Link
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled">Link</a>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="content with-sidebar" id="mainContent">
        <?php include 'verified_documents.php';?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
            <p>Designed for University of Poonch Rawalakot Elections</p>
        </div>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to handle the sidebar toggle and overlay -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const navbarToggle = document.getElementById('navbarToggle'); // Navbar ☰ button
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const mainContent = document.getElementById('mainContent');

        // Toggle sidebar and overlay on small screens via navbar button
        navbarToggle.addEventListener('click', function () {
    sidebar.classList.toggle('open');
    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('no-sidebar');

    // Ensure overlay is active only when sidebar is open
    if (sidebar.classList.contains('open') && window.innerWidth <= 768) {
        overlay.classList.add('active');
    } else {
        overlay.classList.remove('active');
    }
});


        // Close the sidebar via close button
        closeSidebarBtn.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            mainContent.classList.add('no-sidebar');
            overlay.classList.remove('active');
        });

        // Remove overlay if sidebar is closed
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            mainContent.classList.add('no-sidebar');
            overlay.classList.remove('active');
        });
    </script>

</body>
</html>
