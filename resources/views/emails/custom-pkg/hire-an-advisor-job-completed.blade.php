@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        HIRE AN ADVISOR JOB COMPLETED</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello
                            {{ $data['recipient'] }},</span>

                        <p>Great news!</p>
                        <p><strong><a href="{{ $data['agency_profile'] }}"
                                    target="_blank">{{ $data['agency_name'] }}</a>
                            </strong> job has been marked closed.</p>

                        <h4 style="text-decoration: underline; margin-bottom: 5px;">
                            Request Information</h4>

                        <div><b>Title:
                            </b>{{ $data['category'] }}
                        </div>

                        <div><b>Location:
                            </b>{{ $data['state'] . ', ' . $data['city'] }}
                        </div>

                        <div><b>Advisor:
                            </b>{{ sprintf('%s', $data['advisor']) }}
                        </div>

                        <p>Next Steps:</p>
                        <p>Follow up with your Ad Agency Creatives administrative support with the new hires name,
                            start date, and a copy of the offer letter terms and conditions, or the final status of
                            the opportunity.</p>




                        @include('emails.includes.jobboard_footer')
