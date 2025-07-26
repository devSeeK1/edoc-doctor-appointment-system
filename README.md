# eDoc Doctor Appointment System

A comprehensive web-based platform for managing doctor appointments, patient records, and medical scheduling.

## Features

- **Multi-user system** with roles for Admin, Doctor, and Patient
- **Appointment scheduling** with date and time management
- **Patient registration** and profile management
- **Doctor profiles** with specialties and schedules
- **Admin dashboard** for system management
- **Responsive design** for desktop and mobile devices

## User Roles

### Admin
- Manage doctors, patients, and appointments
- View system statistics and reports
- Configure system settings

### Doctor
- View appointment schedule
- Manage patient appointments
- Update profile information

### Patient
- Book appointments with doctors
- View personal appointment history
- Manage profile information

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript, Tailwind CSS
- **Backend**: PHP
- **Database**: MySQL
- **Authentication**: Secure password hashing with PHP's password_hash()

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/edoc-doctor-appointment-system.git
   ```

2. Set up a local development environment:
   - Install XAMPP or similar PHP development environment
   - Start Apache and MySQL services

3. Database setup:
   - Create a new database in phpMyAdmin
   - Import the `edoc.sql` file to create tables and sample data

4. Configure database connection:
   - Update `connection.php` with your database credentials

5. Access the application:
   - Navigate to `http://localhost/your-folder-name/`

## Default Credentials

For testing purposes, use the following credentials:

- **Admin**: admin@edoc.com / 123
- **Doctor**: doctor@edoc.com / 123
- **Patient**: patient@edoc.com / 123

## Security Features

- Passwords are securely hashed using PHP's `password_hash()`
- CSRF protection implemented for forms
- Input validation and sanitization
- Session management for user authentication

## Responsive Design

The application features a responsive design that works well on:
- Desktop computers
- Tablets
- Mobile devices

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Thanks to all contributors who have helped develop and improve this system
- Special thanks to the open-source community for the tools and resources used in this project