// Fill today date automatically
const readingDateInput = document.getElementById('readingDate');
readingDateInput.value = new Date().toISOString().substr(0,10);

const meterForm = document.getElementById('meterReadingForm');
meterForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const customer = document.getElementById('customerName').value.trim();
  const account = document.getElementById('accountNumber').value.trim();
  const current = parseFloat(document.getElementById('currentReading').value);
  const prev = parseFloat(document.getElementById('previousReading').value) || 0;
  const notes = document.getElementById('notes').value.trim();

  // Validation
  if(!customer || !account || isNaN(current)) {
    alert("Please fill all required fields.");
    return;
  }

  if(!/^\d{10}$/.test(account)) {
    alert("Account Number must be exactly 10 digits.");
    return;
  }

  if(current < prev) {
    alert("Current reading cannot be less than previous reading.");
    return;
  }

  alert(`Meter reading saved successfully for ${customer}.\nPrevious: ${prev}\nCurrent: ${current}\nNotes: ${notes}`);
  meterForm.reset();
  readingDateInput.value = new Date().toISOString().substr(0,10);
});
