<li>
    <router-link tag="a" :to="{name: 'profile.view', params: { id: {{ auth()->id() }} }}" class="block no-underline text-90 hover:bg-30 p-3">
        {{ __('Profile') }}
    </router-link>
</li>
