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

## 14/8/2023 - Worked on Adeel bhai's personal project

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

## 18/8/2023 - Admin Dashboard | Job Module

Admin can

-   View all jobs
-   Delete any job
-   Apply different filters on the jobs
    -Title
    -Category
    -Employement Type
    -Industry Experience
    -Media Experience
    -Labels (Featured, Remote, Urgent etc)
-   See all the applications sent on that particular job
-   Download Resumes attached by candidates

## 21/8/2023 - Admin Dashboard | Stat Widgets

-   Admin can update user status
-   Admin can approve or reject job status
-   Admin can also filter jobs based on Status(Pending, Approved, Expired, Filled)
-   Admin can also view agency or creative info by clikcing on detail button
-   Dashboard
    -- User Stats Widgets
    -- Job Stats Widgets
    -- Chart for last 7 days registered users
    -- Chart for last 7 days posted jobs

## 22/8/2023 - Stripe Integration

-   User can purchase the plan
-   User can cancel the subscription
-   List all current subscriptions

## 23/8/2023 - Managing Quota For User's Plan

-   Default Job status set to "Draft"
-   Agency user can publish the job
-   While changing status from draft to published, Checking if he has quota remaning?
-   If no quota remaining
    -   Dont allow job publishing
-   If has quota:
    -   Allow job publishing
    -   Decrease the quota value for that user's plan
-   Dashboard widget for Sales (package wise)
-   Last 7 days sale graph
-   Order page:
    -   Admin can see all the orders on single page with their corresponding user and amount

## 24/8/2023 - Community | Groups

-   Created Migration and Models for
    -   Groups
    -   Group Members
    -   Posts
    -   Likes
    -   Comments
-   API Endpoint for listing all groups (with status and name filter)
-   Admin Can:
    -   Create New Group
    -   See Details of Group
    -   Update the group (status, name, description, cover_image)
    -   Delete the group

## 25/8/2023 - Admin | Posts

-   API Endpoint for Posts
-   Admin can:
    -   See all Posts
    -   Update post status
    -   Delete the post
    -   Filter posts by:
        -   Group
        -   Author
        -   Status
-   Admin can:
    -   Add members to group
    -   See all members of group

## 28/8/2023 - Admin | Comments | Likes

-   API Endpoints for
    -   Comments
    -   Likes
-   Admin can:
    -   Update role of group member
    -   Can see all the comments of post (along with comment authors) on single post page
    -   Can see all the attachments of post.
    -   Can see total likes on the post

## 29/8/2023 - Reviews

-   Endpoint for Reviews:
    -   Agency or Creative can put refiew on each other
    -   You can filter the reviews by target entity (e.g. Agency User ID, Creative User ID)
    -   You will also get average rating in the response

## 30/8/2023 - Dashboard | Jobs detail page | Locations

-   Admin Dashboard:
    -   Added more fields on job detail page
    -   Separate dropdows links for Creative and Agencies
    -   Locations module added, admin can:
        -   Create New States and View Existing states
        -   Create New Cities within specific State and View Existing States

## 31/8/2023 - Agency | Creative Extra Fields

-   Admin Dashboard:
    -   Can see advisors with different dropdown menu
    -   Extra Fields in agency/advisor profile
    -   Extra fields in creative profile

## 01/9/2023 - Agency | Job Add/Update | Categories

-   Admin Dashboard:
    -   Can see advisors, creatives and agencies as separate menu options
    -   Can add new job
    -   Can update the Job (with more fields)
    -   Can manage categories (View | Update | Delete)

## 04/9/2023 - Dashboard | Taxonomies

-   Admin can manage:
    -   Industry Experiences
    -   Media Experiences
    -   Years of Experiences
-   Separate Add and View page for all taxonomies
-   API endpoints for Industy, Media and Years of Experience

## 05/9/2023 - Dashboard | Static to dynamic dropdowns for Taxonomies

-   Separate endpoints for industry & mdedia experiences
-   Years of experience is dynamic (from backend)
-   Few extra fields added in agency profile
-   Some visual fixes
-   Logs endpoint for viewing error logs for admin e.g `/logs`
-   Implemented AWS S3 bucket for storing images

## 06/9/2023 - Dashboard | More meta fields in agency, creatives and job

-   Agency meta fields for making featured and also added workplace preference
-   Creatives meta field for making featured, urgent, remote and relocation.
-   Modified database structure for handling data more efficiently.
-   Job meta fields added for workplace preference, adding location (state and city)
-   Added location and sub-agency info in jobs API

## 07/9/2023 - Dashboard | Bug Fixes | Enhancements | Activity Logs

-   Modified few fields in agency | creatives | advisors and jobs
-   Some bug fixes on job detail page
-   Corrected the image upload process for job and groups
-   Added more fields in jobs, agency and creatives API
-   Added Telescope for activity logs

## 08/9/2023 - Chat Funtionality

-   API endpoint for sending new message.
-   API endpoint for all related users
    -   (with name and last one message to show in left panel of messaging screen
    -   from where we select any user and start the chat).
-   Created frontend which will be used by admin to view messages of all the users.

## 11/9/2023 - Chat Functionality

-   API endpoints for chats
-   Created impersonating route

## 12/9/2023 - Dashboard | Strengths CRUD

-   API endpoints for all strengths(tags).
-   Added strengths module in admin dashboard
-   Added strengths field in creatives profile
-   Setup email on admin action (single email for now)

## 13/9/2023 - Extra fields

-   Added `open_to_relocation` and `open_to_remote` in jobs section
-   Added `workplace preference` in creatives profile
-   Setup Emails with Queues

## 14/9/2023 - Reset Password | Group Invitation

-   API endpoint which will send email to the user conatining the reset link
-   API endpoint which will allows user to enter new password alongwith token
-   API endpoint to allow ther User to send group invitation to other user
-   API endpoint to update the status of invitation, after that user will become member of that group
-   API endpoint to get all the invitations [with filter of user and status]

## 15/9/2023 - Advisor permission | Extra fields

-   Added new fields in jobs api endpoint
-   Added new fields in creatives api endpoint
-   Setup permission level for advisor role for different pages.
-   Sending email to admin when user purchases new order/package
-   Added character strengths field in jobs section

## 18/9/2023 - Location | Filters

-   Added location dropdown for creatives and agencies in admin dashboard
-   Upload profile Image for Creatives
-   Added location field in both (agencies | creatives)
-   Applied filters on agencies (name, location)
-   Applied filters on creatives (name, location, title)

## 19/9/2023 - Agency Filters | Job Alert Subscription

-   Created endpoint for users
    -   To subscribe to any category for receiving emails
    -   To enable/disable the email alert for new jobs
-   Applied filters on agencies (industry and media experiences)
-   Job alert to admin when new job is posted
-   Job alert to all the subscribers who have subscribed to any category when new job is posted

## 20/9/2023 - SEO

-   Admin can:
    -   Update SEO (title, description, keywords) for creatives
    -   Update SEO (title, description, keywords) for agencies
    -   Update SEO (title, description, keywords) for jobs
    -   Update SEO (title, description, keywords) for home page (Generic SEO)
-   Created endpoint for getting generic SEO for homepage
-   Included SEO tags in API response of agencies, creatives and jobs

## 21/9/2023 - SEO | Addtional fields in API

-   Added dynamic tags in SEO so that admin can easily configure SEO titles
-   Modify the registration flow to comply with need.

## 22/9/2023 - SEO | Addtional fields in API

-   Admin can set default SEO title for jobs and creatives
-   Add slug in jobs table, it will be auto-generated from different values of job table
-   Created endpoint for getting user subscription status, it will also show how many jobs user can post
-   Collaborated with frontend-dev to make proper flow by modifying API response (where needed)

## 25/9/2023 - Creative Spotlight

-   Admin can set default SEO title for creatives spotlight
-   Created endpoint which lists all the creatives spotlights along with their urls
-   Transformed creative title field from open text box to specific dropdown

## 26/9/2023 - Job Invitation | Creative Title field

-   Admin can set title (open text field) and also select from industry job title (dropdown).
-   Separate sections for showing portfolio items and spotlights.
-   Job Invitation funtionality for agency users.

## 27/9/2023 - Media page

-   Admin can see all the media attachments uploaded by all users
-   Can Apply all filter to
    -   Select medias for particular user
    -   Also for particular media type e.g.`logo`, `profile_image` etc

## 28/9/2023 - Media page | Coupons

-   Admin can
    -   Update the status of media attachment
    -   Can delete the media attachment
    -   Filter the attachments based on their status
-   Enhanced payment flow a little bit (in progress)

## 29/9/2023 - Dynamic Content for Pages

-   Admin can
    -   Update the content of folowing pages:
    -   Home
    -   Community
    -   Footer
-   Enhancments in the payment flow

## 02/10/2023 - Assign advisor to Custom Job Request

-   Admin can
    -   View all the custom job requests.
    -   Update the status to accepted or rejected.
    -   Assign any advisor to that job request.
    -   Assign any package to that request.
-   API endpoint that will store the custom job reuqest into database.

## 03/10/2023 - Assign advisor to Custom Job Request

-   Admin can
    -   Enter the text in text editor
    -   Can also upload image in that editor even by dragging image instead of selecting.
-   Custom Job Request form fields updated for API as well as for admin panel.

## 04/10/2023 Data Import

-   Created endpoint for advisor where he can see all the assigned agencies.
-   Created endpoint with the help of that he can impersonate the agency profile.
-   Imported users data from WordPress

## 05/10/2023 Data Import (Agecnies | Creatives)

-   Fixed endpoint for media experience filter to work according to jobs.
-   Fixed endpoint for industry experience filter to work according to jobs.
-   Working on importing data for agencies and creatives

## 06/10/2023 Password Configuration | Filters Improvements

-   Fixed few filter endpoints.
-   Added some new keys in the resposne e.g. job api, agency and creative api
-   Make them filterable by slug
-   Fixed Password Update Bug
-   Configured the Laravel to use same wordpress password for logging into new react app

## 09/10/2023 Industry | Media Experience | Admin Dashboard Login

-   Admin now can login with wordpress password.
-   Non-admin users will not be even login to the admin dashboard.
-   Make Employment Type dropdown to select multiple values.
-   Industry | Media experiences are exported from wordpress.
-   Title (Free Text) is exported from wordpress.

## 10/10/2023 Education | Experience | Bug Fix

-   Exported education and experience for creatives
-   Exported Location for creatives
-   Location dropdown bug fixed
-   Added open_jobs field in agencies endpoint

## 11/10/2023 API enhancements

-   Agency Profile update endpoint
-   Agency Logo fixed
-   Added logo field in login route
-   Added location_id in job, creatives and agencies alongwith location name
-   Added test user (creative and agency) so that we don't have to create all details of particular user for testing.
-   Exported views count for creatives and agencies

## 12/10/2023 Jobs | API enhancements

-   Agency Profile update endpoint
-   Jobs exported (published and Expired status)
-   Bug Fix(industry and media experience dropdown) on job detail page

## 13/10/2023 Jobs | API enhancements

-   Creative Profile update endpoint
-   Fixed few bugs on All Jobs page
-   In job detail page (admin dashboard) put some error handing (where we noticed)
-   Job Update endpoint updated to work with latest set of fields

## 16/10/2023 STRIPE

-   Enhanced stripe payment flow
-   Setup stripe payment webhook so that we get the details of user and its purchased pakcage
-   Endpoint for getting all the purchased packages of user
-   Password Update endpoint added

## 17/10/2023 User Media Export

-   Created script for exporting
    -   Profile Pictures
    -   Agency Logo
    -   Creative Spotlights
    -   Portfolio Items
    -   Resume
-   Admin can filter the attachments with email

## 18/10/2023 Bug Fixes

-   Media exported fixed.
-   Audit the admin dashboard and fixed few bugs.
-   Audit the frontend side and reported few bugs and fixed.
-   Group invitation email

## 19/10/2023 Chat | Bookmarks

-   Bookmarks API enhanced to include all the relevant fields.
-   In Chat API
    -   In contacts endpoint, added the user resource and its image along with latest one message.
    -   Also added field message_type, it tells the frontend whether it is received or sent message(with respect to currently logged in user).
    -   In messages endpoint, also added same message_type field, and sorted the messages response (older at the top, latest at the end).
-   Figured out , why chat funtionality is not working.

## 20/10/2023 Notes | Queues

-   `Notes API` enhanced to include all the relevant fields and to work for all entities.
-   Applied relevant filters on `Notes`.
-   Applied relevant filters on `Bookmarks`.
-   Successfully setup `Queues` on the server for emails.
-   Working on Websockets configurations on server (in progress)

## 23/10/2023 API endpoints enhancement

-   Split the profile endpoint in two separate endpoints (for creative)
    -   One for basic information e.g. `phone number`, `linkedin` .
    -   Other for resume fields.
-   Added author profile picture in posts.
-   Configured post images filter on attachamnets route.
-   Map the IP addresses of laravel server and react frontend to test domain name,
    -   So that websockets can works correctly.
-   Still working on Websockets configurations on server (in progress)

## 24/10/2023 Resume | Jobs

-   Created endpoint for applied jobs (for creative).
-   Make resume template with dynamic values.
-   Still working on Websockets configurations on server (in progress)

## 25/10/2023 Friendship Module

-   Created endpoint for sending friend request.
-   FriendRequest email configured to sent.
-   Created endpoint for accepting or rejecting the friend request.
-   FriendRequest Accepted email configured to sent.
-   Added User_id in the groups table, so that any user can create the group instead of just admin.

## 26/10/2023 Account Emails | API Endpoints

-   Modified the endpoint for education and experience (for creative user).
-   Modify the posts endpoint to include atleast 3 comments by default.
-   Made the following emails ready:
    -   Agency User Registration
    -   Creative User Registration
    -   Account Approved
    -   Account Rejected
    -   Reset Password Email

## 27/10/2023 API Endpoint fixed (community section)

-   Added user resource in comments.
-   Included comments in post resource.
-   Created endpoint for `my_friends` .
-   Worked on Pusher Bug Fix.
-   Added author_slug in posts endpoint.

## 30/10/2023 Website Review | Data Import

-   Reviewed the website from frontend and noted all the bugs and inconsistencies.
-   Fixed few bugs from admin side.
-   Created endpoint for fetching notificaitons.
-   Created endpoint for updating notifications.
-   Exported the data from Wordpress to Laravel (with all attachments)

## 31/10/2023 Data Import Bug Fixes

-   Added profile picture on media page
-   Manually Confirmed profile pictures of users
-   Handeled the exceptions which came during data export
-   Bug Fixes

## 01/11/2023 Portfolio website previews

-   Added profile picture section for admin profile.
-   Created endpoint for trending posts.
-   Exported existing portfolio website previews.
-   Added command for storing preview of website for upcoming users.
-   Added portfolio website on Resume.

## 02/11/2023 Notifications | Friend Request Module

-   Implemented application submitted email.
-   Created endpoints for notificaitons module (Full CRUD)
-   Created endpoints for activities module (Full CRUD)
-   Modify the friendship module endpoints accordingto frontned requirements
-   Tested the profile page from frontend and noted all the things which we can improve.

## 03/11/2023 CreativeSpotlights

-   Added option in admin dashboard for managing creative spotlights.
-   Admin can add new spotlight, also can select the user and assign the spotlight.
-   Admin can update the title , slug and user of the spotlight.
-   Created endpoint for frontend along with filters (status accepted, rejected)

## 06/11/2023 Package Management | Creative Permission

-   Added option in admin dashboard for managing agency packages.
-   Enhanced the Friendships api module for better user experience.
-   Removed the user from creative spotlights page (also from DB).
-   Creative phone number and email are subject to special conditions based on logged-In user role.

## 07/11/2023 Reviews | Permissions

-   Setup condition for avoiding multiple reviews.
-   Giving review second time will update the exisitng review.
-   Added package status field in creatives api
-   Creatied separate route for creatives endpoint for logged in users
-   Created featured cities endpoint
-   Sorted the creatives with featured and then by latest.

## 08/11/2023 Agencies Export | Reviews

-   Analyzed the Agency profiles and manually update their status to inactive.
-   On frontend, set featured and active filter for agencies.
-   Mark as read endpoint for messages.
-   Set condition in backend for avoiding giving review to itself.
-   Update and delete endpoint for review.
-   Added friendship status in creatives resource.
-   Logged in user can see their own email and phone on their own detail page

## 09/11/2023 Unread Message EMail

-   Created api module for managing group members.
    -   Group Member can be added.
    -   Can be removed from group.
    -   Its role can be updated in group.
    -   We can fetch groups members of particular group with filter (group filter and role filter)
-   Setup Unread Message email.
-   Creative Dashboard Stats

## 10/11/2023 Emails | Stats | Bug Fixed

-   Fixed creative dashboard stats widgets
-   Created endpoint for lounge stats widgets.
-   Fixed notification endpoint for job board.
-   Fixed recent applications endpoint.
-   Fixed bug on account related emails.

## 13/11/2023 Unread Message Count Email | Approve - Deny links

-   Modify the unread message according to requirement to include profile image of sender.
-   Added Approve and Deny links in registration email so that admin can perform these actions directly.
-   Fixed account related emails.
-   Figured out the reviews bug.

## 14/11/2023 Creative Full Search API

-   Created separated search api endpoints for differet user roles with different permisisons.
-   A little tweak in the applications endpoint.
-   Added filter in bookmarks endpoint.

## 15/11/2023 Post Reaction | Creative Categories

-   Create endpoints for post reactions.
-   Post reaction module is designed in such a way that it can now handle any number of reactions in the future.
-   In the post endpoint:
    -   we are including counts of each reaction.
    -   user status of reaction for that particular post.
-   Worked on export of creatives categories.

## 16/11/2023 Subscription Module | Jobs API

-   Create endpoints for jobs full text search.
-   Customized the subscription module as per request.
-   Update the Notificaions enable/disable endpoint.
-   Jobs Module Testing

## 17/11/2023 Job - Application Emails

-   Created emails for following:
    -   New Job submitted(to amdin)
    -   New Job Added (to creative)
    -   When creative adds applicaiton (application submitted)
    -   New application (to agency)
-   CORS issue fixed in reset password
-   Some bug fixes

## 20/11/2023 Application Status Emails

-   Created emails for following:
    -   Application accepted
    -   Aplication Rejected

## 21/11/2023 Add Group Members

-   Admin can add members in group
-   Admin can delete member from group
-   Resume url changed and bug fixed
-   Tested the app flow and comiled the bug list.
-   Redirected all emails to admin email.

## 22/11/2023 Resume fix | Email fixes

-   Change the resume download url with some extra security feature.
-   Hide phone number when creative downloads resume of other creative.
-   Email Fixes.

## 23/11/2023 Production | Staging

-   Setup separate environments for staging and production
-   Fixing all issues that came after deployment.
-   Stripe Setup on staging and production

## 24/11/2023 Search Fix | Emails

-   Creatives page search fix
-   EMails tested.
-   Post attachments issue fixed
-   Agency URL bug fix
-   Setup separate storage for staging
