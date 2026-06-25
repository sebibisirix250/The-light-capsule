//SCRIPT FOR RECENT ORDER DISPLAY IN ACCOUNT PAGE

//PAGE CONTAINER
document.addEventListener("DOMContentLoaded", function () {
  const activityContainer = document.getElementById(
    "recent-activity-container",
  );

  if (!activityContainer) return; //IF WRONG PAGE

  // EXTRACTS ROOT URL
  const baseUrl = activityContainer.getAttribute("data-baseurl");

  // API CALL FOR HANDLER
fetch(`${baseUrl}/backend/handlers/get_recent_order.php`)

    .then(async response => {
        const text = await response.text(); //RAW TEXT
        try {
            return JSON.parse(text); //READ AS JSON
        } catch (error) {
            // IF FAILS, PRINT ERROR
            console.error("BACKEND CRASHED. PHP output:", text);
            throw new Error("JSON Parse Failed");
        }
    })

    //IF SUCCESS & ORDER EXISTS, INJECT HTML
    .then(data => {
        if (data.success && data.has_order) {
            activityContainer.innerHTML = `
                <div class="recent-order-card">
                    <div class="order-meta">
                        <strong>Order #${data.order.id}</strong>
                        <span>${data.order.date}</span>
                    </div>
                    <div class="order-details">
                        <span class="order-type-badge">${data.order.type} Session</span>
                        <span class="order-price">€${data.order.price}</span>
                    </div>
                    <div class="order-action">
                        <span class="status-indicator">Status: ${data.order.status}</span>
                        <a href="${baseUrl}/pages/order_detail.php?id=${data.order.id}" class="text-link">View details</a>
                    </div>
                </div>
            `;
        //IF SUCCESS BUT ORDER DOESN'T EXIST
        } else {
            activityContainer.innerHTML = `
                <div class="empty-state">
                    <p>No recent orders found. Ready for your next session?</p>
                    <a href="${baseUrl}/pages/service_request.php" class="text-link">Book a session</a>
                </div>
            `;
        }
    })

    //ERROR
    .catch(error => {
        activityContainer.innerHTML = `
            <div class="empty-state">
                <p style="color: #e74c3c;">Unable to load order data.</p>
            </div>
        `;
    })
});
