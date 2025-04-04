@props(['link' => '#', 'image' => null, 'object'])
<style>
    .Object {
        background: #193E6C;
        margin-top: 2%;
        font-size: 30px;
        border: #6699CC 4px solid;
        color: white;
        border-radius: 3px;
        display: flex;
        flex-direction: row;
        transition: all 0.3s ease;
        transform: translateY(-2px);
        align-items: center;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        width: 100%;
        /* Full width by default */
        max-width: 800px;
        /* Maximum width */
        margin-left: auto;
        margin-right: auto;
        overflow:hidden;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .Object {
            font-size: 24px;
            /* Smaller font size */
            flex-direction: column;
            /* Stack vertically */
            padding: 15px;
            /* Add some padding */
        }

        .textContainer {
            width: 100% !important;
            /* Full width text */
            padding: 15px !important;
            /* Adjust padding */
            text-align: center;
            /* Center text */
        }

        .image-container {
            width: 100% !important;
            /* Full width image */
            padding-top: 50% !important;
            /* Adjust aspect ratio */
            margin-bottom: 15px;
            /* Add space below image */
        }

        .Object:hover {
            transform: scale(1.02);
            /* Simpler hover effect on mobile */
            animation: none;
            /* Disable complex animation */
        }
    }

    @media (max-width: 480px) {
        .Object {
            font-size: 20px;
            /* Even smaller font */
            border-width: 3px;
            /* Thinner border */
        }
    }

    /* Rest of your existing styles... */
    .Object:hover {
        box-shadow: 0 0.25rem 0.25rem 0.1rem #121212;
        background-color: #6699CC;
        border: #6699CC 4px solid;
        border-radius: 10px;
        color: white;
        animation: hover ease 2s infinite;
    }

    .disable-hover .Object:hover {
        background: #193E6C;
        margin-top: 2%;
        font-size: 30px;
        border: #6699CC 4px solid;
        color: white;
        border-radius: 3px;
        display: flex;
        flex-direction: row;
        transition: 0.3s ease;
        align-items: center;
    }

    .textContainer {
        display: flex;
        flex-direction: column;
        line-height: 150%;
        z-index: 2;
        width: 80%;
        padding: 4%;
    }

    .circle {
        position: absolute;
        width: 450px;
        height: 450px;
        background-color: #193E6C;
        border-radius: 50%;
        pointer-events: none;
        transform: translate(-50%, -50%);
        opacity: 0;
        z-index: 1;
    }

    .image-container {
        margin: 1%;
        width: 20%;
        position: relative;
        padding-top: 20%;
        /* Maintain aspect ratio */
        overflow: hidden;
        flex-shrink: 0;
        z-index: 2;
        transition: all 0.3s ease;
        /* Smooth transitions */
    }

    /* Responsive image adjustments */
    @media (max-width: 992px) {
        .image-container {
            width: 25%;
            padding-top: 25%;
            /* Slightly larger on medium screens */
        }
    }

    @media (max-width: 768px) {
        .image-container {
            width: 40%;
            padding-top: 40%;
            /* Larger for tablets */
            margin-bottom: 10px;
        }
    }

    @media (max-width: 576px) {
        .image-container {
            width: 60%;
            padding-top: 60%;
            /* Even larger for mobile */
        }
    }

    @media (max-width: 480px) {
        .image-container {
            width: 80%;
            padding-top: 80%;
            /* Nearly full width for small phones */
        }
    }

    .subject-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 2;
    }

    @keyframes hover {
        0% {
            transform: translateY(5px);
        }

        50% {
            transform: translateY(-5px);
        }

        100% {
            transform: translateY(5px);
        }
    }

    /* Enhanced disappear animation */
    @keyframes fadeAndRise {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        70% {
            opacity: 1;
            transform: translateY(-40px) scale(1.05);
            /* Peak of rise */
        }

        100% {
            opacity: 0;
            transform: translateY(-80px) scale(0.95);
        }
    }

    .Object.disappear {
        animation: fadeAndRise 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        pointer-events: none;
    }
</style>

<a href="/{{ $link }}" class="Object"
    style="@if ($image == null) justify-content:center; text-align:center; @endif">
    @if ($image != null)
        <div class="image-container">
            <img src="{{ $image }}" alt="{{ $object }} image" class="subject-image">
        </div>
        <div style="width:1px; height:100%; background-color:#EBEDF2; margin-right:2%; margin-left:2%; z-index:2;"></div>
    @endif
    <div class="textContainer">{{ $slot }}</div>
    <div id="circle" class="circle"></div>
</a>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.Object');

        buttons.forEach(button => {

            const circle = button.querySelector('.circle');

            button.addEventListener('mousemove', (event) => {
                if (window.innerWidth > 768) { // Only apply on larger screens
                    const buttonRect = button.getBoundingClientRect();
                    const mouseX = event.clientX - buttonRect.left;
                    const mouseY = event.clientY - buttonRect.top;

                    circle.style.left = `${mouseX}px`;
                    circle.style.top = `${mouseY}px`;
                    circle.style.opacity = '1';
                }
            });

            button.addEventListener('mouseleave', () => {
                circle.style.opacity = '0';
            });

            document.addEventListener('scroll', () => {
                circle.style.opacity = '0';
            });

            let scrollTimer;
            document.addEventListener('scroll', () => {
                document.body.classList.add('disable-hover');
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    document.body.classList.remove('disable-hover');
                }, 200);
            });
        });
    });
</script>
<script>
    function bindDisappearAnimations() {
        document.querySelectorAll('.Object:not(.disappear)').forEach(card => {
            card.addEventListener('click', function() {
                this.classList.add('disappear');
            });
        });
    }

    // Initial binding
    bindDisappearAnimations();

    // Re-bind after filtering/searching (call this after DOM updates)
    function refreshAnimations() {
        bindDisappearAnimations(); // Re-attach to new/filtered cards
    }

    function refreshAnimations() {
        bindDisappearAnimations(); // Re-attach to new/filtered cards
    }
</script>
