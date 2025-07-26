# Project Setup Plan (Updated)

## Overview
This document outlines the plan for setting up and running the eDoc Doctor Appointment System with its database for testing purposes.

## Current Status Check
- XAMPP is not found on the system
- Docker is not available
- PHP is installed (version 8.4.5)
- MySQL client is not available

## Approach
Since XAMPP is not installed, we have these options:
1. Install XAMPP manually and follow the setup steps
2. Use PHP's built-in server for web serving and install MySQL separately
3. Install Docker Desktop and use the provided Docker configuration

## Recommended Approach: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/download.html
2. Install XAMPP to the default location (C:\xampp)
3. Start Apache and MySQL services through XAMPP Control Panel

## Alternative Approach: Manual Setup with PHP Built-in Server
1. Install MySQL Server separately
2. Use PHP's built-in server for serving the web files
3. Configure the database connection

## Database Setup (After Installing XAMPP)
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Create a new database called "edoc"
3. Import the `edoc.sql` file from the project

## Application Setup
1. Copy all project files to `C:\xampp\htdocs\edoc\`
2. Update `connection.php` with correct database credentials if needed
3. Access the application at http://localhost/edoc/

## Testing Plan
1. Test all user roles (admin, doctor, patient)
2. Verify database connectivity
3. Test appointment booking flow
4. Verify data persistence

## Credentials for Testing
- Admin: admin@edoc.com / 123
- Doctor: doctor@edoc.com / 123
- Patient: patient@edoc.com / 123