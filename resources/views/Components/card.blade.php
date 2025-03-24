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
        transition: 0.3s ease;
        transform: translateY(-2px);
        /* Use transform for hover effect */
        align-items: center;
        text-decoration: none;
        position: relative;
        /* Create a stacking context */
        overflow: hidden;
        /* Prevent the circle from overflowing */
    }

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
        /* Ensure text is above the circle */
        width: 80%;
        padding: 4%;
    }

    .circle {
        position: absolute;
        /* Position relative to .Object */
        width: 450px;
        height: 450px;
        background-color: #193E6C;
        border-radius: 50%;
        pointer-events: none;
        transform: translate(-50%, -50%);
        opacity: 0;
        z-index: 1;
        /* Ensure the circle is behind the text and image */
    }

    .image-container {
        margin: 1%;
        width: 20%;
        position: relative;
        padding-top: 20%;
        overflow: hidden;
        flex-shrink: 0;
        z-index: 2;
        /* Ensure the image is above the circle */
    }

    .subject-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 2;
        /* Ensure the image is above the circle */
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

    /* Add this to your CSS */
    @keyframes fadeAndRise {
        0% {
            opacity: 1;
            transform: translateY(0);
        }

        100% {
            opacity: 0;
            transform: translateY(-10%);
            /* Adjust distance as needed */
        }
    }

    /* Class to trigger the animation */
    .Object.disappear {
        animation: fadeAndRise 0.05s ease forwards;
        /* 'forwards' keeps the final state */
        pointer-events: none;
        /* Disable clicks during animation */
    }
</style>

<a href="/{{ $link }}" class="Object"
    style="@if ($image == null) justify-content:center; text-align:center; @endif">
    @if ($image != null)
        <div class="image-container">
            <img src="{{ $image }}" alt="{{ $object }} image" class="subject-image">
        </div>
        <div style="width:1px; height:100%; background-color:#EBEDF2; margin-right:2%; margin-left:2%; z-index:2;">
        </div>
    @endif
    <div class="textContainer">{{ $slot }}</div>
    <div id="circle" class="circle"></div> <!-- Move the circle inside the .Object -->
</a>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.Object'); // Select all buttons

        buttons.forEach(button => {
            const circle = button.querySelector('.circle'); // Select the circle inside the button

            button.addEventListener('mousemove', (event) => {
                const buttonRect = button.getBoundingClientRect();
                const mouseX = event.clientX - buttonRect
                    .left; // Calculate mouse position relative to the button
                const mouseY = event.clientY - buttonRect.top;

                // Position the circle at the mouse cursor
                circle.style.left = `${mouseX}px`;
                circle.style.top = `${mouseY}px`;
                circle.style.opacity = '1';
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
    // Vanilla JS
    // Function to attach click animations to cards
    function bindDisappearAnimations() {
        document.querySelectorAll('.Object:not(.disappear)').forEach(card => {
            card.addEventListener('click', function() {
                this.classList.add('disappear');
                setTimeout(() => this.remove(), 100); // Remove after animation
            });
        });
    }

    // Initial binding
    bindDisappearAnimations();

    // Re-bind after filtering/searching (call this after DOM updates)
    function refreshAnimations() {
        bindDisappearAnimations(); // Re-attach to new/filtered cards
    }
</script>
