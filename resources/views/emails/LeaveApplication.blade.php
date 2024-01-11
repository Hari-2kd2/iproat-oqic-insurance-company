<div style="padding:24px;">

    @if (isset($body['admin_name']))
        <h2>Hello {{ $body['admin_name'] }},</h2>
    @else
        <h2>Hello Admin,</h2>
    @endif

    <h3>New Leave Application</h3>

    <p>Employee Id :{{ $body['finger_id'] }},</span>
        <span>Name : {{ $body['user_name'] }},</span>
        <span>Date : {{ $body['date'] }},</span>
        <span>From Date : {{ $body['from'] }},</span>
        <span>To Date : {{ $body['to'] }},</span>
        <span>Leave Type : {{ $body['type'] }},</span>
        <span>No of Days : {{ $body['days'] }},</span>
    </p>

    Thanks,<br>
    {{ config('app.name') }}<br>

</div>
