// Wait for the DOM to fully load before executing the script
document.addEventListener("DOMContentLoaded", function () {
  // Select the menu icon and navigation container elements
  const menuIcon = document.querySelector(".menuicn");
  const navContainer = document.querySelector(".navcontainer");

  // Toggle the navigation menu visibility when the menu icon is clicked
  menuIcon.addEventListener("click", () => {
    navContainer.classList.toggle("navclose");
  });

  // Function to get a query parameter value from the URL
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param); // Return the value of the specified parameter
  }

  // Load the page specified in the query parameter or default to 'dashboard.php'
  const page = getQueryParam("page") || "dashboard.php";
  loadPage(page);

  // Add click event listeners to all navigation options
  document.querySelectorAll(".nav-option").forEach((option) => {
    option.addEventListener("click", function (event) {
      // Check if the clicked option is not the logout option
      if (!this.classList.contains("logout")) {
        event.preventDefault(); // Prevent the default link behavior
        const page = this.getAttribute("data-page"); // Get the target page from the data attribute
        loadPage(page); // Load the target page
        window.history.pushState(null, "", `?page=${page}`); // Update the URL without reloading the page
      } else {
        // Allow the logout option to proceed with the default link behavior
        window.location.href = this.getAttribute("data-page");
      }
    });
  });

  // Function to load a page into the '#content' container
  function loadPage(page) {
    $("#content").load(page, function (response, status, xhr) {
      if (status === "error") {
        console.error(`Error loading page: ${xhr.status} ${xhr.statusText}`); // Log errors if the page fails to load
      } else {
        startAnimations(); // Start animations after the page loads successfully
      }
    });

    // Highlight the selected navigation option
    document.querySelectorAll(".nav-option").forEach((opt) => {
      opt.classList.remove("selected"); // Remove the 'selected' class from all options
      var optPage = opt.getAttribute("data-page"); // Get the page associated with the option
      //   console.log("page : " + extractLastWord(page));
      //   console.log("optPage : " + optPage);
      if (optPage === extractLastWord(page)) {
        opt.classList.add("selected"); // Add the 'selected' class to the current option
      }
    });
  }
  function extractLastWord(filename) {
    filename = filename.split("?")[0]; // Remove query parameters
    let parts = filename.split("_"); // Split by '_'
    if (parts.length > 1) {
      return parts.slice(-1)[0]; // Get the last part
    }
    return filename; // Return original if no '_'
  }

  // Function to animate a numeric count on an element
  function animateCount(element, start, end, duration, isPercentage = false) {
    let startTimestamp = null; // Initialize the start timestamp
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp; // Set the start timestamp on the first frame
      const progress = Math.min((timestamp - startTimestamp) / duration, 1); // Calculate progress as a fraction of the duration
      const currentValue = Math.floor(progress * (end - start) + start); // Calculate the current value
      element.textContent = isPercentage ? `${currentValue}%` : currentValue; // Update the element's text content
      if (progress < 1) window.requestAnimationFrame(step); // Continue the animation if not complete
    };
    window.requestAnimationFrame(step); // Start the animation
  }

  // Function to start animations for all elements with the 'box' class
  function startAnimations() {
    document.querySelectorAll(".box").forEach((box) => {
      const countElement = box.querySelector(".topic-heading"); // Find the element to animate
      const targetCount = parseInt(box.getAttribute("data-count"), 10); // Get the target count from the 'data-count' attribute
      const isPercentage = countElement.id === "completion-rate"; // Check if the count should be displayed as a percentage
      animateCount(countElement, 0, targetCount, 2000, isPercentage); // Animate the count
    });
  }
});
