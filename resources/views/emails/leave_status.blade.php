<!DOCTYPE html>
<html>
<head>
    <title>Leave Status Update</title>
</head>
<body>
    <p>Dear {{ $employee }},</p>

    <p>Your leave request from <strong>{{ $from }}</strong> to <strong>{{ $to }}</strong> has been <strong>{{ $status }}</strong> by the admin.</p>

    <p>Thank you,<br>HR Department</p>
</body>
</html>
