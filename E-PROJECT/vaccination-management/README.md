# NurtureDose — Vaccination Management System

A PHP + MySQL full-stack educational project based on the supplied Vaccination Management System requirement document.

## Features implemented
- Role-based accounts: administrator, parent, hospital.
- Password hashing with PHP `password_hash()` / `password_verify()`.
- Session authentication, session ID regeneration, role guards, CSRF tokens, output escaping, and PDO prepared statements.
- Parent dashboard: create child profiles, request an appointment at an active hospital, track booking statuses, and view completed vaccination records.
- Admin dashboard: view key counts, review and approve/reject booking requests, manage vaccine catalogue and availability, activate/deactivate hospital listings, and view child profiles.
- Hospital dashboard: view appointments assigned to its linked hospital, mark approved appointments as vaccinated, and manage its configured vaccine availability.
- MySQL schema with foreign keys, indexes, unique constraints, and illustrative starter vaccine entries.
- Responsive frontend with landing page, login/register forms, dashboard cards, tables, and mobile styles.

## Requirements
- PHP 8.1+ with PDO MySQL enabled
- MySQL 8+ or a compatible MariaDB version
- Apache (XAMPP is suitable)
- A modern browser

## Installation (XAMPP on Windows)
1. Extract the `vaccination-management` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Import `database/schema.sql` using the Import tab. It creates the `vaccination_management` database and starter vaccine catalogue.
5. Check `config/config.php`. The default XAMPP MySQL settings are host `127.0.0.1`, user `root`, blank password. Change these if your local setup differs.
6. Open `http://localhost/vaccination-management/create_admin.php`, create an administrator using a strong password (12+ characters), then **delete `create_admin.php`** from the project directory.
7. Sign in at `http://localhost/vaccination-management/login.php`.

## First-run data setup
The schema deliberately does not seed a known/default admin password. Create it through the one-time helper.

### Add a hospital record
Hospital accounts created through the public registration form start as pending. For this project prototype, an administrator/developer must create the hospital profile and link it to the hospital account's `users.id`. For example, after a hospital account is registered, find its ID in phpMyAdmin and run a query like:

```sql
INSERT INTO hospitals (user_id, name, address, city, phone, email, status)
VALUES (2, 'Community Health Hospital', '12 Main Road', 'Karachi', '021-0000000', 'hospital@example.com', 'active');
UPDATE users SET status='active' WHERE id=2 AND role='hospital';
```

Replace the sample `user_id`, name, address, phone, email, and city with your own test data. Do not use real patient data for a classroom demo.

### Configure vaccines for a hospital
Use the hospital's ID and vaccine IDs from phpMyAdmin:

```sql
INSERT INTO hospital_vaccines (hospital_id, vaccine_id, status)
VALUES (1, 1, 'available');
```
Repeat for each vaccine that hospital offers. Parent booking currently validates that a vaccine is globally available and the hospital is active; for a production deployment, add a hospital-specific availability check to the booking action.

## User workflow
1. Parent registers and signs in.
2. Parent creates a child profile.
3. Admin/developer adds and activates a hospital and configures its vaccines.
4. Parent submits an appointment request.
5. Admin approves or rejects the request.
6. Hospital signs in and marks an approved appointment as vaccinated.
7. The booking status changes to `completed` and a vaccination record appears in the parent's history.

## Database entities
- `users`: credentials, role, and account status.
- `children`: child demographics owned by a parent account.
- `hospitals`: provider details linked optionally to a hospital login.
- `vaccines`: vaccine catalogue and global availability.
- `hospital_vaccines`: vaccines offered by each hospital.
- `bookings`: appointment requests, schedule, and workflow status.
- `vaccination_records`: completed vaccination history linked to a booking.

## Project limitations / future improvements
- Hospital registration-to-profile onboarding is admin-assisted; build a dedicated approval form for production.
- Add editing/deletion for child and hospital profiles, notification delivery, calendar reminders, pagination, report export, audit logging, and stronger appointment slot conflict checks.
- Add hospital-specific vaccine availability enforcement to the booking action.
- Use HTTPS, environment-based secrets, rate limiting, backups, least-privilege database credentials, and privacy/legal review before any real deployment.
- Vaccine ages and seed entries are illustrative, not medical advice. Follow the local official immunisation schedule and qualified healthcare professionals.
- This is an educational prototype, not a certified clinical system.

## Submission checklist
- [ ] Import the SQL schema.
- [ ] Create admin and delete the one-time setup helper.
- [ ] Add test hospital data and hospital vaccine links.
- [ ] Test all three roles with non-real sample data.
- [ ] Include screenshots and your own project documentation for your institute's required format.


## Updated access rules and certificates
- Public registration creates **parent accounts only**. The registration form does not expose an admin or hospital role selector.
- Parent sign-in (`login.php`) accepts active parent accounts only.
- Administrator sign-in is separated at `admin-login.php` and checks for the `admin` role on the server.
- Create the first admin with `create_admin.php` only after importing the schema, then remove that helper file. Never share the admin credentials.
- After a hospital marks an appointment complete and a vaccination record exists, the parent can open **View certificate** in the vaccination history table. Use **Print / Save as PDF** to save an A4 certificate. The certificate is a system record, not a substitute for a provider-signed medical document.
- For this student project, hospital account provisioning should be performed by an administrator rather than public self-registration.


## Premium edition additions
- Polished landing page sections for the care journey, vaccine guide, role-specific portals, and FAQs.
- Admin appointment pipeline visualisation generated from live booking counts.
- Admin-only CSV exports for appointment data and completed vaccination records (`admin/report.php`).
- Quick-search controls are added automatically above dashboard tables; these filter the rows currently displayed on that page.
- Parent booking validation checks hospital-specific vaccine availability when configured and warns if a hospital time already has a pending/approved request.
- Responsive layouts for phones, tablets, and desktop.

## Update an existing XAMPP installation safely
1. Stop editing and make a backup copy of your current `vaccination-management` folder.
2. Extract this ZIP. Open the extracted `vaccination-management` folder.
3. Copy its contents into your existing `C:\xampp\htdocs\E-prjoect\vaccination-management\` folder and choose **Replace files in destination** when Windows asks.
4. Do not delete or re-import your existing database just to update the design. Keep your existing database unless you intentionally want a fresh setup.
5. Start Apache and MySQL, then refresh the site with `Ctrl + F5`.
6. Confirm `create_admin.php` has been deleted after you create your admin account. If it is still present, remove it after setup.

## Notes
- CSV reports contain system data, so only use the admin account and only export fictional classroom-demo records.
- The search box filters records rendered on the current page; it is not server-side pagination.
- Hospital-specific availability is enforced when an entry exists in `hospital_vaccines`. If a hospital has not been configured for a vaccine yet, the global vaccine availability remains the fallback for this educational prototype.
