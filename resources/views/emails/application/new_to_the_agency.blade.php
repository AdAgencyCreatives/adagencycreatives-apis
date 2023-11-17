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
                        New Application</h1>
                    <div
                        style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important">

                        <p>Great news! A new candidate has applied for your
                            {{ sprintf('%s', $data['job_title']) }} role on
                            <a href="{{ $data['APP_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>.
                        </p>


                        <div><b>Creative: </b>
                            <a href="{{ $data['creative_profile'] }}" target="_blank">Profile
                                URL</a>
                        </div>
                        <div><b>Email: </b>{{ $data['applicant']->email ?? '' }}</div>
                        <div><b>Job: </b>
                            <a href="{{ $data['job_url'] }}" target="_blank">Job
                                URL</a>
                        </div>
                        <div><b>Resume: </b>
                            <a href="{{ $data['resume_url'] }}" target="_blank">Resume
                                URL</a>
                        </div>
                        <div><b>Message: </b>{{ $data['message'] ?? '' }}</div>

                        <p>We hope this candidate is a strong fit.</p>
                        @include('emails.includes.jobboard_footer')
