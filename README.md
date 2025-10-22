# 💰 Expense Tracker Website

A modern, responsive expense tracking web application built with PHP, MySQL, and JavaScript. Help users save money by tracking their expenses and analyzing spending patterns.

## ✨ Features

### 🎯 Landing Page
- Eye-catching "DO YOU WANT TO SAVE MONEY?" title
- Interactive sliding Yes/No button
- Smooth animations and modern UI
- "STAY POOR FOREVER" alert for No selection

### 🔐 Authentication System
- User registration with validation
- Secure login system
- Forgot password functionality
- Session management
- Password hashing with PHP's password_hash()

### 🏠 Dashboard (Homepage)
- Clean, modern interface with navigation bar
- Monthly salary input and display
- Real-time budget tracking (Salary vs Expenses)
- Add expenses with categories:
  - Online Food Order
  - Street Food
  - Groceries
  - Online Shopping
  - Offline Shopping
  - Friends & Family
  - Transportation
  - Entertainment
  - Bills & Utilities
  - Other
- Recent expenses list with delete functionality

### 📊 Previous Savings Analysis
- Month-wise expense analysis
- Interactive pie chart showing expense distribution
- Category breakdown with percentages
- Detailed expense table
- Savings/overspending calculation
- Monthly comparison

### 👤 User Profile
- Profile information management
- Account statistics (total expenses, average expense, etc.)
- Password change functionality
- Member since date tracking

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Charts**: Chart.js
- **Styling**: Custom CSS with gradients and animations
- **Security**: Password hashing, SQL injection prevention, session management

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## 🚀 Installation & Setup

### 1. Clone/Download the Project
```bash
# If using git
git clone <repository-url>

# Or download and extract the files to your web server directory
```

### 2. Database Setup
The application will automatically create the database and tables when you first access it. The database configuration is in `config/database.php`.

**Default Database Settings:**
- Host: localhost
- Database: expense_tracker
- Username: root
- Password: (empty)

**To customize database settings:**
1. Edit `config/database.php`
2. Update the connection parameters:
```php
private $host = 'your_host';
private $db_name = 'your_database_name';
private $username = 'your_username';
private $password = 'your_password';
```

### 3. Web Server Setup

#### Using XAMPP (Recommended for beginners)
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Copy the project folder to `xampp/htdocs/`
3. Start Apache and MySQL from XAMPP Control Panel
4. Access the application at `http://localhost/expense-tracker/`

#### Using WAMP
1. Download and install [WAMP](http://www.wampserver.com/)
2. Copy the project folder to `wamp/www/`
3. Start WAMP services
4. Access the application at `http://localhost/expense-tracker/`

#### Using LAMP (Linux)
1. Install Apache, MySQL, and PHP
2. Copy the project to `/var/www/html/`
3. Set proper permissions
4. Access the application at `http://localhost/expense-tracker/`

### 4. File Permissions (Linux/Mac)
```bash
chmod 755 /path/to/expense-tracker
chmod 644 /path/to/expense-tracker/*.php
```

## 📁 Project Structure

```
expense-tracker/
├── index.php              # Landing page with slider
├── homepage.php           # Main dashboard
├── previous_savings.php   # Savings analysis page
├── profile.php           # User profile page
├── auth/
│   ├── login.php         # Login handler
│   ├── register.php      # Registration handler
│   ├── logout.php        # Logout handler
│   └── forgot_password.php # Password reset
├── api/
│   ├── add_expense.php   # Add expense endpoint
│   ├── get_expenses.php  # Get expenses endpoint
│   ├── delete_expense.php # Delete expense endpoint
│   └── update_salary.php # Update salary endpoint
├── config/
│   └── database.php      # Database configuration
├── css/
│   └── style.css         # Main stylesheet
├── js/
│   └── script.js         # JavaScript functionality
└── README.md            # This file
```

## 🎨 Design Features

- **Modern UI/UX**: Clean, professional design with gradients and animations
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Interactive Elements**: Sliding buttons, hover effects, smooth transitions
- **Color Scheme**: Purple gradients with accent colors for different states
- **Typography**: Clear, readable fonts with proper hierarchy
- **Charts**: Beautiful pie charts for expense visualization

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session management for user authentication
- Input validation and sanitization
- CSRF protection through session tokens
- Email validation

## 💡 Usage Guide

### Getting Started
1. Visit the landing page
2. Slide the button to "YES" to proceed
3. Register a new account or login
4. Set your monthly salary on the homepage
5. Start adding your daily expenses

### Adding Expenses
1. Go to the homepage
2. Enter the expense amount
3. Select a category from the dropdown
4. Add an optional description
5. Click "Add Expense"

### Viewing Analysis
1. Click "Previous Savings" in the navigation
2. Select the month you want to analyze
3. View the pie chart and detailed breakdown
4. Check your savings/overspending status

### Managing Profile
1. Click "Profile" in the navigation
2. Update your personal information
3. View your account statistics
4. Change your password if needed

## 🐛 Troubleshooting

### Database Connection Issues
- Check if MySQL service is running
- Verify database credentials in `config/database.php`
- Ensure the database user has proper permissions

### Permission Errors
- Check file permissions (755 for directories, 644 for files)
- Ensure web server has read access to all files

### JavaScript Not Working
- Check browser console for errors
- Ensure all JavaScript files are loading properly
- Verify Chart.js CDN is accessible

### Styling Issues
- Clear browser cache
- Check if CSS file is loading
- Verify file paths are correct

## 🔧 Customization

### Adding New Expense Categories
Edit the category options in `homepage.php`:
```php
<option value="Your New Category">Your New Category</option>
```

### Changing Colors
Modify the CSS variables in `css/style.css`:
```css
/* Update gradient colors */
background: linear-gradient(135deg, #your-color1 0%, #your-color2 100%);
```

### Database Schema
The application creates these tables:
- `users`: User account information
- `expenses`: Expense records
- `password_reset_tokens`: Password reset tokens

## 📈 Future Enhancements

- Email notifications for budget limits
- Export data to CSV/PDF
- Multiple currency support
- Expense categories customization
- Budget planning features
- Mobile app version
- Social sharing features
- Advanced analytics and reports

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 📞 Support

If you encounter any issues or have questions:
1. Check the troubleshooting section
2. Review the code comments
3. Create an issue in the repository
4. Contact the development team

---

**Happy Saving! 💰**

Remember: The goal is to help users track their expenses and save money. Every feature should contribute to this objective.