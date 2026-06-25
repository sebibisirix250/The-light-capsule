//FOR SERVICE BOOKING FUCNTIONALITIES, CALENDAR, OPTIONS, PRICES

//OPTION PRICES LIBRARY
document.addEventListener("DOMContentLoaded", () => {
  const services = {
    individual: {
      basePrice: 100,
      options: { outsideCity: 50, multipleSessions: 100, fiftyPlusPhotos: 50 },
    },
    group: {
      basePrice: 200,
      options: {
        outsideCity: 50,
        multipleSessions: 100,
        fiftyPlusPhotos: 50,
        tenPlusPeople: 50,
      },
    },
    weddings: {
      basePrice: 400,
      options: {
        multipleLocations: 200,
        outsideCity: 100,
        premiumPackage: 250,
      },
    },
    automotive: {
      basePrice: 150,
      options: {
        outsideCity: 100,
        multipleSessions: 100,
        multipleLocations: 100,
        fiftyPlusPhotos: 50,
        premiumPackage: 250,
      },
    },
    realEstate: {
      basePrice: 150,
      options: { outsideCity: 100, multipleBuildings: 150 },
    },
    advertisement: {
      basePrice: 100,
      options: { multipleProducts: 100, wholeLinePackage: 200 },
    },
    baptism: {
      basePrice: 350,
      options: {
        multipleLocations: 150,
        outsideCity: 100,
        premiumPackage: 250,
      },
    },
    sports: {
      basePrice: 200,
      options: { multipleLocations: 50, outsideCity: 100, event: 200 },
    },
    events: {
      basePrice: 0,
      options: {
        multipleLocations: 50,
        outsideCity: 100,
        EighteenthBirthday: 400,
        parties: 250,
        concerts: 350,
        openings: 200,
      },
    },
    landscapes: { basePrice: 50, options: { multipleLocations: 50 } },
    wildlife: { basePrice: 150, options: { multipleLocations: 50 } },
    aerial: {
      basePrice: 250,
      options: {
        multipleLocations: 20,
        outsideCity: 100,
        specificPermitNeeded: 250,
      },
    },
    editing: {
      basePrice: 50,
      options: {
        HundredPhotos: 50,
        HundredFiftyPhotos: 100,
        TwoHundredPhotos: 150,
        TwoHundredFiftyPhotos: 200,
        ThreeHundredPhotos: 250,
      },
    },
  };

  //CURRENCY CHOICE
  const formatCurrency = new Intl.NumberFormat("ro-RO", {
    style: "currency",
    currency: "RON",
    maximumFractionDigits: 0,
  });

  //READ ACTIVE CATEGORY
  const serviceKey = document
    .getElementById("service-data")
    .getAttribute("data-service");

  //DOM SELECTIONS
  const addonsWrapper = document.getElementById("dynamic-addons"); //OPTION SPACE
  const liveTotalDisplay = document.getElementById("live-total"); //PRICE OUTPUT
  const hiddenOptionsInput = document.getElementById("hidden_options");

  //CALENDAR DOM SELECTIONS
  const monthYearDisplay = document.getElementById("month-year-display");
  const calendarGrid = document.getElementById("calendar-grid");
  const prevMonthBtn = document.getElementById("prev-month");
  const nextMonthBtn = document.getElementById("next-month");
  const hiddenDateInput = document.getElementById("hidden_date");
  const submitBtn = document.getElementById("submit-btn");

  //STATE DECLARATIONS
  let currentDate = new Date(); //TODAY
  let bookedDates = []; //BOOKED DATES FROM DATABASE
  let selectedDate = null; //SELECTED DAY FOR SERVICE

  //OPTION INITIALIZING BASED ON URL PARAMETERS 
  function initOptions() {
    if (!services[serviceKey]) return; //STOP IF NAMES DON'T MATCH PRICES

    const serviceData = services[serviceKey];
    const urlParams = new URLSearchParams(window.location.search); //OPTIONS SELECTED ?
    const preselectedKeys = urlParams.get("options") //IF YES SPLIT INTO A LIST
      ? urlParams.get("options").split(",")
      : [];

    //CHECKBOXES


    Object.entries(serviceData.options).forEach(([optKey, optPrice]) => { //LOOP EVERY OPTION OF SELECTED SERVICE
      const labelText = optKey
        .replace(/([A-Z])/g, " $1")
        .replace(/^./, (str) => str.toUpperCase()); //CLEAN RAW TEXT
      const isChecked = preselectedKeys.includes(optKey) ? "checked" : ""; //CHECK ALREADY SELECTED OPTIONS

      //HTML INJECTION FOR CHECKBOX
      const html = `
                <label class="checkbox-group">
                    <div class="checkbox-label-wrapper">
                        <input type="checkbox" class="addon-checkbox" value="${optPrice}" data-name="${labelText}" ${isChecked}>
                        <span>${labelText}</span>
                    </div>
                    <span class="option-price">+${formatCurrency.format(optPrice)}</span>
                </label>
            `;
      addonsWrapper.insertAdjacentHTML("beforeend", html);
    });

    //LISTENS TO CHANGES TO AUTOMATICALLY RECALCULATE BASED ON INPUTS
    document.querySelectorAll(".addon-checkbox").forEach((cb) => {
      cb.addEventListener("change", calculateLiveTotal);
    });
    calculateLiveTotal();
  }

  //PRICE UPDATE
  function calculateLiveTotal() {
    const serviceData = services[serviceKey];
    let total = serviceData.basePrice;
    let selectedNames = [];

    document.querySelectorAll(".addon-checkbox:checked").forEach((cb) => {
      total += parseFloat(cb.value);
      selectedNames.push(cb.getAttribute("data-name"));
    });

    liveTotalDisplay.textContent = formatCurrency.format(total);
    hiddenOptionsInput.value = selectedNames.join(", ");
  }

  //CALENDAR
  async function fetchBookedDates() {
        //URL GRAB 
        const baseUrl = document.getElementById('service-data').getAttribute('data-baseurl');
        
        try {
            const response = await fetch(`${baseUrl}/backend/handlers/get_booked_dates.php`); //ASK HANDLER FOR DATABASE REGSITRED BOOKED DATES
            
            //ERROR
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json(); //JSON TO JS DECODING

            if (data.status === 'success') {
                bookedDates = data.dates;    //SUCCES, SAVE BLOCKED DATES
            }

        } catch (error) {
            console.error("Failed to fetch booked dates.", error);
        }

        renderCalendar();
    }

  //BUILD CALENDAR
  function renderCalendar() {
    calendarGrid.innerHTML = "";
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDayIndex = new Date(year, month, 1).getDay();
    const lastDay = new Date(year, month + 1, 0).getDate();

    const monthNames = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

    for (let i = 0; i < firstDayIndex; i++) {
      calendarGrid.innerHTML += `<div></div>`;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 1; i <= lastDay; i++) {
      const loopDate = new Date(year, month, i);
      loopDate.setHours(0, 0, 0, 0);

      const dateString = `${year}-${String(month + 1).padStart(2, "0")}-${String(i).padStart(2, "0")}`;
      const isPast = loopDate < today;
      const isBooked = bookedDates.includes(dateString);
      const isSelected = selectedDate === dateString;

      let classes = "cal-day";
      if (isPast) classes += " past";
      if (isBooked) classes += " booked";
      if (isSelected) classes += " selected";

      const btnHTML = `<button type="button" class="${classes}" data-date="${dateString}" ${isPast || isBooked ? "disabled" : ""}>${i}</button>`;
      calendarGrid.innerHTML += btnHTML;
    }

    //CLICK LISTENER FOR AVLAIBLE DAYS
    document.querySelectorAll(".cal-day:not(.disabled)").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        document
          .querySelectorAll(".cal-day")
          .forEach((d) => d.classList.remove("selected"));
        e.target.classList.add("selected");
        selectedDate = e.target.getAttribute("data-date");
        hiddenDateInput.value = selectedDate;

        submitBtn.disabled = false;
        submitBtn.textContent = "Send booking request";
      });
    });
  }

  //CALENDAR UI BUTTONS
  prevMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  });

  nextMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  });

  initOptions();
  fetchBookedDates();
});
