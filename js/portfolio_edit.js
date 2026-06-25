//EVENT STORY CREATION - IMAGE ORDERING

document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("dragGrid");
  const orderInput = document.getElementById("imageOrderInput");

  if (!grid || !orderInput) return;

  let draggedItem = null;

  //READ PICTURE ID TAG AND STORE THEM ALL IN ONE TEXT
  const updateOrderInput = () => {
    const items = grid.querySelectorAll(".draggable-item");
    const ids = Array.from(items).map((item) => item.getAttribute("data-id"));
    orderInput.value = ids.join(",");
  };

  //EVENT LISTENER FOR DRAG
  grid.addEventListener("dragstart", (e) => {
    draggedItem = e.target;
    e.dataTransfer.effectAllowed = "move";
    //FOR SOME OTHER BROWSERS TO ALLOW DRAG
    e.dataTransfer.setData("text/html", e.target.innerHTML);
    draggedItem.classList.add("dragging");
  });

  grid.addEventListener("dragover", (e) => {
    e.preventDefault(); //PREVENT DEFAULT EVENT ON DRAG, KILLS RED ICON
    return false;
  });

  //LOGIC FOR SWAP
  grid.addEventListener("dragenter", (e) => {
    const target = e.target.closest(".draggable-item");
    if (target && target !== draggedItem) {
      target.classList.add("over");

      //MOVING ITEMS IN THE DOM
      const rect = target.getBoundingClientRect();
      const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
      grid.insertBefore(draggedItem, next ? target.nextSibling : target);
    }
  });

  //REMOVE CLASSIC BEHAVIOUR, MAKE DROPPING CLEAN
  grid.addEventListener("dragleave", (e) => {
    const target = e.target.closest(".draggable-item");
    if (target) target.classList.remove("over");
  });

  grid.addEventListener("drop", (e) => {
    e.stopPropagation();
    e.preventDefault();
    return false;
  });

  grid.addEventListener("dragend", (e) => {
    const items = grid.querySelectorAll(".draggable-item");
    items.forEach((item) => {
      item.classList.remove("dragging");
      item.classList.remove("over");
    });
    updateOrderInput();
  });

  updateOrderInput();
});
