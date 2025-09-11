{{-- resources/views/welcome.blade.php --}}
<x-app-layout>
    <div class="pb-12 bg-white dark:bg-black">
        <div class="bg-homepage">
            <div class="py-12">
                <x-hero-section/>
            </div>
            @include('pages.client.home.benefit')
            @include('pages.client.home.about')
            @include('pages.client.home.services')
            @include('layouts.portfolio')
            @include('layouts.pricing')
            @include('components.contact-widget')
        </div>

    </div>
</x-app-layout>

<style>
    .bg-homepage {
        background-image:
        url('/images/vastech-mars-robot.png');
        background-size: 100% auto; /* Penuhi horizontal dulu */
        background-position: center top;
        background-repeat: no-repeat;
    }
    @media (max-width: 768px) {
        .bg-homepage {
            background-size: 240vw auto; /* Penuhi seluruh area */
        }
    }


</style>
