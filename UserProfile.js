  // Your existing JavaScript code here
// Get modal elements
const modal = document.getElementById("myModal");
const closeBtn = document.querySelector(".close");
const modalTitle = document.getElementById("modalTitle");
const modalHost = document.getElementById("modalHost");
const modalLocation = document.getElementById("modalLocation");
const modalDate = document.getElementById("modalDate");
const modalDescription = document.getElementById("modalDescription");

// Function to open the modal with dynamic content
function openModal(title, host, location, date, description) {
  modalTitle.textContent = title;
  modalHost.textContent = host;
  modalLocation.textContent = location;
  modalDate.textContent = date;
  modalDescription.textContent = description;
  modal.style.display = "block"; // Show the modal
}

// Close the modal when the close button is clicked
closeBtn.onclick = function () {
  modal.style.display = "none";
};

// Close the modal when clicking outside of it
window.onclick = function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

