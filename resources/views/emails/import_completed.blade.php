<p>Hello {{ $log->user->name }},</p>

<p>Your data import has completed successfully!</p>

<p><strong>Import type:</strong> {{ $log->import_type_key }}</p>
<p><strong>File:</strong> {{ $log->original_filename }}</p>

<p>You can now review the imported data in your dashboard.</p>

<p>Best regards,<br>Your App Team</p>