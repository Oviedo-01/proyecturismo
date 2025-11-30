<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Foto de Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Sube una foto de perfil para personalizar tu cuenta. (Opcional)') }}
        </p>
    </header>

    {{-- Preview actual --}}
    <div class="mt-4 flex items-center space-x-4">
        <img 
            id="avatar-preview" 
            src="{{ auth()->user()->avatar_url }}" 
            alt="Avatar actual"
            class="w-20 h-20 rounded-full object-cover border-2 border-gray-300"
        >
        
        @if(auth()->user()->avatar)
            <form method="POST" action="{{ route('profile.avatar.delete') }}">
                @csrf
                @method('DELETE')
                
                <button 
                    type="submit" 
                    class="text-sm text-red-600 hover:text-red-800 underline"
                    onclick="return confirm('¿Eliminar foto de perfil?')"
                >
                    {{ __('Eliminar foto') }}
                </button>
            </form>
        @endif
    </div>

    {{-- Formulario de subida --}}
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Nueva Foto')" />
            <input 
                id="avatar" 
                name="avatar" 
                type="file" 
                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                accept="image/jpeg,image/jpg,image/png"
                onchange="previewAvatar(event)"
            >
            <p class="mt-1 text-xs text-gray-500">JPG, JPEG o PNG. Máximo 2MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar Foto') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>

    {{-- Script para preview --}}
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</section>