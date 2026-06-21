@component('mail::message')
# Thanks, {{ $contact->name }}!

We've received your message and will get back to you shortly.

## Your submission

@component('mail::panel')
{{ $contact->message }}
@endcomponent

**Phone on file:** {{ $contact->phone }}

If you didn't submit this, just ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent