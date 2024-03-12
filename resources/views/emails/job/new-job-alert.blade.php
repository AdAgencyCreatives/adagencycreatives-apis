@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        JOB ALERTS</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $user->full_name }},</span>

                        <p>We are writing to keep you informed of opportunities you might find interesting for yourself
                            or a friend.</p>
                        <p>If you want to update this feature, simply go to your Dashboard and Job Alerts.</p>
                        
                        <h4 style="text-decoration: underline; margin-bottom: 5px;">Job Details</h4>
                        <div><b>Job Title: </b>{{ $data['title'] }}</div>
                        <div><b>Job URL: </b>
                            <a href="{{ $data['url'] }}" target="_blank">Click here to open job</a>
                        </div>
                        <div><b>Agency: </b>
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency'] }}</a>
                        </div>

                        @include('emails.includes.jobboard_footer')
