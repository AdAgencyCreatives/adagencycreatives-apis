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
                        They're looking</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['applicant'] }},</span>

                        <p>@if (strlen($data['agency_profile']) > 0)
                            <a href="{{ $data['agency_profile'] }}" target="_blank">{{ $data['agency_name'] }}</a>
                        @else
                            {{ $data['agency_name'] }}
                        @endif is currently reviewing your application for their <a
                                href="{{ $data['job_url'] }}" target="_blank">{{ $data['job_title'] }}</a> role.
                            Just thought we’d keep you in the loop.

                        </p>

                        <p>Our fingers are crossed!
                        </p>

                        @include('emails.includes.jobboard_footer')
