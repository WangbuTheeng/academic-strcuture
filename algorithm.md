# Project Algorithms

This document outlines the core algorithms used in the academic-struct project, particularly focusing on the multi-school architecture, data management, and user authentication.

## 1. Multi-School Architecture

The system is designed to support multiple schools within a single application instance. This is achieved through a combination of data isolation and context-aware services.

### 1.1. Data Isolation

Data isolation is critical to ensure that each school's data is kept separate and secure. The primary mechanism for this is the use of a `school_id` foreign key on all school-specific tables.

**Algorithm: Data Isolation Verification**

1.  **Identify Scoped Tables:** A predefined list of tables that contain school-specific data is maintained (e.g., `students`, `teachers`, `exams`).
2.  **Iterate and Verify:** For each school in the system, the `DataIsolationService` iterates through the scoped tables and performs the following checks:
    *   **`school_id` Column:** Verifies that the table has a `school_id` column.
    *   **Null `school_id`:** Checks for any records where `school_id` is `NULL`.
    *   **Cross-Contamination:** Ensures that there are no records in the table that belong to a different school.
3.  **Report Violations:** Any violations found are reported, allowing administrators to take corrective action.

### 1.2. School Context Management

The application maintains the current school's context within the user's session.

**Algorithm: Session-Based School Context**

1.  **Authentication:** When a school user logs in, the `SchoolAuthService` sets the `school_context` in the session to the user's `school_id`.
2.  **Context-Aware Queries:** A global query scope (`SchoolScope`) is applied to all relevant models. This scope automatically adds a `WHERE school_id = ?` clause to all queries, using the `school_context` from the session.
3.  **Super-Admin Access:** Super-admins do not have a `school_id` and can switch the `school_context` manually to manage different schools.

## 2. User Authentication

The system has two main authentication flows: one for super-admins and one for school-specific users.

**Algorithm: School User Authentication**

1.  **Credentials:** The user provides their school's unique code, their email address, and their password.
2.  **School Verification:** The `SchoolAuthService` first verifies that the school code is valid and the school is active.
3.  **User Authentication:** It then authenticates the user's email and password against the `users` table, ensuring that the user belongs to the specified school (`school_id`).
4.  **Session Setup:** Upon successful authentication, the `school_context` is set in the session, and the user is redirected to their respective dashboard.

**Algorithm: Super-Admin Authentication**

1.  **Credentials:** The super-admin provides their email and password.
2.  **User Verification:** The `SchoolAuthService` authenticates the user, verifying that they have the `super-admin` role and that their `school_id` is `NULL`.
3.  **Session Setup:** A session variable `is_super_admin` is set to `true`, granting them access to the super-admin dashboard.

## 3. School Provisioning

New schools can be created by super-admins. The `SchoolCreationService` handles the entire provisioning process.

**Algorithm: New School Creation**

1.  **Input Data:** The service takes the school's name, desired password, and other details as input.
2.  **Generate School Code:** A unique, human-readable code is generated for the school based on its name.
3.  **Create School Record:** A new record is created in the `schools` table.
4.  **Create Admin User:** A default administrator user is created for the new school.
5.  **Initialize Structure:** The `SchoolSetupService` is called to create default academic structures for the new school (e.g., default grading scales, academic years).
6.  **Create Statistics Record:** An initial record is created in the `school_statistics` table to track key metrics for the school.
7.  **Audit Log:** The creation of the new school is logged for auditing purposes.

## 4. Unique ID Generation

The system needs to generate unique IDs for various records, such as student admission numbers.

**Algorithm: Unique Admission Number Generation**

1.  **Prefix and Year:** The admission number is prefixed with a standard string (e.g., "ADM") and the current year.
2.  **Find Last Number:** The `DataIsolationService` queries the `students` table to find the last admission number generated for the current school in the current year.
3.  **Increment:** The numeric part of the last admission number is incremented by one.
4.  **Format:** The new number is formatted with leading zeros to ensure a consistent length.
5.  **Uniqueness Check:** An additional check is performed to ensure that the generated number is truly unique before it is assigned to a new student.
