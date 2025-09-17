@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Status Update</h1>
                    <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['applicant'] }},</span>

                        <p>The status of one of your job applications on <a href="{{ $data['APP_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a> has changed.
                        </p>

                        <p>
                            Your application with
                            @if (strlen($data['agency_profile']) > 0)
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency_name'] }}</a>
                            @else
                            {{ $data['agency_name'] }}
                            @endif
                            is not moving forward at this time for the
                            <a href="{{ $data['job_url'] }}" target="_blank">{{ $data['job_title'] }}</a> opportunity.
                            There are a variety of reasons for this, such as; they chose
                            another candidate, an internal move was made, the job was closed without a hire, or
                            you are not interested in moving forward.
                        </p>

                        <p>As you know, our industry is alive and changing every moment. We encourage you
                            to keep an eye on the jobs board for other opportunities. You might also try exploring the
                            <a href="{{ $data['APP_URL'] }}/mentoring-resources" target="_blank">Resources</a>
                            and <a href="{{ $data['APP_URL'] }}/publication-resources" target="_blank">Publications</a>
                            sections on our home page, or connect with other creatives in <a
                                href="{{ $data['APP_URL'] }}/community" target="_blank">The Lounge</a>.
                        </p>

                        <p>Trust that the right opportunity is looking for you. It's all about the timing.
                        </p>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>
                        @include('emails.includes.jobboard_footer')