# Reply-To and submission results

`Mail` accepts an optional final `replyTo: Sender`. An explicit reply address reaches Postmark, Mailgun and SMTP. Without one, SMTP and Postmark retain their sender fallback; Mailgun omits the explicit Reply-To header.

```php
$result = $mailer->sendWithResult(
    $mail,
    metadata: ['job_id' => '123'],
    trackOpens: true
);
```

`SubmissionMailerInterface` adds this optional capability without changing the existing `MailerInterface::send(): bool` contract. `MailSubmission` contains `messageId`, `submittedAt` and `accepted`. A message identifier or provider timestamp can be null when the transport does not supply it.

Acceptance means the transport accepted submission, not that a recipient received the message. Provider rejection remains an exception where the SDK reports an error. SMTP can return `accepted: false` on transport refusal. Existing boolean sends delegate to submission results and preserve their transport behavior.

Postmark and Mailgun pass metadata and open-tracking settings to their providers. SMTP cannot provide those provider features and ignores the optional settings. Correlate SMTP submissions with their message identifier instead. Tests inspect SDK and SMTP payloads without sending actual mail.
