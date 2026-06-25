//PRICING CALCULATOR & BOOKING DATA TRANSFER

document.addEventListener("DOMContentLoaded", () => {
  
  //DATA LIBRARY
  const services = {
    individual: {
      name: "Individual Session",
      basePrice: 100,
      desc: "Individual photography session with options for additional services.",
      options: { outsideCity: 50, multipleSessions: 100, fiftyPlusPhotos: 50 },
    },
    group: {
      name: "Group Session",
      basePrice: 200,
      desc: "Group photography session with options for additional services.",
      options: {
        outsideCity: 50,
        multipleSessions: 100,
        fiftyPlusPhotos: 50,
        tenPlusPeople: 50,
      },
    },
    weddings: {
      name: "Weddings",
      basePrice: 400,
      desc: "Wedding photography with various options including premium packages.",
      options: {
        multipleLocations: 200,
        outsideCity: 100,
        premiumPackage: 250,
      },
    },
    automotive: {
      name: "Automotive",
      basePrice: 150,
      desc: "Automotive photography with various options including premium packages.",
      options: {
        outsideCity: 100,
        multipleSessions: 100,
        multipleLocations: 100,
        fiftyPlusPhotos: 50,
        premiumPackage: 250,
      },
    },
    realEstate: {
      name: "Real Estate",
      basePrice: 150,
      desc: "Real estate photography with options for multiple buildings.",
      options: { outsideCity: 100, multipleBuildings: 150 },
    },
    advertisement: {
      name: "Advertising",
      basePrice: 100,
      desc: "Advertisement photography with options for product packages.",
      options: { multipleProducts: 100, wholeLinePackage: 200 },
    },
    baptism: {
      name: "Baptism",
      basePrice: 350,
      desc: "Baptism photography with options for multiple locations and premium packages.",
      options: {
        multipleLocations: 150,
        outsideCity: 100,
        premiumPackage: 250,
      },
    },
    sports: {
      name: "Sports",
      basePrice: 200,
      desc: "Sports photography with options for multiple locations and events.",
      options: { multipleLocations: 50, outsideCity: 100, event: 200 },
    },
    events: {
      name: "Events",
      basePrice: 0,
      desc: "Events photography with options for multiple locations and types, including birthday parties, concerts, and openings.",
      options: {
        multipleLocations: 50,
        outsideCity: 100,
        EighteenthBirthday: 400,
        parties: 250,
        concerts: 350,
        openings: 200,
      },
    },
    landscapes: {
      name: "Landscapes",
      basePrice: 50,
      desc: "Landscapes photography with options for various locations.",
      options: { multipleLocations: 50 },
    },
    wildlife: {
      name: "Wildlife",
      basePrice: 150,
      desc: "Wildlife photography with options for various locations.",
      options: { multipleLocations: 50 },
    },
    aerial: {
      name: "Aerial Photography",
      basePrice: 250,
      desc: "Aerial photography with options for multiple locations and specific permits.",
      options: {
        multipleLocations: 20,
        outsideCity: 100,
        specificPermitNeeded: 250,
      },
    },
    editing: {
      name: "Editing Services",
      basePrice: 50,
      desc: "Editing price per set of photos, with pricing for different quantities.",
      options: {
        HundredPhotos: 50,
        HundredFiftyPhotos: 100,
        TwoHundredPhotos: 150,
        TwoHundredFiftyPhotos: 200,
        ThreeHundredPhotos: 250,
      },
    },
  };

  //DOM ELEMENTS
  const buttons = document.querySelectorAll(".service-button");
  const placeholder = document.getElementById("pricing-placeholder");
  const contentWrapper = document.getElementById("price-content-wrapper");
  const headerContainer = document.getElementById("price-header");
  const optionsForm = document.getElementById("options-form");
  const totalPriceDisplay = document.getElementById("total-price-display");
  const descriptionDisplay = document.getElementById("service-description");
  const bookPackageBtn = document.getElementById("book-package-btn");

  //CURRENCY CHOICE
  const formatCurrency = new Intl.NumberFormat("ro-RO", {
    style: "currency",
    currency: "RON",
    maximumFractionDigits: 0,
  });

  let currentActiveService = null;

  //LINK READER
  const urlParams = new URLSearchParams(window.location.search);
  const initialService = urlParams.get("service");
  if (initialService && services[initialService]) {
    loadServiceData(initialService);
  }

  //EVENT LISTENER FOR BUTTONS
  buttons.forEach((button) => {
    button.addEventListener("click", (e) => {
      
      //TARGET BUTTONS
      const serviceKey = e.currentTarget.getAttribute("data-service");
      if (serviceKey && services[serviceKey]) {
        loadServiceData(serviceKey);

        //UPDATE URL WITHOUT RELOADING
        window.history.pushState(null, "", `?service=${serviceKey}`);
      }
    });
  });

  optionsForm.addEventListener("change", calculateTotal);

  //TRANSFER STATE TO BOOKING PAGE
  if (bookPackageBtn) {
    bookPackageBtn.addEventListener("click", () => {
      if (!currentActiveService) return;

      //TARGET CHECKED BOXES
      const checkedBoxes = optionsForm.querySelectorAll(
        'input[type="checkbox"]:checked',
      );

      //EXTRACT RAW KEYS
      const optionKeys = Array.from(checkedBoxes).map((box) =>
        box.id.replace("opt_", ""),
      );

      //BUILD QUERY PARAMETERS
      const queryParams = new URLSearchParams();
      queryParams.append("service", currentActiveService);

      if (optionKeys.length > 0) {
        queryParams.append("options", optionKeys.join(","));
      }

      //REDIRECT THEM TO BOOKING PAGE
      window.location.href = `service_request.php?${queryParams.toString()}`;
    });
  }

  //INTERFACE BUILDING 
  function loadServiceData(serviceKey) {
    currentActiveService = serviceKey;
    const data = services[serviceKey];

    // UPDATE ACTIVE BUTTONS STATE
    buttons.forEach((btn) => btn.classList.remove("active"));
    const activeBtn = document.querySelector(
      `.service-button[data-service="${serviceKey}"]`,
    );
    if (activeBtn) activeBtn.classList.add("active");

    //SWAP UI STATES
    placeholder.classList.add("hidden");
    contentWrapper.classList.remove("hidden");

    //SET HEADER USING TEXTCONTENT (PREVENT XSS) - SECURITY
    headerContainer.innerHTML = "";
    const titleEl = document.createElement("h2");
    titleEl.textContent = data.name;

    const basePriceEl = document.createElement("div");
    basePriceEl.className = "base-price";
    basePriceEl.textContent = `Base Package: ${formatCurrency.format(data.basePrice)}`;

    headerContainer.appendChild(titleEl);
    headerContainer.appendChild(basePriceEl);

    //GENERATE OPTIONS
    optionsForm.innerHTML = "";

    Object.entries(data.options).forEach(([optKey, optPrice]) => {
      //FORMAT TO NORMAL CASE
      const labelText = optKey
        .replace(/([A-Z])/g, " $1")
        .replace(/^./, (str) => str.toUpperCase());

      //BUILD DOM NODES
      const labelWrapper = document.createElement("label");
      labelWrapper.className = "checkbox-group";

      const inputWrapper = document.createElement("div");
      inputWrapper.className = "checkbox-label-wrapper";

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = optPrice;
      checkbox.id = `opt_${optKey}`; //SET ID FOR EXTRACTION

      const textSpan = document.createElement("span");
      textSpan.textContent = labelText;

      inputWrapper.appendChild(checkbox);
      inputWrapper.appendChild(textSpan);

      const priceSpan = document.createElement("span");
      priceSpan.className = "option-price";
      priceSpan.textContent = `+${formatCurrency.format(optPrice)}`;

      labelWrapper.appendChild(inputWrapper);
      labelWrapper.appendChild(priceSpan);

      optionsForm.appendChild(labelWrapper);
    });

    //SET DESCRIPTION TEXT
    descriptionDisplay.textContent = data.desc;

    //RUN INITIAL CALCULATIONS
    calculateTotal();
  }

  //PRICING CALCULATIONS BASED ON CHECKBOXES STATE
  function calculateTotal() {
    if (!currentActiveService) return;

    const basePrice = services[currentActiveService].basePrice;
    let addonsTotal = 0;
    const checkedOptions = optionsForm.querySelectorAll(
      'input[type="checkbox"]:checked',
    );
    checkedOptions.forEach((input) => {
      addonsTotal += parseFloat(input.value);
    });

    const finalPrice = basePrice + addonsTotal;
    totalPriceDisplay.textContent = formatCurrency.format(finalPrice);
  }
});
