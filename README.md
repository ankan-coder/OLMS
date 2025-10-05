# OLMS - Online Library Management System

A comprehensive web-based library management system developed as a minor project for BTech 5th semester Software Engineering course. This system provides a complete solution for managing library operations including book management, user registration, book issuing/returning, and transaction tracking.

## 🚀 Features

### For Members
- **User Registration & Authentication**: Secure member registration with profile photo upload
- **Book Browsing**: View available books with detailed information (title, author, publication, genre, stock)
- **Book Request System**: Request books for borrowing
- **My Books**: View currently issued books and return them
- **Profile Management**: Update personal information and profile

### For Librarians
- **Book Management**: Add, edit, and delete books from the library catalog
- **Issue Management**: Process book issue requests from members
- **Transaction Tracking**: Monitor all book transactions (issue/return)
- **Member Management**: View and manage member profiles
- **Request Processing**: Handle book issue requests efficiently

### For Administrators
- **Full System Access**: Complete control over all system operations
- **User Management**: Manage both members and librarians
- **System Monitoring**: Track all activities and transactions
- **Database Management**: Full access to all data and operations

## 🛠️ Technology Stack

- **Backend**: PHP 8.2+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Dependencies**: PHPMailer (for email functionality)
- **Server**: Apache (XAMPP)

## 📁 Project Structure

```
OLMS/
├── CSS/                          # Stylesheets
│   ├── Images/                   # Background images and assets
│   ├── about_style.css
│   ├── book_style.css
│   ├── contact_us_style.css
│   ├── index_style.css
│   ├── login_style.css
│   ├── member_style.css
│   ├── my_books_style.css
│   ├── nav_style.css
│   └── ... (other CSS files)
├── Database file/
│   └── olms.sql                  # Database schema and sample data
├── favicon/                      # Website favicon files
├── php_utils/
│   └── _dbConnect.php           # Database connection configuration
├── profile_pics/                # User profile pictures
│   ├── Librarians/
│   └── Members/
├── vendor/                      # Composer dependencies
│   └── phpmailer/
├── about.html                   # About page
├── admin_login.php             # Administrator login
├── admin_page.php              # Administrator dashboard
├── books.php                   # Book management
├── contact.php                 # Contact form
├── index.html                  # Homepage
├── issue_requests.php          # Book issue request management
├── librarian_login.php         # Librarian login
├── librarian_page.php          # Librarian dashboard
├── member_login.php            # Member login
├── member_page.php             # Member dashboard
├── member_signup.php           # Member registration
├── my_books.php                # Member's issued books
├── transactions.php            # Transaction history
└── ... (other PHP files)
```

## 🗄️ Database Schema

The system uses a MySQL database with the following main tables:

- **olms_books**: Stores book information (ID, name, author, publication, genre, stock)
- **olms_members**: Member information and credentials
- **olms_librarian**: Librarian information and credentials
- **olms_issued**: Currently issued books
- **olms_issue_requests**: Pending book issue requests
- **olms_transactions**: Complete transaction history
- **olms_messages**: Contact form messages

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- Web browser

### Installation Steps

1. **Clone/Download the project**
   ```bash
   # Place the OLMS folder in your XAMPP htdocs directory
   # Path: C:\xampp\htdocs\OLMS
   ```

2. **Database Setup**
   - Start XAMPP and ensure Apache and MySQL services are running
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `olms`
   - Import the `Database file/olms.sql` file to set up the database schema

3. **Configure Database Connection**
   - Edit `php_utils/_dbConnect.php` if needed:
   ```php
   $server = "localhost";
   $uname = "root";        // Your MySQL username
   $pword = "";            // Your MySQL password
   $dbname = "olms";
   ```

4. **Install Dependencies**
   ```bash
   composer install
   ```

5. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/OLMS`

## 👥 User Roles & Access

### Default Credentials
- **Administrator**: 
  - Username: `admin`
  - Password: `admin`

### Member Registration
- Members can register through the "Get Membership" link
- Profile photo upload required
- Email and username must be unique

### Librarian Registration
- Librarians can register through the librarian signup page
- Admin approval may be required (depending on implementation)

## 🔧 Key Features Explained

### Book Management
- Add books with complete details (title, author, publication, genre, stock)
- Edit book information
- Delete books from catalog
- Stock management

### Issue/Return System
- Members can request books
- Librarians process requests and issue books
- Automatic return date calculation (10 days from issue)
- Transaction logging for all operations

### Security Features
- Password hashing using PHP's `password_hash()`
- Session management with timeout (5 minutes)
- Role-based access control
- SQL injection prevention using prepared statements

### User Interface
- Responsive design
- Modern CSS styling with Google Fonts
- Intuitive navigation
- Background images and visual appeal

## 📱 Usage

1. **For Members**:
   - Register for membership
   - Browse available books
   - Request books for borrowing
   - View and return issued books

2. **For Librarians**:
   - Login with librarian credentials
   - Manage book catalog
   - Process book issue requests
   - Monitor transactions

3. **For Administrators**:
   - Full system access
   - Manage all users and books
   - Monitor system activities

## 🎯 Project Information

- **Course**: BTech Computer Science and Engineering, 5th Semester
- **Institution**: Bengal College of Engineering and Technology
- **Project Type**: Software Engineering Minor Project
- **Development Period**: 2023

## 🔮 Future Enhancements

- Email notifications for book due dates
- Advanced search and filtering
- Book reservation system
- Fine management for overdue books
- Mobile-responsive improvements
- Advanced reporting and analytics

## 📞 Support

For any queries or support, please contact through the contact form on the website or reach out to the development team.

---

**Note**: This is an academic project developed for educational purposes. Ensure proper security measures are implemented before using in a production environment.
