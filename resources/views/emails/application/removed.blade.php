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
                        Status Update</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['applicant'] }},</span>

                        <p>The status of one of your job applications on <a href="{{ $data['APP_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a> has changed.
                        </p>

                        <p>Unfortunately, <a href="{{ $data['APP_URL'] }}" target="_blank">{{ $data['APP_NAME'] }}</a>
                            will not be moving forward with your application
                            for the role of {{ $data['job_title'] }} at this time.
                        </p>

                        <p>As you know, our industry is alive and changing every moment. We encourage you
                            to keep an eye on other opportunities. You might also try exploring the <a
                                href="{{ $data['APP_URL'] }}/mentoring-resources" target="_blank">Mentors</a>
                            and <a href="{{ $data['APP_URL'] }}/publication-resources" target="_blank">Publications</a>
                            sections on our home page to network and gather
                            helpful information.
                        </p>

                        <p>Trust that the right opportunity is looking for you. It's all about the timing.
                        </p>
                        @include('emails.includes.jobboard_footer')
