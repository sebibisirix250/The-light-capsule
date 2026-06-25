//CART INTERACTIONS 

document.addEventListener("DOMContentLoaded", () => {
  const itemsSection = document.querySelector(".cart-items-section");
  if (!itemsSection) return;

  //CURRENCY
  const formatCurrency = (amount) => {
    return "RON" + parseFloat(amount).toFixed(2);
  };

  //PRICES UPDATES
  const recalculateCart = () => {
    let newTotal = 0;
    const items = document.querySelectorAll(".cart-item-card");

    items.forEach((item) => {
      const price = parseFloat(item.dataset.unitPrice);
      const qty = parseInt(item.querySelector(".qty-input").value) || 0;
      const lineTotal = price * qty;

      item.querySelector(".line-total").innerText = formatCurrency(lineTotal);
      newTotal += lineTotal;
    });

    document.getElementById("summary-subtotal").innerText =
      formatCurrency(newTotal);
    document.getElementById("summary-total").innerText =
      formatCurrency(newTotal);
  };

  //EVENTS LINKED BUTTONS
  itemsSection.addEventListener("click", (e) => {
    const target = e.target.closest("button");
    if (!target) return;

    // TRASH BUTTON - LETS PHP BACK-END DELETE ITEMS NATURALLY
    if (target.classList.contains("btn-remove")) {
      return;
    }

    // QUANTITY BUTTONS
    if (
      target.classList.contains("minus") ||
      target.classList.contains("plus")
    ) {
      e.preventDefault(); //PREVENTS REFRESH

      const input = target
        .closest(".quantity-wrapper")
        .querySelector(".qty-input");
      let currentQty = parseInt(input.value) || 0;

      if (target.classList.contains("minus") && currentQty > 1) {
        input.value = currentQty - 1;
        recalculateCart();
      }

      if (target.classList.contains("plus")) {
        input.value = currentQty + 1;
        recalculateCart();
      }
    }
  });

  //MANUAL KEYBOARD INPUT CHANGES
  itemsSection.addEventListener("change", (e) => {
    if (e.target.classList.contains("qty-input")) {
      let val = parseInt(e.target.value);
      if (isNaN(val) || val < 1) {
        e.target.value = 1;
      }
      recalculateCart();
    }
  });

  //PROMO CODE HANDLER - TO BE DEVELOPED (MOCK STATE) - NOT ACTIVE
  const promoBtn = document.getElementById("apply-promo-btn");
  const promoInput = document.getElementById("promo-input");
  const promoMsg = document.getElementById("promo-message");

  if (promoBtn) {
    promoBtn.addEventListener("click", () => {
      const code = promoInput.value.trim().toUpperCase();
      if (code === "") return;

      promoMsg.style.color = "#59C5B8";
      promoMsg.innerText = "Checking code...";

      setTimeout(() => {
        if (code === "CAPSULE10") {
          promoMsg.innerText = "10% discount applied!";
        } else {
          promoMsg.style.color = "#e74c3c";
          promoMsg.innerText = "Invalid or expired code.";
        }
      }, 800);
    });
  }
});
