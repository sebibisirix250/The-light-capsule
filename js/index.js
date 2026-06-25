//HOME PAGE IMAGE SLIDER 

document.addEventListener("DOMContentLoaded", () => {
  const sliderContainer = document.getElementById("bgSlider");

  //PATH TO IMAGES
  const imagePath = "assets/images/INDEX/";

  //FIRST IMAGE HARDCODED IN HTML, REST DOWNLOAD IN THE BACKGROUND - OPTIMIZATION
  const images = [
    "2.jpg",
    "3.jpg",
    "4.jpg",
    "5.jpg",
    "6.jpg",
    "7.jpg",
    "8.jpg",
    "9.jpg",
    "10.jpg",
  ];

  //BUILD AND INJECT THOSE REMAINING SLIDES
  images.forEach((imgSrc) => {
    const slideDiv = document.createElement("div");
    slideDiv.className = "slide";
    slideDiv.style.backgroundImage = `url('${imagePath}${imgSrc}')`;
    slideDiv.setAttribute("aria-hidden", "true");

    //ANTITHEFT OVERLAY ON TOP - SECURITY
    const overlay = document.querySelector(".anti-theft-overlay");
    sliderContainer.insertBefore(slideDiv, overlay);
  });

  //SELECT ALL SLIDES IN THE DOM (ALL 10)
  const slides = document.querySelectorAll(".slide");
  let currentSlideIndex = 0;

  //CROSSFADE
  setInterval(() => {
    //REMOVE ACTIVE CLASS FROM CURRENT SLIDE
    slides[currentSlideIndex].classList.remove("active");

    //CALCULATE NEXT SLIDE INDEX (LOOPS BACK TO FIRST ONE)
    currentSlideIndex = (currentSlideIndex + 1) % slides.length;

    //ADD ACTIVE CLASS FOR NEW ONE - TIMING 7.5 SECONDS
    slides[currentSlideIndex].classList.add("active");
  }, 7500);
});
