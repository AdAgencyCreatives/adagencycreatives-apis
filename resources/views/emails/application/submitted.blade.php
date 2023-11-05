@include('emails.includes.lounge_header')
<tr>
    <td style="padding: 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
        class="body_text_color body_text_size">
        <div style="background:#fff; border-radius: 5px; width: 450px; margin: 0 auto; color:#000000">
            <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi {{ $data['recipient'] }},</span>

            <p>Your application for {{ $data['job_title'] }} has been submitted.</p>

            <p>We know the job search can be an uncertain journey, and we’re rooting for you all the way.
            </p>
            @include('emails.includes.lounge_footer')
