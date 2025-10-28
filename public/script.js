// Global variables
let students = [];
let currentEditId = null;

// DOM Elements
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');
const studentForm = document.getElementById('student-form');
const editForm = document.getElementById('edit-form');
const editModal = document.getElementById('edit-modal');
const searchInput = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    loadStudents();
    bindEvents();
    updateDashboard();
});

// Tab functionality
function initializeTabs() {
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
}

function switchTab(tabId) {
    // Remove active class from all tabs and contents
    tabBtns.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
    
    // Add active class to selected tab and content
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
    
    // Load data based on tab
    if (tabId === 'dashboard') {
        updateDashboard();
    } else if (tabId === 'manage-students') {
        displayStudents(students);
    }
}

// Event bindings
function bindEvents() {
    // Student form submission
    studentForm.addEventListener('submit', handleAddStudent);
    
    // Edit form submission
    editForm.addEventListener('submit', handleEditStudent);
    
    // Modal close events
    document.querySelector('.close').addEventListener('click', closeModal);
    document.getElementById('cancel-edit').addEventListener('click', closeModal);
    
    // Search functionality
    searchBtn.addEventListener('click', handleSearch);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === editModal) {
            closeModal();
        }
    });
}

// API Functions
async function loadStudents() {
    try {
        showLoading();
        const response = await fetch('/api/students');
        const data = await response.json();
        
        if (data.success) {
            students = data.data;
            displayStudents(students);
            updateDashboard();
        } else {
            showToast('Error loading students', 'error');
        }
    } catch (error) {
        console.error('Error loading students:', error);
        showToast('Error loading students', 'error');
    } finally {
        hideLoading();
    }
}

async function addStudent(studentData) {
    try {
        const response = await fetch('/api/students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(studentData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            students.unshift(data.data);
            showToast('Student added successfully!', 'success');
            studentForm.reset();
            updateDashboard();
            if (document.getElementById('manage-students').classList.contains('active')) {
                displayStudents(students);
            }
        } else {
            showToast(data.message || 'Error adding student', 'error');
        }
    } catch (error) {
        console.error('Error adding student:', error);
        showToast('Error adding student', 'error');
    }
}

async function updateStudent(id, studentData) {
    try {
        const response = await fetch(`/api/students/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(studentData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            const index = students.findIndex(s => s._id === id);
            if (index !== -1) {
                students[index] = data.data;
            }
            showToast('Student updated successfully!', 'success');
            closeModal();
            displayStudents(students);
            updateDashboard();
        } else {
            showToast(data.message || 'Error updating student', 'error');
        }
    } catch (error) {
        console.error('Error updating student:', error);
        showToast('Error updating student', 'error');
    }
}

async function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/students/${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            students = students.filter(s => s._id !== id);
            showToast('Student deleted successfully!', 'success');
            displayStudents(students);
            updateDashboard();
        } else {
            showToast(data.message || 'Error deleting student', 'error');
        }
    } catch (error) {
        console.error('Error deleting student:', error);
        showToast('Error deleting student', 'error');
    }
}

async function searchStudents(query) {
    try {
        showLoading();
        const response = await fetch(`/api/students/search/${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success) {
            displayStudents(data.data);
        } else {
            showToast('Error searching students', 'error');
        }
    } catch (error) {
        console.error('Error searching students:', error);
        showToast('Error searching students', 'error');
    } finally {
        hideLoading();
    }
}

// Event Handlers
function handleAddStudent(e) {
    e.preventDefault();
    
    const formData = new FormData(studentForm);
    const studentData = {};
    
    for (let [key, value] = formData.entries()) {
        if (key === 'year' || key === 'gpa') {
            studentData[key] = value ? Number(value) : undefined;
        } else {
            studentData[key] = value;
        }
    }
    
    // Remove empty gpa if not provided
    if (!studentData.gpa) {
        delete studentData.gpa;
    }
    
    addStudent(studentData);
}

function handleEditStudent(e) {
    e.preventDefault();
    
    const formData = new FormData(editForm);
    const studentData = {};
    
    for (let [key, value] of formData.entries()) {
        if (key === 'id') continue;
        
        if (key === 'year' || key === 'gpa') {
            studentData[key] = value ? Number(value) : undefined;
        } else {
            studentData[key] = value;
        }
    }
    
    // Remove empty gpa if not provided
    if (!studentData.gpa) {
        delete studentData.gpa;
    }
    
    const id = document.getElementById('edit-id').value;
    updateStudent(id, studentData);
}

function handleSearch() {
    const query = searchInput.value.trim();
    if (query) {
        searchStudents(query);
    } else {
        displayStudents(students);
    }
}

// Display Functions
function displayStudents(studentsToShow) {
    const studentsList = document.getElementById('students-list');
    
    if (studentsToShow.length === 0) {
        studentsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No students found</h3>
                <p>Add some students to get started!</p>
            </div>
        `;
        return;
    }
    
    const tableHTML = `
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>GPA</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${studentsToShow.map(student => `
                    <tr>
                        <td><span class="student-id">${student.studentId}</span></td>
                        <td>${student.firstName} ${student.lastName}</td>
                        <td>${student.email}</td>
                        <td><span class="course-badge">${student.course}</span></td>
                        <td><span class="year-badge">${getYearText(student.year)}</span></td>
                        <td><span class="gpa-badge ${getGPAClass(student.gpa)}">${student.gpa ? student.gpa.toFixed(2) : 'N/A'}</span></td>
                        <td class="actions">
                            <button class="btn btn-success" onclick="editStudent('${student._id}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger" onclick="deleteStudent('${student._id}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    studentsList.innerHTML = tableHTML;
}

function updateDashboard() {
    // Update stats
    document.getElementById('total-students').textContent = students.length;
    
    const courses = [...new Set(students.map(s => s.course))];
    document.getElementById('total-courses').textContent = courses.length;
    
    const avgGPA = students.length > 0 
        ? students.filter(s => s.gpa).reduce((sum, s) => sum + s.gpa, 0) / students.filter(s => s.gpa).length
        : 0;
    document.getElementById('avg-gpa').textContent = avgGPA ? avgGPA.toFixed(2) : '0.0';
    
    // Display recent students
    const recentStudents = students.slice(0, 6);
    const recentStudentsList = document.getElementById('recent-students-list');
    
    if (recentStudents.length === 0) {
        recentStudentsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-user-plus"></i>
                <h3>No students yet</h3>
                <p>Start by adding your first student!</p>
            </div>
        `;
        return;
    }
    
    recentStudentsList.innerHTML = recentStudents.map(student => `
        <div class="student-card">
            <span class="student-id">${student.studentId}</span>
            <h4>${student.firstName} ${student.lastName}</h4>
            <p><i class="fas fa-envelope"></i> ${student.email}</p>
            <p><i class="fas fa-phone"></i> ${student.phone}</p>
            <p><i class="fas fa-book"></i> ${student.course}</p>
            <p><i class="fas fa-calendar"></i> ${getYearText(student.year)} Year</p>
            ${student.gpa ? `<p><i class="fas fa-star"></i> GPA: ${student.gpa.toFixed(2)}</p>` : ''}
        </div>
    `).join('');
}

// Modal Functions
function editStudent(id) {
    const student = students.find(s => s._id === id);
    if (!student) return;
    
    currentEditId = id;
    
    // Populate form
    document.getElementById('edit-id').value = student._id;
    document.getElementById('edit-studentId').value = student.studentId;
    document.getElementById('edit-firstName').value = student.firstName;
    document.getElementById('edit-lastName').value = student.lastName;
    document.getElementById('edit-email').value = student.email;
    document.getElementById('edit-phone').value = student.phone;
    document.getElementById('edit-course').value = student.course;
    document.getElementById('edit-year').value = student.year;
    document.getElementById('edit-gpa').value = student.gpa || '';
    
    editModal.style.display = 'block';
}

function closeModal() {
    editModal.style.display = 'none';
    currentEditId = null;
    editForm.reset();
}

// Utility Functions
function getYearText(year) {
    const yearTexts = { 1: '1st', 2: '2nd', 3: '3rd', 4: '4th' };
    return yearTexts[year] || year;
}

function getGPAClass(gpa) {
    if (!gpa) return '';
    if (gpa >= 3.5) return '';
    if (gpa >= 2.5) return 'medium';
    return 'low';
}

function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'exclamation-circle' : 
                 'info-circle';
    
    toast.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    toastContainer.appendChild(toast);
    
    // Remove toast after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

function showLoading() {
    // You can implement a loading spinner here
    console.log('Loading...');
}

function hideLoading() {
    // Hide loading spinner
    console.log('Loading complete');
}

// Make functions globally available
window.editStudent = editStudent;
window.deleteStudent = deleteStudent;