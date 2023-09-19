@include('emails.email-header')
<table style="height: 661px" border="0" width="100%" cellspacing="0" cellpadding="20">
    <tbody>
        <tr>
            <td valign="top"><span style="color: #000000">Hi {{ $data['user']->username }},</span></p>
                <p><span style="color: #000000">Welcome to {{ env('APP_NAME')}}!</span></p>

                <p><span style="color: #000000;">Visit your <a style="color: #000000;" href="#">profile</a>, where you
                        can tell us more about
                        yourself, change your preferences, or make new connections, to get
                        started.</span></p>
                <p><span style="color: #000000;">Forgot your password? Don't worry, you can reset it
                        with your email address from <a style="color: #000000;" href="#">this page</a>
                        of our site</span></p>
                <p><span style="color: #000000;">Thanks,<br />
                        <p>&nbsp;


                        <p><span style="color: #000000">Ad Agency Creatives</span></p>
                        <p><em><span style="color: #000000">Member Support Team</span></em>
            </td>
        </tr>
    </tbody>
</table>


<tr>
    @include('emails.email-footer')