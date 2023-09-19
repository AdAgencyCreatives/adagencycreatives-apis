@include('emails.email-header')
<table style="height: 661px" border="0" width="100%" cellspacing="0" cellpadding="20">
    <tbody>
        <tr>
            <td valign="top"><span style="color: #000000">Hello Member Support,</span></p>
                <p><span style="color: #000000">A new registration has been requested for
                        <strong>{{ $data['user']->username }}</strong>. See the links below to review and activate the
                        account</span></p>
                <h5><span style="color: #000000">INFORMATION</span></h5>
                <table class="blueTable" style="height: 349px" width="100%">
                    <tbody>
                        <tr>
                            <td><strong><span style="color: #000000">Username</span></strong></td>
                            <td>&nbsp;</p>
                                <p><span style="color: #000000">{{ $data['user']->username }}</span></p>
                                <p>&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><span style="color: #000000">User Email</span></strong></td>
                            <td><span style="color: #000000">{{ $data['user']->email }}</span></td>
                        </tr>

                        <tr>
                            <td>&nbsp;</p>
                                <p><strong><span style="color: #000000">Profile URL<br /></span></strong></p>
                            </td>
                            <td>&nbsp;</p>
                                <p><a href="{{ $data['profile_url'] }}"
                                        style="color: #000000;">{{ $data['profile_url'] }}</a></p>
                        </tr>
                    </tbody>
                </table>
                <p>&nbsp;</p>
                <p><span style="color: #000000">Your Ad Agency Creatives Support Team</span></p>
                <p><em><span style="color: #000000">Gather. Inspire. Do Cool $#*T!</span></em>
            </td>
        </tr>
    </tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
    @include('emails.email-footer')