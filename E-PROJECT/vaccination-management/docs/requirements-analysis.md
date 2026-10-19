# Requirements Analysis — Vaccination Management System

## Problem definition
Manual vaccination coordination can result in missed doses, scattered child records, and time-consuming appointment coordination. The proposed web application centralises child profiles, hospital listings, appointment requests, vaccine availability, and completed vaccination records.

## Actors and permissions
| Actor | Main permissions |
|---|---|
| Administrator | View child profiles and bookings; approve/reject appointment requests; maintain hospital status and vaccine catalogue; view summary counts. |
| Parent | Register/login; create child profiles; request appointments; see booking status and completed vaccination history for their own children. |
| Hospital | Login; view bookings assigned to its own hospital; mark approved bookings completed; update configured vaccine availability. |

## Functional requirements
1. User registration and login with role-specific access.
2. Parent can add child details: name, date of birth, gender, guardian phone, and optional notes.
3. Parent can view active hospitals and available vaccines and request a date/time.
4. Admin can view appointment requests and approve/reject pending requests.
5. Admin can maintain vaccine names, dose labels, recommended-age text, and global availability.
6. Admin can view child and hospital listings and activate/deactivate hospital profiles.
7. Hospital can view its assigned appointments and mark an approved appointment vaccinated.
8. Completing an appointment creates a vaccination history record visible to the parent.
9. Dashboard summary counts provide a quick overview.

## Non-functional requirements
- Responsive interface for desktop and mobile.
- Server-side validation for all state-changing requests.
- Passwords stored as hashes; role checks on protected pages.
- PDO prepared statements, output escaping, and CSRF tokens for POST forms.
- Relational database with foreign keys and useful indexes.
- Maintainable separation of configuration, shared functions, role pages, assets, and SQL schema.

## Core workflow
Parent registration → add child → select active hospital/vaccine → request appointment → admin approval/rejection → hospital completes approved appointment → vaccination record visible in parent portal.

## Entity relationship overview
- `users` 1-to-many `children` (parent owns child profiles).
- `users` 1-to-0/1 `hospitals` (hospital login can be linked to a hospital profile).
- `children`, `hospitals`, and `vaccines` each relate to many `bookings`.
- `hospitals` and `vaccines` have a many-to-many relationship through `hospital_vaccines`.
- One completed `booking` can have one `vaccination_records` row.

## Assumptions and boundaries
This is a classroom prototype based on the supplied specification. Hospital profile approval is assisted by the administrator/database setup. Notifications are dashboard/status based; email/SMS delivery is not implemented. Vaccine catalogue descriptions and ages are sample data and must not be treated as a medical schedule.
