@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
@if ($data['show_test_links'] ?? [] == 'yes')
<tr>
    <td
        style="padding: 10px; display: flex; flex-direction: column; row-gap: 10px; background-color: pink; justify-content: center; align-items: center">
        <div>Ignore this pink section, use the links given below to view different versions</div>
        <div>
            You are currently viewing: <b>{{ $data['apply_type'] }}</b> Job Closed Version
        </div>
        <div style="display: flex; column-gap: 10px; justify-content: center; align-items: center">
            @if ($data['apply_type'] == 'External')
            <a class="btn btn-primary" href="?apply_type=Internal">View Internal Job Closed Version</a>
            @else
            <a class="btn btn-primary" href="?apply_type=External">View External Job Closed Version</a>
            @endif
        </div>
    </td>
</tr>
@endif
<tr>
    <td>
        <table style="background: #000; color:#fff; " role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style=" font-family: sans-serif;  mso-height-rule: exactly; line-height: 14px; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Job Closed Notification</h1>
                    <div style="  border-radius: 5px; max-width: 900px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['recipient_name']  ?? '[Ali]'}},</span>

                        <p>The <b><a href="{{ $data['job_url']  ?? ''}}" target="_blank">{{ $data['job_title'] ?? '' }}</a></b>
                            opportunity posted by
                            @if (strlen($data['agency_profile'] ?? '' ) > 0)
                            <a href="{{ $data['agency_profile'] }}" target="_blank"> {{ $data['agency_name'] ?? ''}}</a>
                            @else
                            {{ $data['agency_name'] ?? '' }}
                            @endif
                            has been closed on
                            <a href="{{ $data['APP_URL'] ?? '' }}" target="_blank">{{ $data['APP_NAME'] ?? "" }}</a> job board.
                        </p>

                        <p>
                            @if ($data['apply_type'] ?? '' == 'Internal')
                            The job post could have expired or another candidate was selected. Either way, we
                            wanted to keep you informed and thank you for your time and interest.
                            @else
                            We wanted to keep you informed and thank you for exploring the opportunity.
                            @endif
                            Click <a href="{{ $data['APP_URL'] ?? '' }}/creative-jobs/" target="_blank">here</a> to view
                            current open jobs.
                        </p>
                                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                <p>Cheers,<br>
                    The Ad Agency Creatives Team.</p>


                        @include('emails.includes.jobboard_footer')