//CONTACT FORM MANAGEMENT

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const statusBox = document.getElementById("contactFormStatus");

  if (!form) return;

  //ATACHES EVENT TO SUBMIT BUTTON
  form.addEventListener("submit", function (event) {
    event.preventDefault();

    //LOADING LABEL
    if (statusBox) {
      statusBox.textContent = "Sending...";
    }

    //COLLECTS AND PACKS ALL INPUTED TEXT
    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData,
      headers: {
        Accept: "application/json",
      },
    })

      .then(async (response) => {
        const rawText = await response.text(); //READ ROW OUPTUT

        let data = null;

        try {
          data = JSON.parse(rawText); //TRANSLATE FOR JS TO READ
        } catch (e) {
          throw new Error("Server returned non-JSON response:\n" + rawText); //ERROR
        }

        if (!response.ok || !data.success) {
          throw new Error(data.message || "Failed to send message."); //IF ERROR, DISPLAY
        }

        if (statusBox) {
          statusBox.textContent = data.message || "Message sent successfully."; //IF SUCCESS, DISPLAY
        }

        form.reset(); //CLEAN FORM
      })
      //ERROR HANDLING
      .catch((error) => {
        console.error("Contact form error:", error);

        if (statusBox) {
          statusBox.textContent =
            error.message || "An error occurred. Please try again later.";
        }
      });
  });
});
