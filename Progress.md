## 7/8/2023 - User CRUD
Following endpoints were created
- Fetch all users ```GET /api/v1/users```
- Fetch single user ```GET /api/v1/users/{id}```
- Create new user ```POST /api/v1/users```
- Update single user ```PATCH /api/v1/users/{id}```
- Delete user ```DELETE /api/v1/users/{id}```

## 8/8/2023 - Agency | Creative CRUD
Following endpoints were created
- Fetch all agencies ```GET /api/v1/agencies```
- Fetch single agency ```GET /api/v1/agencies/{id}```
- Create new agency ```POST /api/v1/agencies```
- Update single agency ```PATCH /api/v1/agencies/{id}```
- Delete agency ```DELETE /api/v1/agencies/{id}```
---
- Fetch all creatives ```GET /api/v1/creatives```
- Fetch single creative ```GET /api/v1/creatives/{id}```
- Create new creative ```POST /api/v1/creatives```
- Update single creative ```PATCH /api/v1/creatives/{id}```
- Delete creative ```DELETE /api/v1/creatives/{id}```
- ---
Config file for global settings
- Datetime format ```2023-08-08 19:08:02```  ```(Y-m-d H:i:s)```

## 9/8/2023 - Job | Application | Resumes| Link | Phone | Address |  CRUD
Following endpoints were created
- Fetch all jobs ```GET /api/v1/jobs```
- Fetch single job ```GET /api/v1/jobs/{id}```
- Create new job: `POST /api/v1/jobs`
- Update single job: `PATCH /api/v1/jobs/{id}`
- Delete job: `DELETE /api/v1/jobs/{id}`
---
- Fetch all applications: `GET /api/v1/applications`
- Fetch single application: `GET /api/v1/applications/{id}`
- Create new application: `POST /api/v1/applications`
- Update single application: `PATCH /api/v1/applications/{id}`
- Delete application: `DELETE /api/v1/applications/{id}`
---
- Fetch all resumes: `GET /api/v1/resumes`
- Fetch single resume: `GET /api/v1/resumes/{id}`
- Create new resume: `POST /api/v1/resumes`
- Update single resume: `PATCH /api/v1/resumes/{id}`
- Delete resume: `DELETE /api/v1/resumes/{id}`
---
- Fetch all links: `GET /api/v1/links`
- Fetch single link: `GET /api/v1/links/{id}`
- Create new link: `POST /api/v1/links`
- Update single link: `PATCH /api/v1/links/{id}`
- Delete link: `DELETE /api/v1/links/{id}`
---
- Fetch all phone numbers: `GET /api/v1/phones`
- Fetch single phone number: `GET /api/v1/phones/{id}`
- Create new phone number: `POST /api/v1/phones`
- Update single phone number: `PATCH /api/v1/phones/{id}`
- Delete phone number: `DELETE /api/v1/phones/{id}`
---
- Fetch all addresses: `GET /api/v1/addresses`
- Fetch single address: `GET /api/v1/addresses/{id}`
- Create new address: `POST /api/v1/addresses`
- Update single address: `PATCH /api/v1/addresses/{id}`
- Delete address: `DELETE /api/v1/addresses/{id}`
---

## 10/8/2023 - Attachment | Notes |  CRUD
- Seeders for 
-- Categories
--	Industries
- Global Exception handling
- Cache Implementattion
- Notes CRUD Endpoints
- Attachments CRUD Endpoints

## 11/8/2023 - Bookmarks | Educations | Experiences | Categories | Industries CRUD
- Bookmarks API Endpoints
- Educations API Endpoints  
- Experiences API Endpoints
- Categories API Endpoints
- Industries API Endpoints
 
## 14/8/2023 - Worked on Adeel bhai's personal project

## 15/8/2023 - Filters On All Entities
- Users ( Agencies | Creatives )
- Jobs
- Applications
- Notes
- Resumes
- Educations
- Experiences
- Atachments
- Links
- Phones
- Addresses
- Bookmarks
- 

## 16/8/2023 - Authentication & Authorization
- All the endpoints require token based authentication except  `registration` and `login`
- `/login` will give token that can be used in all subsiquent requests.
- Roles and permissions created for critical entities and assigned to appropriate roles.
- New registered user will acquire all the permissions based on its role.
- Following permissions will be check based on role
- `Agency` role can:
--  `create`  `update` `delete`   agencies , jobs and other common entities
- `Creative` role can:
--  `create`  `update` `delete`   creatives , applications, resumes, eduaction, experience and other common entities
- `Advisor` role user has all the permissions of `agency` ,  `creative` and other common entities.
#### Common entities:
- Phones
- Addresses
- Links
- Attachments
- Bookmarks
- Notes
- 
## 17/8/2023 - Admin Dashboard
- Login form for admin
- Admin can 
-- Create new users
-- Update existing users (password, status, role etc)
-- View all users
-- Apply filters on Users (role, status, email, username)
-- Pagination (Previous | Next page)
-- Per Page Filter (10 | 20 | 50 PerPage)
- With proper toast notifications and error alerts  

## 18/8/2023 - Admin Dashboard | Job Module
Admin can
- View all jobs
- Delete any job
- Apply different filters on the jobs
    -Title
    -Category
    -Employement Type
    -Industry Experience
    -Media Experience
    -Labels (Featured, Remote, Urgent etc)
- See all the applications sent on that particular job
- Download Resumes attached by candidates	 

## 21/8/2023 - Admin Dashboard | Stat Widgets
- Admin can update user status
- Admin can approve or reject job status
- Admin can also filter jobs based on Status(Pending, Approved, Expired, Filled)
- Admin can also view agency or creative info by clikcing on detail button
- Dashboard
-- User Stats Widgets
-- Job Stats Widgets
-- Chart for last 7 days registered users
-- Chart for last 7 days posted jobs

## 22/8/2023 - Stripe Integration
- User can purchase the plan
- User can cancel the subscription
- List all current subscriptions
 
## 23/8/2023 - Managing Quota For User's Plan
- Default Job status set to "Draft"
- Agency user can publish the job
- While changing status from draft to published, Checking if he has quota remaning?
- If no quota remaining 
    - Dont allow job publishing
- If has quota:
    - Allow job publishing
	- Decrease the quota value for that user's plan
-  Dashboard widget for Sales (package wise)
- Last 7 days sale graph
- Order page:
    - Admin can see all the orders on single page with their corresponding user and amount	
## 24/8/2023 - Community | Groups
- Created Migration and Models for
    - Groups
    - Group Members
    - Posts
    - Likes
    - Comments
- API Endpoint for listing all groups (with status and name filter)
- Admin Can:
	- Create New Group
	- See Details of Group
	- Update the group (status, name, description, cover_image)
	- Delete the group

## 25/8/2023 - Admin | Posts
- API Endpoint for Posts
-   Admin can:
	- See all Posts
	- Update post status
	- Delete the post
	- Filter posts by:
		- Group
		- Author
		- Status
- Admin can:		
	- Add members to group
	- See all members of group

## 28/8/2023 - Admin  | Comments | Likes
- API Endpoints for 
    - Comments
    - Likes
- Admin can:
	- Update role of group member
	- Can see all the comments of post (along with comment authors) on single post page
	- Can see all the attachments of post.
	- Can see total likes on the post
	
## 29/8/2023 - Reviews
- Endpoint for Reviews:
    - Agency or Creative can put refiew on each other
    - You can filter the reviews by target entity (e.g. Agency User ID, Creative User ID)
    - You will also get average rating in the response

## 30/8/2023 - Dashboard | Jobs detail page | Locations
- Admin Dashboard:
	- Added more fields on job detail page
	- Separate dropdows links for Creative and Agencies
	- Locations module added, admin can:	
		- Create New States and View Existing states
		- Create New Cities within specific State and View Existing States
## 31/8/2023 - Agency | Creative Extra Fields
- Admin Dashboard:
	- Can see advisors with different dropdown menu
	- Extra Fields in agency/advisor profile
	- Extra fields in creative profile	
 
## 01/9/2023 - Agency | Job Add/Update | Categories
- Admin Dashboard:
	- Can see advisors, creatives and agencies as separate menu options
	- Can add new job
	- Can update the Job (with more fields)
	- Can manage categories (View | Update | Delete)

## 04/9/2023 - Dashboard | Taxonomies
- Admin can manage:
	- Industry Experiences
	- Media Experiences
	- Years of Experiences
-  Separate Add and View page for all taxonomies
-  API endpoints for Industy, Media and Years of Experience

## 05/9/2023 - Dashboard | Static to dynamic dropdowns for Taxonomies
- Separate endpoints for industry & mdedia experiences
- Years of experience is dynamic (from backend)
- Few extra fields added in agency profile 
- Some visual fixes 
- Logs endpoint for viewing error logs for admin e.g `/logs`
- Implemented AWS S3 bucket for storing images
 
## 06/9/2023 - Dashboard | More meta fields in agency, creatives and job 
- Agency meta fields for making featured and also added workplace preference
- Creatives meta field for making featured, urgent, remote and relocation.
- Modified database structure for handling data more efficiently.
- Job meta fields added for workplace preference, adding location (state and city)
- Added location and sub-agency info in jobs API
 
## 07/9/2023 - Dashboard | Bug Fixes | Enhancements | Activity Logs 
- Modified few fields in agency | creatives | advisors and jobs
- Some bug fixes on job detail page
- Corrected the image upload process for job and groups
- Added more fields in jobs, agency and creatives API
- Added Telescope for activity logs

## 08/9/2023 - Chat Funtionality
 - API endpoint for sending new message.
 - API endpoint for all related users 
    - (with name and last one message to show in left panel of messaging screen 
    - from where we select any user and start the chat).
 - Created frontend which will be used by admin to view messages of all the users.
 
## 11/9/2023 - Chat Functionality
 - API endpoints for chats
 - Created impersonating route

## 12/9/2023 - Dashboard | Strengths CRUD
 - API endpoints for all strengths(tags).
 - Added strengths module in admin dashboard 
 - Added strengths field in creatives profile
 - Setup email on admin action (single email for now)
 
## 13/9/2023 - Extra fields
- Added `open_to_relocation` and `open_to_remote` in jobs section
- Added `workplace preference` in creatives profile
- Setup Emails with Queues
 
## 14/9/2023 - Reset Password | Group Invitation
 - API endpoint which will send email to the user conatining the reset link
 - API endpoint which will allows user to enter new password alongwith token
 - API endpoint to allow ther User to send group invitation to other user
 - API endpoint to update the status of invitation, after that user will become member of that group
 - API endpoint to get all the invitations [with filter of user and status]
  
 ## 15/9/2023 - Advisor permission | Extra fields 
 - Added new fields in jobs api endpoint
 - Added new fields in creatives api endpoint
 - Setup permission level for advisor role for different pages.
 - Sending email to admin when user purchases new order/package
 - Added character strengths field in jobs section
 
  ## 18/9/2023 - Location | Filters
 - Added location dropdown for creatives and agencies in admin dashboard
 - Upload profile Image for Creatives
 - Added location field in both (agencies | creatives)
 - Applied filters on agencies (name, location)
 - Applied filters on creatives (name, location, title)
  
 ## 19/9/2023 - Agency Filters | Job Alert Subscription
 - Created endpoint for users 
    - To subscribe to any category for receiving emails
    - To enable/disable the email alert for new jobs
 - Applied filters on agencies (industry and media experiences)
 - Job alert to admin when new job is posted
 - Job alert to all the subscribers who have subscribed to any category when new job is posted
 
 ## 20/9/2023 - SEO
- Admin can:
    - Update SEO (title, description, keywords) for creatives 
    - Update SEO (title, description, keywords) for agencies 
    - Update SEO (title, description, keywords) for jobs 
    - Update SEO (title, description, keywords) for home page (Generic SEO) 
- Created endpoint for getting generic SEO for homepage 
- Included SEO tags in API response of agencies, creatives and jobs
 
 ## 21/9/2023 - SEO | Addtional fields in API
- Added dynamic tags in SEO so that admin can easily configure SEO titles
- Modify the registration flow to comply with need.
 
 ## 22/9/2023 - SEO | Addtional fields in API
- Admin can set default SEO title for jobs and creatives 
- Add slug in jobs table, it will be auto-generated  from different values of job table
- Created endpoint for getting user subscription status, it will also show how many jobs user can post
- Collaborated with frontend-dev to make proper flow by modifying API response (where needed)
 
 ## 25/9/2023 - Creative Spotlight
- Admin can set default SEO title for creatives spotlight
- Created endpoint which lists all the creatives spotlights along with their urls
- Transformed creative title field from open text box to specific dropdown
 
 ## 26/9/2023 - Job Invitation | Creative Title field
- Admin can set title (open text field) and also select from industry job title (dropdown).
- Separate sections for showing portfolio items and spotlights.
- Job Invitation funtionality for agency users.
 
 ## 27/9/2023 - Media page
- Admin can see all the media attachments uploaded by all users
- Can Apply all filter to 
    -   Select medias for particular user 
    -   Also for particular media type e.g.`logo`, `profile_image` etc 

## 28/9/2023 - Media page | Coupons
- Admin can 
    - Update the status of media attachment
    - Can delete the media attachment
    - Filter the attachments based on their status
- Enhanced payment flow a little bit (in progress) 

## 29/9/2023 - Dynamic Content for Pages
- Admin can 
    - Update the content of folowing pages:
    - Home
    - Community
    - Footer
- Enhancments in the payment flow 
 
## 02/10/2023 - Assign advisor to Custom Job Request
- Admin can 
    - View all the custom job requests.
    - Update the status to accepted or rejected.
    - Assign any advisor to that job request.
    - Assign any package to that request.
- API endpoint that will store the custom job reuqest into database.

## 03/10/2023 - Assign advisor to Custom Job Request
- Admin can 
     - Enter the text in text editor
     - Can also upload image in that editor even by dragging image instead of selecting.
- Custom Job Request form fields updated for API as well as for admin panel.
 
## 04/10/2023  Data Import
- Created endpoint for advisor where he can see all the assigned agencies.
- Created endpoint with the help of that he can impersonate the agency profile.
- Imported users data from WordPress
