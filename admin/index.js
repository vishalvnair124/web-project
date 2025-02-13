document.addEventListener("DOMContentLoaded", function() {
    const menuIcon = document.querySelector(".menuicn");
    const navContainer = document.querySelector(".navcontainer");

    menuIcon.addEventListener("click", () => {
        navContainer.classList.toggle("navclose");
    });

    // Function to get query parameter
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Load the page from the query parameter or default to the dashboard
    const page = getQueryParam('page') || 'dashboard.php';
    loadPage(page);

    // Handle navigation clicks
    document.querySelectorAll('.nav-option').forEach(option => {
        option.addEventListener('click', function(event) {
            // Check if the clicked option is not the logout option
            if (!this.classList.contains('logout')) {
                event.preventDefault(); // Prevent the default link behavior
                const page = this.getAttribute('data-page');
                loadPage(page);
                window.history.pushState(null, '', `?page=${page}`); // Update the URL without reloading the page
            } else {
                // Allow the logout option to proceed with the default link behavior
                window.location.href = this.getAttribute('data-page');
            }
        });
    });

    function loadPage(page) {
        $('#content').load(page, function(response, status, xhr) {
            if (status === "error") {
                console.error(`Error loading page: ${xhr.status} ${xhr.statusText}`);
            } else {
                startAnimations();
            }
        });

        // Highlight the selected navigation option
        document.querySelectorAll('.nav-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.getAttribute('data-page') === page) {
                opt.classList.add('selected');
            }
        });
    }

    function animateCount(element, start, end, duration, isPercentage = false) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            element.textContent = isPercentage ? `${currentValue}%` : currentValue;
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    }

    function startAnimations() {
        document.querySelectorAll('.box').forEach(box => {
            const countElement = box.querySelector('.topic-heading');
            const targetCount = parseInt(box.getAttribute('data-count'), 10);
            const isPercentage = countElement.id === 'completion-rate';
            animateCount(countElement, 0, targetCount, 2000, isPercentage);
        });
    }
});
