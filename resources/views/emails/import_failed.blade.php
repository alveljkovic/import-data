<p>Hello {{ $log->user->name }},</p>

<p>Your data import has failed.</p>

<p><strong>Import type:</strong> {{ $log->import_type_key }}</p>
<p><strong>File:</strong> {{ $log->original_filename }}</p>

<p>Please review your data and try again.</p>

<p>Best regards,<br>Your App Team</p>