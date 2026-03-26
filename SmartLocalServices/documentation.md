# Smart Local Services Marketplace

## Short Project Explanation
The **Smart Local Services Marketplace** is a full-stack platform that bridges the gap between local service providers (like electricians, plumbers, and cleaners) and customers seeking reliable help. The system provides a seamless booking experience where customers can search for specific services, pick a convenient date, and leave verified ratings after the job is completed. Service providers get a comprehensive dashboard to list their offerings, manage their incoming bookings, and grow their local reach. Built with a Vanilla JS frontend and a PHP/MySQL backend, the application focuses on speed, accessibility, and ease of use.

## Business Model
The platform operates on a robust business model tailored for scalability:
- **Commission-Based Revenue:** The platform charges a 10% commission on every successfully completed booking.
- **Subscription Plans (Future Extension):** Premium providers can pay a monthly fee to be featured at the top of search results.
- **Lead Generation:** Selling high-quality leads to newly registered professionals looking for early traction.

---

## PPT Content (For Presentation)

### Slide 1: Title Slide
- **Title:** Smart Local Services Marketplace
- **Subtitle:** Connecting you with trusted local professionals
- **Presented By:** [Your Name/Team]

### Slide 2: Problem Statement
- Finding reliable local service providers (plumbers, tutors, technicians) is difficult and word-of-mouth is slow.
- Providers struggle to market themselves locally.
- Lack of trust and transparency in pricing and service quality.

### Slide 3: Our Solution
- A centralized marketplace connecting customers with verified providers.
- Real-time search and filtering.
- Secure, easy-to-use booking system.
- Transparent review and rating system ensuring high standards.

### Slide 4: Key Features
- **User Roles:** Customer, Provider, Admin
- **Service Listings:** Creating and managing localized service cards.
- **Booking Pipeline:** Pending -> Accepted -> Completed lifecycle.
- **Verified Reviews:** Strict 1-review-per-completed-booking policy.

### Slide 5: Tech Stack
- **Frontend:** HTML5, CSS3 (Custom Variables/Flexbox), Vanilla JavaScript (ES6+), Fetch API
- **Backend:** PHP 8+ (RESTful API architecture)
- **Database:** MySQL relational DB with cascading foreign keys
- **Environment:** XAMPP / WAMP localhost

### Slide 6: Database Entity-Relationship
- `Users` (1) to (N) `Services`
- `Users` (1) to (N) `Bookings`
- `Services` (1) to (N) `Bookings`
- `Bookings` (1) to (1) `Reviews` (Enforced constraint)

### Slide 7: Future Scope
- Integration with payment gateways (Stripe/PayPal)
- Real-time chat between providers and customers
- Google Maps API integration for exact location tracking
- Mobile app using React Native/Flutter

### Slide 8: Q&A
- Thank You!
- *Questions?*

---

## Viva Questions & Answers

**Q1: What architecture does this project use?**
**A1:** The project uses a decoupled client-server architecture. The frontend is built with purely vanilla HTML/CSS/JS which acts as the client. It communicates with a backend RESTful API built in PHP via HTTP requests using the JavaScript `fetch()` API.

**Q2: How do you handle password security in your database?**
**A2:** Plain text passwords are never stored. The PHP backend uses `password_hash()` with the `PASSWORD_DEFAULT` algorithm (usually bcrypt) to cryptographically hash the password before saving it to MySQL. During login, `password_verify()` is used to compare the hashes.

**Q3: How do you ensure that a user can only review a service once?**
**A3:** In the MySQL database schema, the `reviews` table has a `UNIQUE` constraint on the `booking_id` column. Additionally, the backend API first validates that the given booking actually belongs to the user and is marked as 'completed' before attempting the `INSERT`.

**Q4: What is the difference between `GET` and `POST` requests in your API?**
**A4:** `GET` requests are used to retrieve data without modifying the server state (e.g., `get_services.php`, `get_bookings.php`). `POST` requests are used to send sensitive data or create new records (e.g., `register.php`, `add_service.php`). 

**Q5: What are Foreign Keys and where did you use them?**
**A5:** Foreign keys are rules that enforce referential integrity between tables. For example, in the `services` table, `provider_id` is a foreign key referencing the `id` in the `users` table. If the provider is deleted, `ON DELETE CASCADE` ensures all their associated services and bookings are also removed safely.
