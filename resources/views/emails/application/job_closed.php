@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;" class="body_text_color body_text_size">
                    <h1 style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        Job Closed Alert</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important" class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['recipient_name'] }},</span>

                        <p>The {{$data['job_title']}} posted by {{ $data['agency_name']}} has been closed on
                            $data['APP_NAME'] job board. The job could have expired or it was filled. Either way, we
                            wanted to
                            keep you informed and thank you for your time and interest.
                        </p>

                        <p>Wishing you all the best.</p>
                        <p>Thanks,</p>

                        @include('emails.includes.jobboard_footer')