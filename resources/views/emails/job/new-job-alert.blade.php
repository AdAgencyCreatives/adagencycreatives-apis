@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        JOB ALERTS</h1>
                    <div style="border-radius: 5px; max-width: 900px; margin: 0 auto; line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $user->first_name ?? '-' }},</span>

                        <p>We are writing to keep you informed of opportunities you might find interesting for yourself
                            or a friend.</p>
                        <p>If you want to update this feature, simply go to your Dashboard and Job Alerts.</p>

                        <h4 style="text-decoration: underline; margin-bottom: 5px;">Job Details</h4>
                        <div><b>Job Title: </b>{{ $data['title'] }}</div>
                        <div><b>Agency: </b>
                            @if (strlen($data['agency_profile']) > 0)
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency'] }}</a>
                            @else
                            {{ $data['agency'] }}
                            @endif
                        </div>
                        <div><b>Location: </b>
                            {{ $data['location'] }}
                        </div>
                        <div><b>Remote: </b>
                            {{ $data['remote'] }}
                        </div>
                        <div><b>Job Link: </b>
                            <a href="{{ $data['url'] }}" target="_blank">Click here</a>
                        </div>
                        @if ($data['subscribers_count'] && strlen($data['subscribers_count']) > 0)
                        <div style="display: none"><b>For Staging Only:</b><br />
                            Subscribers Count = {{ $data['subscribers_count'] }}
                        </div>
                        @endif
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>
                        @include('emails.includes.jobboard_footer')