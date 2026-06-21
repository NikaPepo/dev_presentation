@component('mail::message')
# New contact-form submission

**From:** {{ $contact->name }}
**Email:** {{ $contact->email }}
**Phone:** {{ $contact->phone }}
**Category:** {{ $contact->category->label() }}

@if($contact->ai_sentiment)
**Sentiment:** {{ ucfirst($contact->ai_sentiment) }}
@endif

---

## Message

{{ $contact->message }}

@if($contact->ai_summary)
---

**AI summary:** {{ $contact->ai_summary }}
@endif

@component('mail::subcopy')
Submitted at {{ $contact->created_at->toDateTimeString() }}
@endcomponent
@endcomponent