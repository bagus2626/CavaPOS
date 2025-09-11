<x-app-layout>
    <x-guest-layout>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Company Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            {{-- Province --}}
            <div class="mt-4">
                <x-input-label for="province" :value="__('Province')" />

                <select id="province" name="province"
                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        dark:bg-black dark:border-gray-700 dark:text-gray-200 dark:focus:border-indigo-500">
                    <option value="">-- Pilih Provinsi --</option>
                </select>

                {{-- hidden input untuk nama --}}
                <input type="hidden" id="province_name" name="province_name">

                <x-input-error :messages="$errors->get('province')" class="mt-2" />
            </div>

            {{-- City --}}
            <div class="mt-4">
                <x-input-label for="city" :value="__('City')" />

                <select id="city" name="city"
                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        dark:bg-black dark:border-gray-700 dark:text-gray-200 dark:focus:border-indigo-500">
                    <option value="">-- Pilih Kota --</option>
                </select>
                <input type="hidden" id="city_name" name="city_name">

                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>

            {{-- District --}}
            <div class="mt-4">
                <x-input-label for="district" :value="__('District')" />

                <select id="district" name="district"
                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        dark:bg-black dark:border-gray-700 dark:text-gray-200 dark:focus:border-indigo-500">
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
                <input type="hidden" id="district_name" name="district_name">

                <x-input-error :messages="$errors->get('district')" class="mt-2" />
            </div>

            {{-- Village --}}
            <div class="mt-4">
                <x-input-label for="village" :value="__('Village')" />

                <select id="village" name="village"
                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm
                        focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        dark:bg-black dark:border-gray-700 dark:text-gray-200 dark:focus:border-indigo-500">
                    <option value="">-- Pilih Kelurahan --</option>
                </select>
                <input type="hidden" id="village_name" name="village_name">

                <x-input-error :messages="$errors->get('village')" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="mt-4">
                <x-input-label for="address" :value="__('Address')" />
                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:text-gray-400" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4 dark:bg-gray-600">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>

        @push('scripts')
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const provinceSelect = document.getElementById("province");
            const citySelect = document.getElementById("city");
            const districtSelect = document.getElementById("district");
            const villageSelect = document.getElementById("village");

            // Load Provinces
            fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
                .then(res => res.json())
                .then(provinces => {
                    provinces.forEach(province => {
                        const option = document.createElement("option");
                        option.value = province.id;
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });
                });

            // Load Cities after province selected
            provinceSelect.addEventListener("change", function() {
                citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                if (!this.value) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                    .then(res => res.json())
                    .then(cities => {
                        cities.forEach(city => {
                            const option = document.createElement("option");
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    });
            });

            // Load Districts after city selected
            citySelect.addEventListener("change", function() {
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                if (!this.value) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                    .then(res => res.json())
                    .then(districts => {
                        districts.forEach(district => {
                            const option = document.createElement("option");
                            option.value = district.id;
                            option.textContent = district.name;
                            districtSelect.appendChild(option);
                        });
                    });
            });

            // Load Villages after district selected
            districtSelect.addEventListener("change", function() {
                villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                if (!this.value) return;

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                    .then(res => res.json())
                    .then(villages => {
                        villages.forEach(village => {
                            const option = document.createElement("option");
                            option.value = village.id;
                            option.textContent = village.name;
                            villageSelect.appendChild(option);
                        });
                    });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            let provinceSelect = document.getElementById("province");
            let citySelect = document.getElementById("city");
            let districtSelect = document.getElementById("district");
            let villageSelect = document.getElementById("village");

            let provinceNameInput = document.getElementById("province_name");
            let cityNameInput = document.getElementById("city_name");
            let districtNameInput = document.getElementById("district_name");
            let villageNameInput = document.getElementById("village_name");

            // contoh isi province
            fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
                .then(response => response.json())
                .then(provinces => {
                    provinces.forEach(prov => {
                        let option = document.createElement("option");
                        option.value = prov.id;
                        option.text = prov.name;
                        provinceSelect.add(option);
                    });
                });

            // simpan nama province saat pilih
            provinceSelect.addEventListener("change", function() {
                let selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
                provinceNameInput.value = selectedOption.text;
            });

            citySelect.addEventListener("change", function() {
                cityNameInput.value = citySelect.options[citySelect.selectedIndex].text;
            });

            districtSelect.addEventListener("change", function() {
                districtNameInput.value = districtSelect.options[districtSelect.selectedIndex].text;
            });

            villageSelect.addEventListener("change", function() {
                villageNameInput.value = villageSelect.options[villageSelect.selectedIndex].text;
            });
        });
        </script>
        @endpush

    </x-guest-layout>
</x-app-layout>
