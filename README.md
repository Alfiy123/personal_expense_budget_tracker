# PennyPlan — Personal Expense and Budget Tracker

A student project built with HTML, CSS, JavaScript, PHP, MySQL, XAMPP, and Git.

## Features
- User registration and login
- Password hashing and PHP sessions
- Add income and expense transactions
- Monthly budget setting and spending progress
- Monthly income and expense totals
- Recent transaction list and delete action
- Server-side validation and prepared SQL statements
- Each account can see only its own transactions

## Requirements
- XAMPP with Apache, PHP, and MySQL/MariaDB
- Git
- A web browser

## Installation (XAMPP on Windows)
1. Copy the `personal_expense_budget_tracker` folder into `C:\\xampp\\htdocs\\`.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Visit `http://localhost/phpmyadmin`.
4. Choose **Import**, select this project's `database.sql`, then click **Import/Go**. The SQL creates the `expense_tracker` database and tables.
5. Check `config/db.php`. The default XAMPP settings are host `localhost`, database `expense_tracker`, username `root`, and an empty password. Change these if your local MySQL credentials differ.
6. Open `http://localhost/personal_expense_budget_tracker/` in your browser.
7. Register a test account, set a monthly budget, and add sample income/expense transactions.

## Project structure
- `index.php` — redirects to dashboard or login
- `register.php`, `login.php`, `logout.php` — authentication
- `dashboard.php` — totals, budget progress, transaction history, add form
- `budget.php` — monthly budget form
- `save_transaction.php`, `delete_transaction.php` — transaction actions
- `config/db.php` — PDO database connection
- `includes/auth.php` — login protection and HTML escaping helper
- `assets/style.css` — responsive styles
- `assets/app.js` — browser form validation
- `database.sql` — database schema

## Git setup and commit plan
Run these commands in the project folder. Make each commit after completing and testing that milestone.

```bash
git init
git add .
git commit -m "2026-08-18: Created project structure"

git add .
git commit -m "2026-08-19: Added login page"

git add .
git commit -m "2026-08-20: Implemented form validation"

git add .
git commit -m "2026-08-21: Connected database"

git add .
git commit -m "2026-08-22: Fixed authentication issue"
```

These messages label the milestones; they do not set the actual commit timestamps. Use real commit dates for honest history. To check history, run `git log --oneline`.

## Test checklist
- [ ] Register with a new email.
- [ ] Try registering the same email again; it should show an error.
- [ ] Log out and log in with the correct password.
- [ ] Try a wrong password; access should be denied.
- [ ] Set a monthly budget.
- [ ] Add an expense and check the dashboard totals.
- [ ] Add income and check the income total.
- [ ] Try an empty or invalid amount; it should not be saved.
- [ ] Delete a transaction.
- [ ] Register a second account and confirm it cannot see the first account's transactions.

## Notes
This is a local learning project, not a production-ready financial service. Before public deployment, add CSRF protection, HTTPS, stricter session-cookie settings, rate limiting, and production database credentials. Do not upload real personal financial data to a test deployment.
Project maintained by Alfiya Asre.