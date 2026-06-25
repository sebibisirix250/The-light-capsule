//FAQ INTERACTIONS - ABOUT PAGE

document.addEventListener("DOMContentLoaded", () => {
  const faqToggles = document.querySelectorAll(".faq-toggle"); //INFO GATHERING

  //EVENT FOR EACH QUESTION BUTTON
  faqToggles.forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      const currentToggle = e.currentTarget;  //QUESTION
      const answerWrapper = currentToggle.nextElementSibling; //ITS ANSWER

      //DETERMINE STATE
      const isExpanded = currentToggle.getAttribute("aria-expanded") === "true";

      //CLOSE CURRENT ACTIVE QUESTION IF ANOTHER BECOMES ACTIVE
      faqToggles.forEach((otherToggle) => {
        if (
          otherToggle !== currentToggle &&
          otherToggle.classList.contains("active")
        ) {
          otherToggle.classList.remove("active");
          otherToggle.setAttribute("aria-expanded", "false");
          otherToggle.nextElementSibling.style.maxHeight = null;
        }
      });

      //ACTUAL TOGGLE
      if (isExpanded) {
        //CLOSE
        currentToggle.classList.remove("active");
        currentToggle.setAttribute("aria-expanded", "false");
        answerWrapper.style.maxHeight = null;
      } else {
        //OPEN
        currentToggle.classList.add("active");
        currentToggle.setAttribute("aria-expanded", "true");
        //CALCULATE SIZE FOR ANSWER BOX
        answerWrapper.style.maxHeight = answerWrapper.scrollHeight + "px";
      }
    });
  });

  // WINDOW RESIZE
  window.addEventListener("resize", () => { //CHECK FOR RESIZES
    faqToggles.forEach((toggle) => {
      if (toggle.classList.contains("active")) {
        const answerWrapper = toggle.nextElementSibling;
        //INSTANT RECALCULATION
        answerWrapper.style.transition = "none";
        answerWrapper.style.maxHeight = answerWrapper.scrollHeight + "px";

        // RE-ENABLE TRANSITION
        setTimeout(() => {
          answerWrapper.style.transition = "";
        }, 10);
      }
    });
  });
});
