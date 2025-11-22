<p align="center">
  <img src="public/images/library.png" alt="Library Management System" width="200">
</p>

<h1 align="center">📚 Laravel Library Management System</h1>

<p align="center">
  <strong>A Complete, Modern Library Management System with Beautiful UI</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-8.x-red.svg" alt="Laravel 8">
  <img src="https://img.shields.io/badge/PHP-8.0+-blue.svg" alt="PHP 8.0+">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

---

## ✨ Features

### 🎨 Modern UI Design
- **Professional Color Scheme**: Beautiful blue/purple gradient design
- **Responsive Layout**: Works perfectly on all devices
- **Smooth Animations**: Modern transitions and hover effects
- **Icon Integration**: Font Awesome icons throughout
- **Clean Typography**: Inter & Poppins fonts for better readability

### 📚 Core Features
- ✅ **Book Management**: Add, edit, delete books with cover images
- ✅ **Member Management**: Student, Teacher, Librarian roles
- ✅ **Book Issuing System**: Issue and return books with receipts
- ✅ **Fine Management**: Automatic fine calculation and payment tracking
- ✅ **Reservation System**: Reserve books when unavailable
- ✅ **Advanced Search**: Filter books by multiple criteria
- ✅ **Reports & Analytics**: Comprehensive reporting system
- ✅ **Dashboard**: Role-based dashboards with statistics
- ✅ **Settings Management**: Configurable borrowing limits and fine rates

### 🎯 Special Features
- **Computer Science Department**: Pre-configured for CS department
- **Quantity Management**: Track total, available, and issued quantities
- **Book Condition Tracking**: Monitor book condition on return
- **Receipt Generation**: Unique issue and return receipts
- **Overdue Notifications**: Automatic fine calculation
- **Role-Based Access**: Admin, Librarian, Teacher, Student roles

---

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.0
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/Mdali79/Library-Management-System.git
cd Library-Management-System
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install && npm run dev
```

4. **Create environment file**
```bash
cp .env.example .env
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Configure database** in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=
```

7. **Run migrations and seeders**
```bash
php artisan migrate:fresh --seed
```

8. **Create storage link**
```bash
php artisan storage:link
```

9. **Start the server**
```bash
php artisan serve
```

10. **Visit the application**
```
http://localhost:8000
```

---

## 🔐 Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `password`

### Student Account
- **Username**: `student`
- **Password**: `password`

### Teacher Account
- **Username**: `teacher`
- **Password**: `password`

> **Note**: Change passwords after first login for security!

---

## 📸 Screenshots

### Modern Dashboard
![Dashboard](Screenshots/lms%20(1).png)

### Book Management
![Books](Screenshots/lms%20(2).png)

### Issue Book
![Issue Book](Screenshots/lms%20(3).png)

### Reports
![Reports](Screenshots/lms%20(4).png)

> **Note**: Screenshots show the latest modern UI design with gradient cards, smooth animations, and professional color scheme.

---

## 🎨 UI Improvements

### Design Highlights
- **Color Palette**: Modern blue (#2563eb) and purple (#7c3aed) gradients
- **Typography**: Inter & Poppins fonts for professional look
- **Cards**: Gradient backgrounds with hover animations
- **Forms**: Clean, modern inputs with focus states
- **Tables**: Professional styling with hover effects
- **Navigation**: Sticky header with icon-enhanced menu
- **Buttons**: Gradient effects with smooth transitions

### Responsive Design
- Mobile-friendly layouts
- Touch-optimized buttons
- Adaptive navigation
- Flexible grid system

---

## 📁 Project Structure

```
Library-Management-System/
├── app/
│   ├── Http/Controllers/    # All controllers
│   ├── Models/               # Eloquent models
│   └── Requests/             # Form requests
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/             # Model factories
├── resources/
│   └── views/                # Blade templates
├── public/
│   ├── css/                  # Stylesheets
│   └── images/               # Images
└── routes/
    └── web.php               # Web routes
```

---

## 🔧 Configuration

### Settings
Access Settings (Admin only) to configure:
- Return days
- Fine per day
- Fine grace period
- Borrowing limits (Student/Teacher/Librarian)
- Online payment (if enabled)

### Database Seeding
The system comes pre-seeded with:
- Computer Science categories
- Sample books
- Sample authors and publishers
- Default users (admin, student, teacher)

---

## 📚 Documentation

- [Setup Instructions](SETUP_INSTRUCTIONS.md)
- [Computer Science Setup](COMPUTER_SCIENCE_SETUP.md)
- [UI Improvements](UI_IMPROVEMENTS.md)
- [Implementation Summary](IMPLEMENTATION_SUMMARY.md)
- [Login Credentials](LOGIN_CREDENTIALS.md)

---

## 🛠️ Technologies Used

- **Backend**: Laravel 8.x
- **Frontend**: Blade Templates, Bootstrap 4
- **Database**: MySQL
- **Icons**: Font Awesome 6.4
- **Charts**: Chart.js
- **JavaScript**: jQuery, Bootstrap JS

---

## 📝 Features in Detail

### Book Management
- Add books with cover images
- ISBN, edition, publication year tracking
- Quantity management (total, available, issued)
- Advanced search and filtering
- Category and author management

### Member Management
- Student, Teacher, Librarian roles
- Department and batch tracking
- Registration number and roll number
- Borrowing limit configuration

### Issue & Return
- Issue books with unique receipt numbers
- Return books with condition tracking
- Automatic fine calculation
- Overdue notifications
- Damage notes and book condition

### Fine Management
- Automatic fine calculation
- Fine payment tracking
- Fine history
- Waive fines option
- Payment method tracking

### Reservation System
- Reserve unavailable books
- Notification when book becomes available
- Reservation expiry management
- Auto-cancel expired reservations

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

**Mdali79**

- GitHub: [@Mdali79](https://github.com/Mdali79)
- Repository: [Library-Management-System](https://github.com/Mdali79/Library-Management-System)

---

## ⭐ Show Your Support

If you find this project helpful, please give it a ⭐ on GitHub!

---

## 🎉 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI inspired by modern design principles
- Icons by [Font Awesome](https://fontawesome.com)

---

<p align="center">
  Made with ❤️ using Laravel
</p>
