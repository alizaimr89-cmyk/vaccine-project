# Manual test checklist

Use synthetic data only. Check each item after testing on your own XAMPP/MySQL environment.

- [ ] Import `database/schema.sql` without SQL errors.
- [ ] Create the first administrator and remove `create_admin.php`.
- [ ] Confirm a parent can register, sign in, and sign out.
- [ ] Confirm duplicate email registration is rejected.
- [ ] Confirm invalid credentials do not sign in.
- [ ] Confirm a parent can add a child with a valid date of birth.
- [ ] Confirm future dates can be selected for appointments and past dates are rejected server-side.
- [ ] Add a hospital and link a hospital login from the admin portal.
- [ ] Configure at least one hospital vaccine.
- [ ] Confirm a parent can request an appointment.
- [ ] Confirm admin can approve and reject a pending appointment.
- [ ] Confirm hospital only sees appointments for its own hospital.
- [ ] Confirm an approved appointment can be marked vaccinated once.
- [ ] Confirm completed record appears in the parent's vaccination history.
- [ ] Confirm role-protected URLs return 403 or redirect when accessed by the wrong role.
- [ ] Check layout at desktop and mobile widths.
