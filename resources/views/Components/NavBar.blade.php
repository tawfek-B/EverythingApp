<style>
    .NavBar {
        width: 100%;
        background-color: #101010;
        min-height: 50px;
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-bottom: 2.5%;
    }

    .NavBarElement {
        margin-left: 5%;
        height: 100%;
        width: 80%;
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .NavBarElement:last-child {
        width: 50%;
        /* Allow the second headerElement to grow as needed */
        justify-content: flex-end;
        /* Align links to the right */
        display: flex;
        flex-direction: row;
    }

    .NavBarText {
        font-size: 20px;
        height: 100%;
        /* Ensure the height matches the header */
        padding: 0 15px;
        /* Add padding for better spacing */
        text-align: center;
        text-decoration: none;
        color: white;
        background-color: transparent;
        transition: 0.3s ease;
        display: flex;
        align-items: center;
        /* Vertically center the text */
    }

    .NavBarText:hover {
        background-color: #6699CC;
        color: black;
    }

    .NavBarText.active {
        background-color: #6699CC;
        /* Highlight the current page */
        color: black;
    }
</style>
<nav class="NavBar">
    <div class="NavBarElement">
        @if (session('teacher') != null)
        <a href="/teacher/welcome"
            style="display:flex; flex-direction:row;text-decoration: none; color:white;font-size:20px;align-items:center;">
            <img src="{{ asset('Web/EVERYTHING1.png') }}" alt="ICON" style="width:8%; height:80%">Everything App
        </a>
        @elseif(session('admin')!=null)
        <a href="/welcomeAdmin"
            style="display:flex; flex-direction:row;text-decoration: none; color:white;font-size:20px;align-items:center;">
            <img src="{{ asset('Web/EVERYTHING1.png') }}" alt="ICON" style="width:8%; height:80%">Everything App
        </a>
        @endif
    </div>
    <div class="NavBarElement" style="margin-right:5%;">
        <a href="/teachers" class="NavBarText" id="teachersLink">Teachers</a>
        <a href="/subjects" class="NavBarText" id="subjectsLink">Subjects</a>
        <a href="/users" class="NavBarText" id="usersLink">Users</a>
        <a href="/admins" class="NavBarText" id="adminsLink">Admins</a>
    </div>
</nav>

<script>
    // Function to highlight the current page link
    function setActiveLink() {
        const currentPage = window.location.pathname; // Get the current page path
        const links = document.querySelectorAll('.headerText'); // Get all navigation links

        links.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active'); // Add the 'active' class to the current page link
            } else {
                link.classList.remove('active'); // Remove 'active' class from other links
            }
        });
    }

    // Call the function when the page loads
    window.onload = setActiveLink;
</script>
