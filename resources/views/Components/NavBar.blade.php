<style>
    .NavBar {
        width: 100%;
        background-color: #101010;
        height: 60px;
        /* Set a fixed height for the navbar */
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-bottom: 2.5%;
        font-family: 'Just Another Hand';
        padding: 0 10px;
        /* Add horizontal padding */
    }

    .NavBarElement {
        height: 100%;
        /* Ensure it takes full height of the navbar */
        width: 50%;
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-left: 5%;
    }

    .NavBarElement:last-child {
        width: 50%;
        justify-content: flex-end;
        display: flex;
        flex-direction: row;
    }

    .NavBarText {
        width: 100%;
        height: 100%;
        /* Ensure it takes full height of the navbar */
        font-size: 1.7rem;
        /* Use relative units */
        /* width: auto; Allow width to adjust based on content */
        padding: 0 1rem;
        /* Use relative units */
        text-align: center;
        text-decoration: none;
        color: white;
        background-color: transparent;
        transition: 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .NavBarLogout {
        font-size: 1.5rem;
        padding: 0.5rem 1rem;
        width: 150%;
        text-align: center;
        text-decoration: none;
        color: white;
        background-color: #193E6C;
        transition: 0.3s ease;
        display: flex;
        height: inherit;
        align-items: center;
        font-family: 'Just Another Hand';
        cursor: pointer;
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .NavBarText:hover {
        background-color: #6699CC;
        color: black;
    }

    .NavBarLogout:hover {
        background-color: #6699CC;
        color: black;
    }

    .NavBarText.active {
        background-color: #6699CC;
        color: black;
    }

    .nav-count {
        margin-left: 5px;
        background-color: #193E6C;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 5px;
        right: 5px;
    }

    @media (max-width: 1200px) {
        .NavBarText {
            font-size: 1.5rem;
        }

        .NavBarLogout {
            font-size: 1rem;
        }

        .nav-count {
            font-size: 0.7rem;
            width: 1rem;
            height: 1rem;
        }
    }

    @media (max-width: 768px) {
        .NavBarText {
            font-size: 1.2rem;
            padding: 0 0.8rem;
        }

        .NavBarLogout {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }

        .nav-count {
            font-size: 0.6rem;
            width: 0.9rem;
            height: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .NavBarText {
            font-size: 1rem;
            padding: 0 0.6rem;
        }

        .NavBarLogout {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        .nav-count {
            font-size: 0.5rem;
            width: 0.8rem;
            height: 0.8rem;
        }

        .NavBarElement {
            margin-left: 2%;
        }

        .NavBarElement:last-child {
            width: 40%;
        }
    }
</style>

<nav class="NavBar">
    <div class="NavBarElement">
        <div style="width: 20%">
            <a href="/welcome"
                style="display: flex; flex-direction: row; text-decoration: none; color: white; font-size: 1.5rem; align-items: center;">
                <img src="{{ asset('Web/EVERYTHING1.png') }}" alt="ICON" style=" height: 100%; width:50%;">Everything
                App
            </a>
        </div>
    </div>
    @if (Auth::user()->privileges == 2)
        <div class="NavBarElement" style="margin-right: 5%;">
            <a href="/universities" class="NavBarText" id="universitiesLink">
                Universities
                <span class="nav-count">{{ App\Models\university::count() }}</span>
            </a>
            <a href="/subjects" class="NavBarText" id="subjectsLink">
                Subjects
                <span class="nav-count">{{ App\Models\Subject::count() }}</span>
            </a>
            <a href="/lectures" class="NavBarText" id="lecturesLink">
                Lectures
                <span class="nav-count">{{ App\Models\Lecture::count() }}</span>
            </a>
            <a href="/teachers" class="NavBarText" id="teachersLink">
                Teachers
                <span class="nav-count">{{ App\Models\Teacher::count() }}</span>
            </a>
            <a href="/users" class="NavBarText" id="usersLink">
                Users
                <span class="nav-count">{{ App\Models\User::count() }}</span>
            </a>
            <a href="/admins" class="NavBarText" id="adminsLink">
                Admins
                <span class="nav-count">{{ App\Models\Admin::count() }}</span>
            </a>
        @elseif (Auth::user()->privileges == 1)
            <div class="NavBarElement" style="margin-right: 5%;">
                <a href="/users" class="NavBarText" id="usersLink" style="width:7%;">
                    Users
                    <span class="nav-count">{{ App\Models\User::count() }}</span>
                </a>
            @elseif (Auth::user()->privileges == 0)
                @php
                    $teacher = App\Models\Teacher::findOrFail(Auth::user()->teacher_id);
                    $lecCount = 0;
                    foreach ($teacher->subjects as $subject) {
                        $lecCount += $subject->lectures->count();
                    }
                @endphp
                <div class="NavBarElement" style="margin-right: 5%;">
                    <a href="/subjects" class="NavBarText" id="subjectsLink" style="width:8%;">
                        Your Subjects
                        <span class="nav-count">{{ $teacher->subjects->count() }}</span>
                    </a>
                    <a href="/lectures" class="NavBarText" id="Link" style="width:8%;">
                        Your Lectures
                        <span class="nav-count">{{ $lecCount }}</span>
                    </a>
    @endif
    <form action="/logout" method="POST"
        style="cursor: pointer; padding: 0 0; height: 100%; margin-left: 10%; margin-right:5%;"
        onsubmit="return confirmLogout()">
        @csrf
        <button type="submit" id="adminsLink" class="NavBarLogout" style="">
            <div style="text-align:center">Log Out</div>
        </button>
    </form>
    </div>
</nav>

<script>
    function confirmLogout() {
        return confirm('Are you sure you want to log out?');
    }

    // Function to highlight the current page link
    function setActiveLink() {
        const currentPage = window.location.pathname; // Get the current page path
        const links = document.querySelectorAll('.NavBarText'); // Get all navigation links

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
