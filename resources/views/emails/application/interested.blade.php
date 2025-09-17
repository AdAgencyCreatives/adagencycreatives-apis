@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Status Update</h1>
                    <div style="background: #000; border-radius: 5px; max-width: 900px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['applicant'] }},</span>

                        <p>The hiring team at {{ $data['agency_name'] }} has reviewed your application for
                            <a href="{{ $data['job_url'] }}" target="_blank">{{ $data['job_title'] }}</a>.
                            Each company's hiring process is based on their own unique policies, but things
                            look promising!
                        </p>

                        <p>The agency should hopefully be reaching out for an interview soon. In the
                            meantime, here are a few best practices we recommend ahead of your meeting:
                        </p>

                        <ul>
                            <li>Research the agency and your potential creative partners.</li>
                            <li>Have some insightful questions ready to go.</li>
                            <li>Be on time. Better yet, be a bit early.</li>
                            <li>Be interested and engaging.</li>
                            <li>Be prepared to talk about your work, and how it specifically aligns with the
                                opportunity.
                            </li>
                            <li>If you have technical difficulties, suggest a phone call or reschedule.</li>
                            <li>Don't forget to follow up with an email and say thank you. It’s appreciated!</li>
                            <li>Most of all, be yourself.</li>
                        </ul>

                        <p>You got this!
                        </p>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>
                        @include('emails.includes.jobboard_footer')