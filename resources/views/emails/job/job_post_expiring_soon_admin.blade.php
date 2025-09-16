@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height:1.5 !important;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Job Post Expiring Soon</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            Member Support,</span>

                        <p>Just a helpful heads up, the job listing below expires tomorrow.</p>

                        <p>We notified {{ $data['agency_name'] }} in an email a few days ago, so this is an excellent
                            time to follow up with them for feedback and check on any pending applications.
                        </p>

                        <h4 style="text-decoration: underline; margin-bottom: 5px;">Job Details</h4>
                        <div><b>Job Title: </b>{{ $data['job_title'] }}</div>
                        <div><b>Posting: </b>
                            <a href="{{ $data['url'] }}" target="_blank">Click here to open job</a>
                        </div>
                        <div><b>Date Posted: </b>{{ $data['created_at'] }}</div>
                        <div><b>Agency: </b>
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency_name'] }}</a>
                        </div>

                        @include('emails.includes.jobboard_footer')