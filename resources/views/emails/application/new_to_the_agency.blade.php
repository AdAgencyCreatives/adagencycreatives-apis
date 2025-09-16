@include('emails.includes.jobboard_header')
@php
$apply_type = isset($data['apply_type']) ? $data['apply_type'] : 'Internal';
@endphp
<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        @if ($apply_type == 'Internal')
                        New
                        @else
                        Interested
                        @endif
                        Applicant
                    </h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['receiver_name'] ?? '' }},</span>

                        <p>Great news! A new candidate
                            @if ($apply_type == 'Internal')
                            has applied for
                            @else
                            clicked <i>Apply Now</i> to
                            @endif
                            your
                            <a href="{{ $data['job_url'] ?? '' }}" target="_blank">{{ $data['job_title'] ?? '' }}</a> role on
                            <a href="{{ $data['APP_URL'] ?? '' }}" target="_blank">{{ $data['APP_NAME'] ?? '' }}</a>.
                            @if ($apply_type == 'Internal')
                            @else
                            You have selected the external apply option.
                            @endif
                        </p>

                        <div><b>Creative: </b>
                            {{ $data['creative_name'] ?? '' }}
                        </div>
                        <div><b>Email: </b>{{ $data['applicant']->email ?? '' }}</div>
                        <div><b>Job: </b>
                            <a href="{{ $data['job_url'] ?? '' }}" target="_blank">Job
                                Link</a>
                        </div>
                        <div><b>Resume: </b>
                            <a href="{{ $data['resume_url'] ?? '' }}" target="_blank">Resume
                                Link</a>
                        </div>
                        <div><b>Profile Link: </b><a href="{{ $data['creative_profile'] ?? '' }}" target="_blank">AAC
                                Profile</a></div>
                        <div><b>Message: </b>
                            @if ($apply_type == 'Internal')
                            {{ $data['message'] ?? '' }}
                            @else
                            Clicked <i>Apply Now</i>
                            @endif
                        </div>

                        <p style="">&nbsp;</p>
                        @include('emails.includes.jobboard_footer')