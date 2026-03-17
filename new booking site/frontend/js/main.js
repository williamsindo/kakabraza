// Select modal elements
const modal = document.getElementById('bookingModal');
const closeBtn = modal.querySelector('.close-btn');
const bnbTitle = document.getElementById('bnbTitle');
const bookingForm = document.getElementById('bookingForm');

let selectedBnB = null;

// Show modal and set BnB name
function openModal(bnbName) {
  selectedBnB = bnbName;
  bnbTitle.textContent = bnbName;
  bookingForm.reset();
  modal.style.display = 'flex';
}

// Hide modal
function closeModal() {
  modal.style.display = 'none';
}

// Click event for Book Now buttons
document.querySelectorAll('.booking-btn').forEach(button => {
  button.addEventListener('click', (e) => {
    const bnbName = e.target.dataset.bnb;
    openModal(bnbName);
  });
});

// Close modal on close button click
closeBtn.addEventListener('click', closeModal);

// Close modal if click outside modal content
window.addEventListener('click', (e) => {
  if (e.target === modal) {
    closeModal();
  }
});

// Booking form submission handler
bookingForm.addEventListener('submit', (e) => {
  e.preventDefault();

  const name = document.getElementById('guestName').value.trim();
  const email = document.getElementById('guestEmail').value.trim();
  const checkIn = document.getElementById('checkInDate').value;
  const checkOut = document.getElementById('checkOutDate').value;

  if (!name || !email || !checkIn || !checkOut) {
    alert('Please fill in all fields.');
    return;
  }

  if (new Date(checkOut) <= new Date(checkIn)) {
    alert('Check-out date must be after check-in date.');
    return;
  }

  // Confirm booking (could be expanded for backend connection)
  alert(`Thank you, ${name}! Your booking at ${selectedBnB} from ${checkIn} to ${checkOut} has been received. We will contact you shortly at ${email}.`);

  closeModal();
});
