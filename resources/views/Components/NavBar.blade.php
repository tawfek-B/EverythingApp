<style>
    .NavBar {
        width: 100%;
        background-color: #101010;
        height: 65px;
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-bottom: 2.5%;
        font-family: 'Just Another Hand';
        padding: 0 10px;
        position: relative;
    }

    .NavBarElement {
        height: 100%;
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
        font-size: 1.7rem;
        padding: 0 1rem;
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

    /* Mobile Menu Button */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 1.7rem;
        cursor: pointer;
        padding: 0 1rem;
        font-family: 'Just Another Hand';
        margin-left: auto;
    }

    /* Mobile Menu */
    .mobile-menu {
        display: none;
        position: absolute;
        top: 65px;
        right: 10px;
        background-color: #101010;
        z-index: 1000;
        flex-direction: column;
        padding: 10px 0;
        min-width: 200px;
        border-radius: 0 0 5px 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .mobile-menu a, .mobile-menu .mobile-logout {
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        font-size: 1.5rem;
        transition: 0.3s ease;
        white-space: nowrap;
        width:100%;
        text-align: center;
    }

    .mobile-menu a:hover, .mobile-menu .mobile-logout:hover {
        background-color: #6699CC;
        color: black;
    }

    .mobile-menu .nav-count {
        position: relative;
        display: inline-flex;
        top: 0;
        right: 0;
        margin-left: 5px;
    }

    .mobile-logout {
        background-color: #193E6C;
        border: none;
        text-align: left;
        font-family: 'Just Another Hand';
        cursor: pointer;
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

    @media (max-width: 992px) {
        .NavBarElement:last-child {
            display: none;
        }

        .mobile-menu-btn {
            display: block;
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

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">☰ Menu</button>

    <!-- Mobile Menu (hidden by default) -->
    <div class="mobile-menu" id="mobileMenu">
        @if (Auth::user()->privileges == 2)
            <a href="/universities" class="NavBarText">
                Universities
                <span class="nav-count">{{ App\Models\university::count() }}</span>
            </a>
            <a href="/subjects" class="NavBarText">
                Subjects
                <span class="nav-count">{{ App\Models\Subject::count() }}</span>
            </a>
            <a href="/lectures" class="NavBarText">
                Lectures
                <span class="nav-count">{{ App\Models\Lecture::count() }}</span>
            </a>
            <a href="/teachers" class="NavBarText">
                Teachers
                <span class="nav-count">{{ App\Models\Teacher::count() }}</span>
            </a>
            <a href="/users" class="NavBarText">
                Users
                <span class="nav-count">{{ App\Models\User::count() }}</span>
            </a>
            <a href="/admins" class="NavBarText">
                Admins
                <span class="nav-count">{{ App\Models\Admin::count() }}</span>
            </a>
        @elseif (Auth::user()->privileges == 1)
            <a href="/users" class="NavBarText">
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
            <a href="/subjects" class="NavBarText">
                Your Subjects
                <span class="nav-count">{{ $teacher->subjects->count() }}</span>
            </a>
            <a href="/lectures" class="NavBarText">
                Your Lectures
                <span class="nav-count">{{ $lecCount }}</span>
            </a>
        @endif

        <!-- Mobile Logout Button -->
        <form action="/logout" method="POST" class="mobile-logout-form">
            @csrf
            <button type="submit" class="mobile-logout" onclick="return confirmLogout()">
                Log Out
            </button>
        </form>
    </div>

    <!-- Original Desktop Navigation -->
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
        const currentPage = window.location.pathname;
        const links = document.querySelectorAll('.NavBarText');

        links.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    // Mobile menu toggle functionality
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.style.display = mobileMenu.style.display === 'flex' ? 'none' : 'flex';
        this.textContent = mobileMenu.style.display === 'flex' ? '✕ Close' : '☰ Menu';
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuBtn = document.getElementById('mobileMenuBtn');

        if (!mobileMenu.contains(event.target) && event.target !== menuBtn) {
            mobileMenu.style.display = 'none';
            menuBtn.textContent = '☰ Menu';
        }
    });

    // Call the function when the page loads
    window.onload = setActiveLink;
</script>