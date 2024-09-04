@component('mail::message')
    # Gözləmədə olan Elanlar

    Aşağıdakı elanlar hələ də gözləmədədir:

    @foreach ($announcements as $announcement)
        - Elan ID: {{ $announcement->id }}, Istifadəçi: {{ $announcement->user->name }}
    @endforeach

    Zəhmət olmasa, bu elanlara nəzər yetirin.
    <br>
    <a href="https://api.myhome.az/nova"> Admin Panel</a>
    <br>
    Təşəkkürlər,<br>
   Myhome.az
@endcomponent
