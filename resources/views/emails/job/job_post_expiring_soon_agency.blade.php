@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height:1.5 !important; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Job Post Expiring Soon</h1>
                    <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['author'] }},</span>

                        <p>We hope you’ve been finding strong candidates for your {{ $data['job_title'] }} role on
                            {{ $data['APP_NAME'] }}.</p>

                        <p>This email is just a courtesy heads up to let you know this listing will expire in three (3)
                            days. If you need more time, you can easily renew listings from your <a
                                href="{{ $data['url'] }}" target="_blank">dashboard</a>.

                        </p>

                        <h4 style="text-decoration: underline; margin-bottom: 5px;">Job Details</h4>
                        <div><b>Job Title: </b>{{ $data['job_title'] }}</div>
                        <div><b>Posting: </b>
                            <a href="{{ $data['url'] }}" target="_blank">Click here to open job</a>
                        </div>
                        <div><b>Date Posted: </b>{{ $data['created_at'] }}</div>
                        <div><b>Expiration: </b>{{ $data['expired_at'] }}</div>
                        <div><b>Agency: </b>
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency_name'] }}</a>
                        </div>

                        <p>We appreciate you sharing this opportunity with our community.</p>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard. </p>
                                                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>

                        @include('emails.includes.jobboard_footer')
