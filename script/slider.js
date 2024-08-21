let slideIndex1 = 0;
const slides1 = document.querySelectorAll('.slide1');
const totalSlides1 = slides1.length;
let slideInterval1;

function showSlides1() {
    if (slideIndex1 >= totalSlides1) {
        slideIndex1 = 0;
    } else if (slideIndex1 < 0) {
        slideIndex1 = totalSlides1 - 1;
    }

    slides1.forEach((slide1, index1) => {
        if (index1 === slideIndex1) {
            slide1.style.display = 'block';
        } else {
            slide1.style.display = 'none';
        }
    });
}

function nextSlide1() {
    slideIndex1++;
    showSlides1();
}

function prevSlide1() {
    slideIndex1--;
    showSlides1();
}

function startSlider1() {
    slideInterval1 = setInterval(() => {
        nextSlide1();
    }, 3000); // Change slide every 3 seconds (3000 milliseconds)
}

function stopSlider1() {
    clearInterval(slideInterval1);
}

showSlides1(); // Display the initial slide
startSlider1(); // Start the slider automatically

