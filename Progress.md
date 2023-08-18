## 7/8/2023 - User CRUD

Following endpoints were created

-   Fetch all users `GET /api/v1/users`
-   Fetch single user `GET /api/v1/users/{id}`
-   Create new user `POST /api/v1/users`
-   Update single user `PATCH /api/v1/users/{id}`
-   Delete user `DELETE /api/v1/users/{id}`

## 8/8/2023 - Agency | Creative CRUD

Following endpoints were created

-   Fetch all agencies `GET /api/v1/agencies`
-   Fetch single agency `GET /api/v1/agencies/{id}`
-   Create new agency `POST /api/v1/agencies`
-   Update single agency `PATCH /api/v1/agencies/{id}`
-   Delete agency `DELETE /api/v1/agencies/{id}`

---

-   Fetch all creatives `GET /api/v1/creatives`
-   Fetch single creative `GET /api/v1/creatives/{id}`
-   Create new creative `POST /api/v1/creatives`
-   Update single creative `PATCH /api/v1/creatives/{id}`
-   Delete creative `DELETE /api/v1/creatives/{id}`

---

Config file for global settings

-   Datetime format `2023-08-08 19:08:02` `(Y-m-d H:i:s)`

## 9/8/2023 - Job | Application | Resumes| Link | Phone | Address | CRUD

Following endpoints were created

-   Fetch all jobs `GET /api/v1/jobs`
-   Fetch single job `GET /api/v1/jobs/{id}`
-   Create new job: `POST /api/v1/jobs`
-   Update single job: `PATCH /api/v1/jobs/{id}`
-   Delete job: `DELETE /api/v1/jobs/{id}`

---

-   Fetch all applications: `GET /api/v1/applications`
-   Fetch single application: `GET /api/v1/applications/{id}`
-   Create new application: `POST /api/v1/applications`
-   Update single application: `PATCH /api/v1/applications/{id}`
-   Delete application: `DELETE /api/v1/applications/{id}`

---

-   Fetch all resumes: `GET /api/v1/resumes`
-   Fetch single resume: `GET /api/v1/resumes/{id}`
-   Create new resume: `POST /api/v1/resumes`
-   Update single resume: `PATCH /api/v1/resumes/{id}`
-   Delete resume: `DELETE /api/v1/resumes/{id}`

---

-   Fetch all links: `GET /api/v1/links`
-   Fetch single link: `GET /api/v1/links/{id}`
-   Create new link: `POST /api/v1/links`
-   Update single link: `PATCH /api/v1/links/{id}`
-   Delete link: `DELETE /api/v1/links/{id}`

---

-   Fetch all phone numbers: `GET /api/v1/phones`
-   Fetch single phone number: `GET /api/v1/phones/{id}`
-   Create new phone number: `POST /api/v1/phones`
-   Update single phone number: `PATCH /api/v1/phones/{id}`
-   Delete phone number: `DELETE /api/v1/phones/{id}`

---

-   Fetch all addresses: `GET /api/v1/addresses`
-   Fetch single address: `GET /api/v1/addresses/{id}`
-   Create new address: `POST /api/v1/addresses`
-   Update single address: `PATCH /api/v1/addresses/{id}`
-   Delete address: `DELETE /api/v1/addresses/{id}`

---

## 10/8/2023 - Attachment | Notes | CRUD

-   Seeders for
    -- Categories
    -- Industries
-   Global Exception handling
-   Cache Implementattion
-   Notes CRUD Endpoints
-   Attachments CRUD Endpoints

## 11/8/2023 - Bookmarks | Educations | Experiences | Categories | Industries CRUD

-   Bookmarks API Endpoints
-   Educations API Endpoints
-   Experiences API Endpoints
-   Categories API Endpoints
-   Industries API Endpoints

## 15/8/2023 - Filters On All Entities

-   Users ( Agencies | Creatives )
-   Jobs
-   Applications
-   Notes
-   Resumes
-   Educations
-   Experiences
-   Atachments
-   Links
-   Phones
-   Addresses
-   Bookmarks
-

## 16/8/2023 - Authentication & Authorization

-   All the endpoints require token based authentication except `registration` and `login`
-   `/login` will give token that can be used in all subsiquent requests.
-   Roles and permissions created for critical entities and assigned to appropriate roles.
-   New registered user will acquire all the permissions based on its role.
-   Following permissions will be check based on role
-   `Agency` role can:
    -- `create` `update` `delete` agencies , jobs and other common entities
-   `Creative` role can:
    -- `create` `update` `delete` creatives , applications, resumes, eduaction, experience and other common entities
-   `Advisor` role user has all the permissions of `agency` , `creative` and other common entities.

#### Common entities:

-   Phones
-   Addresses
-   Links
-   Attachments
-   Bookmarks
-   Notes
-

## 17/8/2023 - Admin Dashboard

-   Login form for admin
-   Admin can
    -- Create new users
    -- Update existing users (password, status, role etc)
    -- View all users
    -- Apply filters on Users (role, status, email, username)
    -- Pagination (Previous | Next page)
    -- Per Page Filter (10 | 20 | 50 PerPage)
-   With proper toast notifications and error alerts
