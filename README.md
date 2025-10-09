# College Student Management System

A modern web application built with Node.js, Express, and MongoDB that demonstrates CRUD (Create, Read, Update, Delete) operations for managing college student records.

## 🎯 Project Overview

This is a comprehensive college project that showcases:
- **Backend**: Node.js with Express.js framework
- **Database**: MongoDB with Mongoose ODM
- **Frontend**: Modern HTML5, CSS3, and Vanilla JavaScript
- **Features**: Complete CRUD operations for student management

## ✨ Features

### 📊 Dashboard
- Real-time statistics (Total Students, Courses, Average GPA)
- Recent students overview
- Modern card-based layout

### 👥 Student Management
- **Create**: Add new students with validation
- **Read**: View all students in a responsive table
- **Update**: Edit student information via modal
- **Delete**: Remove students with confirmation
- **Search**: Find students by name, ID, or course

### 🎨 Modern UI/UX
- Responsive design for all devices
- Beautiful gradient backgrounds
- Smooth animations and transitions
- Toast notifications for user feedback
- Modal dialogs for editing

## 🛠️ Technology Stack

- **Backend**: Node.js, Express.js
- **Database**: MongoDB, Mongoose
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with Flexbox/Grid
- **Icons**: Font Awesome
- **Development**: Nodemon for auto-restart

## 📋 Prerequisites

Before running this application, make sure you have:

1. **Node.js** (v14 or higher) - [Download here](https://nodejs.org/)
2. **MongoDB** - Choose one option:
   - Local installation - [Download here](https://www.mongodb.com/try/download/community)
   - MongoDB Atlas (Cloud) - [Sign up here](https://www.mongodb.com/atlas)
   - Docker - `docker run -d -p 27017:27017 --name mongodb mongo`

## 🚀 Installation & Setup

### 1. Clone or Download the Project
```bash
# If using git
git clone <repository-url>
cd college-student-management

# Or download and extract the ZIP file
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Configure Environment
The application uses the default MongoDB connection string. If you need to change it:

Edit the `.env` file:
```env
PORT=3000
MONGODB_URI=mongodb://localhost:27017/college_db
```

For MongoDB Atlas, use:
```env
MONGODB_URI=mongodb+srv://username:password@cluster.mongodb.net/college_db
```

### 4. Start MongoDB (if using local installation)
```bash
# On Windows
mongod

# On macOS/Linux
sudo systemctl start mongod
# or
brew services start mongodb-community
```

### 5. Run the Application
```bash
# Development mode (with auto-restart)
npm run dev

# Production mode
npm start
```

### 6. Access the Application
Open your browser and navigate to: `http://localhost:3000`

## 📖 Usage Guide

### Adding Students
1. Click on the "Add Student" tab
2. Fill in all required fields (marked with *)
3. Click "Add Student" button
4. Success notification will appear

### Managing Students
1. Go to "Manage Students" tab
2. View all students in the table
3. Use the search bar to find specific students
4. Click "Edit" to modify student information
5. Click "Delete" to remove a student (with confirmation)

### Dashboard Overview
- View total statistics
- See recently added students
- Quick overview of your data

## 🗃️ Database Schema

### Student Model
```javascript
{
  studentId: String (required, unique),
  firstName: String (required),
  lastName: String (required),
  email: String (required, unique),
  phone: String (required),
  course: String (required),
  year: Number (1-4, required),
  gpa: Number (0-4, optional),
  enrollmentDate: Date (default: now),
  createdAt: Date (auto),
  updatedAt: Date (auto)
}
```

## 🔌 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/students` | Get all students |
| GET | `/api/students/:id` | Get student by ID |
| POST | `/api/students` | Create new student |
| PUT | `/api/students/:id` | Update student |
| DELETE | `/api/students/:id` | Delete student |
| GET | `/api/students/search/:query` | Search students |

### Example API Usage

#### Create Student
```bash
curl -X POST http://localhost:3000/api/students \
  -H "Content-Type: application/json" \
  -d '{
    "studentId": "STU001",
    "firstName": "John",
    "lastName": "Doe",
    "email": "john.doe@college.edu",
    "phone": "(555) 123-4567",
    "course": "Computer Science",
    "year": 2,
    "gpa": 3.75
  }'
```

#### Get All Students
```bash
curl http://localhost:3000/api/students
```

## 📁 Project Structure
```
college-student-management/
├── server.js              # Main server file
├── package.json           # Dependencies and scripts
├── .env                   # Environment variables
├── README.md             # Project documentation
└── public/               # Frontend files
    ├── index.html        # Main HTML file
    ├── styles.css        # CSS styles
    └── script.js         # JavaScript functionality
```

## 🎨 Customization

### Adding New Courses
Edit the course options in `public/index.html`:
```html
<option value="Your New Course">Your New Course</option>
```

### Changing Colors
Modify the CSS variables in `public/styles.css`:
```css
:root {
  --primary-color: #667eea;
  --secondary-color: #764ba2;
}
```

### Adding New Fields
1. Update the schema in `server.js`
2. Add form fields in `index.html`
3. Update the JavaScript in `script.js`

## 🐛 Troubleshooting

### Common Issues

**1. MongoDB Connection Error**
```
Error: connect ECONNREFUSED 127.0.0.1:27017
```
- Make sure MongoDB is running
- Check the connection string in `.env`

**2. Port Already in Use**
```
Error: listen EADDRINUSE :::3000
```
- Change the port in `.env` file
- Or kill the process using the port

**3. Module Not Found**
```
Error: Cannot find module 'express'
```
- Run `npm install` to install dependencies

### Getting Help
- Check the browser console for JavaScript errors
- Check the terminal for server errors
- Ensure all dependencies are installed

## 🚀 Deployment

### Local Network Access
To access from other devices on your network:
```bash
# Find your IP address
ipconfig getifaddr en0  # macOS
hostname -I             # Linux
ipconfig               # Windows

# Then access: http://YOUR_IP:3000
```

### Production Deployment
For production deployment, consider:
- Using PM2 for process management
- Setting up nginx as reverse proxy
- Using MongoDB Atlas for database
- Setting up SSL certificates

## 📝 Learning Outcomes

This project demonstrates:
- ✅ RESTful API design
- ✅ MongoDB CRUD operations
- ✅ Modern frontend development
- ✅ Responsive web design
- ✅ Error handling and validation
- ✅ User experience design
- ✅ Full-stack development

## 🤝 Contributing

Feel free to fork this project and submit pull requests for improvements!

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

**Happy Coding! 🎓**