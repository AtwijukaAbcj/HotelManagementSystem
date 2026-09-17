<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $documentType }} {{ $documentNumber }}</title>
</head>
<body style="margin:0;background:#f4f7fb;color:#12233f;font-family:Arial,Helvetica,sans-serif;padding:24px;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e3e9f1;border-radius:8px;padding:28px;">
        <h1 style="margin:0 0 8px;font-size:22px;">Hotel Management System</h1>
        <p style="margin:0 0 24px;color:#667085;">Your {{ strtolower($documentType) }} is ready.</p>
        <p>Hello {{ $recipientName }},</p>
        <p>Thank you. Your document details are below:</p>
        <table style="width:100%;border-collapse:collapse;margin:20px 0;">
            <tr><td style="padding:8px 0;color:#667085;">Document</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $documentNumber }}</td></tr>
            <tr><td style="padding:8px 0;color:#667085;">Date</td><td style="padding:8px 0;text-align:right;">{{ $documentDate }}</td></tr>
            <tr><td style="padding:8px 0;color:#667085;">Amount</td><td style="padding:8px 0;text-align:right;font-weight:700;">UGX {{ $amount }}</td></tr>
        </table>
        <a href="{{ $receiptUrl }}" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;border-radius:6px;padding:12px 18px;font-weight:700;">View receipt</a>
        <p style="margin:24px 0 0;color:#667085;font-size:12px;">This email was generated automatically. Please keep it for your records.</p>
    </div>
</body>
</html>
