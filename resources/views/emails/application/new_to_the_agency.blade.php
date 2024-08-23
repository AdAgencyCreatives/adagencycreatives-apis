@include('emails.includes.jobboard_header')
@php
    $apply_type = isset($data['apply_type']) ? $data['apply_type'] : 'Internal';
@endphp
<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        @if ($apply_type == 'Internal')
                            New
                        @else
                            Interested
                        @endif
                        Applicant
                    </h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['receiver_name'] }},</span>

                        <p>Great news! A new candidate
                            @if ($apply_type == 'Internal')
                                has applied for
                            @else
                                clicked Apply Now to
                            @endif
                            your
                            <a href="{{ $data['job_url'] }}" target="_blank">{{ $data['job_title'] }}</a> role on
                            <a href="{{ $data['APP_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>.
                        </p>

                        <div><b>Creative: </b>
                            <a href="{{ $data['creative_profile'] }}" target="_blank">{{ $data['creative_name'] }}</a>
                        </div>
                        <div><b>Email: </b>{{ $data['applicant']->email ?? '' }}</div>
                        <div><b>Job: </b>
                            <a href="{{ $data['job_url'] }}" target="_blank">Job
                                Link</a>
                        </div>
                        <div><b>Resume: </b>
                            <a href="{{ $data['resume_url'] }}" target="_blank">Resume
                                Link</a>
                        </div>
                        <div><b>Message: </b>{{ $data['message'] ?? '' }}</div>

                        <p>We hope this candidate is a strong fit.</p>
                        @include('emails.includes.jobboard_footer')
