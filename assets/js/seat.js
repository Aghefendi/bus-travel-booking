const seatMap = document.querySelector(".seat-map");
const selectedSeatInput = document.getElementById("selectedSeat");
const buyButton = document.getElementById("buyButton");
const selectionMessage = document.getElementById("selectionMessage");

seatMap.addEventListener("click", function (e) {
  if (e.target.classList.contains("available")) {
    const currentlySelected = seatMap.querySelector(".selected");
    if (currentlySelected) {
      currentlySelected.classList.remove("selected");
    }

    e.target.classList.add("selected");
    const seatNumber = e.target.dataset.seatNumber;

    selectedSeatInput.value = seatNumber;

    buyButton.disabled = false;
    selectionMessage.style.display = "none";
  }
});
