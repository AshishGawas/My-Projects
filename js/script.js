document.addEventListener('DOMContentLoaded', function() {
    const sliderThumb = document.getElementById('sliderThumb');
    const sliderTrack = document.querySelector('.slider-track');
    const authForms = document.getElementById('authForms');
    
    let isSliding = false;
    let startX = 0;
    let currentX = 0;
    
    // Slider functionality
    sliderTrack.addEventListener('mousedown', startSlide);
    sliderTrack.addEventListener('touchstart', startSlide);
    
    document.addEventListener('mousemove', slide);
    document.addEventListener('touchmove', slide);
    
    document.addEventListener('mouseup', endSlide);
    document.addEventListener('touchend', endSlide);
    
    function startSlide(e) {
        isSliding = true;
        startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
        sliderTrack.style.cursor = 'grabbing';
    }
    
    function slide(e) {
        if (!isSliding) return;
        
        e.preventDefault();
        currentX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
        const deltaX = currentX - startX;
        
        const rect = sliderTrack.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        
        if (currentX > centerX) {
            // Slide to YES (right)
            sliderThumb.classList.add('active');
        } else {
            // Slide to NO (left)
            sliderThumb.classList.remove('active');
        }
    }
    
    function endSlide(e) {
        if (!isSliding) return;
        
        isSliding = false;
        sliderTrack.style.cursor = 'pointer';
        
        const rect = sliderTrack.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        
        if (currentX > centerX) {
            // YES selected - show auth forms
            setTimeout(() => {
                authForms.style.display = 'block';
            }, 300);
        } else {
            // NO selected - show alert
            showStayPoorAlert();
        }
    }
    
    // Click functionality for slider
    sliderTrack.addEventListener('click', function(e) {
        if (isSliding) return;
        
        const rect = sliderTrack.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        
        if (e.clientX > centerX) {
            // YES selected
            sliderThumb.classList.add('active');
            setTimeout(() => {
                authForms.style.display = 'block';
            }, 300);
        } else {
            // NO selected
            sliderThumb.classList.remove('active');
            showStayPoorAlert();
        }
    });
    
    function showStayPoorAlert() {
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
        
        const alert = document.createElement('div');
        alert.className = 'alert';
        alert.textContent = 'STAY POOR FOREVER';
        
        document.body.appendChild(overlay);
        document.body.appendChild(alert);
        
        setTimeout(() => {
            document.body.removeChild(overlay);
            document.body.removeChild(alert);
        }, 3000);
    }
});

// Auth form switching functions
function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('signupForm').style.display = 'none';
    document.getElementById('forgotForm').style.display = 'none';
    
    // Update toggle buttons
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    toggleBtns.forEach(btn => btn.classList.remove('active'));
    toggleBtns[0].classList.add('active');
}

function showSignup() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('signupForm').style.display = 'block';
    document.getElementById('forgotForm').style.display = 'none';
    
    // Update toggle buttons
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    toggleBtns.forEach(btn => btn.classList.remove('active'));
    toggleBtns[1].classList.add('active');
}

function showForgotPassword() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('signupForm').style.display = 'none';
    document.getElementById('forgotForm').style.display = 'block';
    
    // Remove active class from toggle buttons
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    toggleBtns.forEach(btn => btn.classList.remove('active'));
}

// Homepage functionality
function updateSalaryDisplay() {
    const salaryInput = document.getElementById('salaryInput');
    const salaryDisplay = document.getElementById('salaryDisplay');
    
    if (salaryInput && salaryDisplay) {
        const salary = salaryInput.value;
        if (salary) {
            salaryDisplay.textContent = `₹${parseFloat(salary).toLocaleString()}`;
        } else {
            salaryDisplay.textContent = '';
        }
    }
}

function addExpense() {
    const amountInput = document.getElementById('expenseAmount');
    const categorySelect = document.getElementById('expenseCategory');
    const descriptionInput = document.getElementById('expenseDescription');
    
    const amount = amountInput.value;
    const category = categorySelect.value;
    const description = descriptionInput.value;
    
    if (!amount || !category) {
        alert('Please fill in amount and category');
        return;
    }
    
    // Send data to server
    fetch('api/add_expense.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            amount: amount,
            category: category,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear form
            amountInput.value = '';
            descriptionInput.value = '';
            categorySelect.value = '';
            
            // Reload expenses
            loadExpenses();
        } else {
            alert('Error adding expense: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding expense');
    });
}

function loadExpenses() {
    fetch('api/get_expenses.php')
    .then(response => response.json())
    .then(data => {
        const expenseList = document.getElementById('expenseList');
        if (expenseList) {
            expenseList.innerHTML = '';
            
            data.expenses.forEach(expense => {
                const expenseItem = document.createElement('div');
                expenseItem.className = 'expense-item';
                expenseItem.innerHTML = `
                    <div>
                        <strong>₹${expense.amount}</strong> - ${expense.category}
                        ${expense.description ? `<br><small>${expense.description}</small>` : ''}
                        <br><small>${expense.date}</small>
                    </div>
                    <button class="delete-btn" onclick="deleteExpense(${expense.id})">Delete</button>
                `;
                expenseList.appendChild(expenseItem);
            });
        }
    })
    .catch(error => {
        console.error('Error loading expenses:', error);
    });
}

function deleteExpense(id) {
    if (confirm('Are you sure you want to delete this expense?')) {
        fetch('api/delete_expense.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({id: id})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadExpenses();
            } else {
                alert('Error deleting expense');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting expense');
        });
    }
}

function updateSalary() {
    const salaryInput = document.getElementById('salaryInput');
    const salary = salaryInput.value;
    
    if (!salary) {
        alert('Please enter your salary');
        return;
    }
    
    fetch('api/update_salary.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({salary: salary})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSalaryDisplay();
        } else {
            alert('Error updating salary');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating salary');
    });
}