const images = document.querySelectorAll('.about-image');
const aboutSection = document.querySelector('#about');

images.forEach(image => {
    image.addEventListener('mouseover', () => {
        aboutSection.classList.add('grayscale');
        image.classList.remove('grayscale');
    });

    image.addEventListener('mouseout', () => {
        aboutSection.classList.remove('grayscale');
    });
});

