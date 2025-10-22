document.addEventListener('DOMContentLoaded', () => {
  const sliderInput = document.getElementById('saveToggle');
  const authWrap = document.getElementById('authWrap');
  if (sliderInput) {
    sliderInput.addEventListener('change', () => {
      if (sliderInput.checked) {
        authWrap?.classList.remove('hidden');
      } else {
        authWrap?.classList.add('hidden');
        alert('STAY POOR FOREVER');
      }
    });
  }

  const salaryInput = document.getElementById('sa');
  const salaryDisplay = document.getElementById('salaryDisplay');
  salaryInput?.addEventListener('input', () => {
    const value = parseFloat(salaryInput.value);
    if (!isNaN(value)) {
      salaryDisplay.textContent = new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(value);
    } else {
      salaryDisplay.textContent = '';
    }
  });
});
