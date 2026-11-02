@extends('citizen.layouts.app')

@section('title', 'Mi Perfil | Álamo Reporta')

@section('content')
    <div class="profile-container" style="max-width: 520px; margin: 0 auto; padding: 12px 4px 40px 4px;">

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.12); border: 1.5px solid #10B981; color: #10B981; padding: 14px 20px; border-radius: 9999px; margin-bottom: 22px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- TARJETA PRINCIPAL DEL PERFIL -->
        <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 28px; padding: 30px 24px; box-shadow: var(--shadow-card); margin-bottom: 22px; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 75px; background: linear-gradient(135deg, #FBBF24 0%, #EA580C 100%); opacity: 0.9;"></div>

            <div style="position: relative; z-index: 2; margin-top: 20px;">
                <div style="width: 84px; height: 84px; border-radius: 50%; background: #FFFFFF; border: 4px solid var(--bg-surface); color: #EA580C; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; box-shadow: 0 10px 24px -4px rgba(234, 88, 12, 0.35);">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">{{ $user->name ?? 'Ciudadano' }}</h2>
                <span style="display: inline-block; background: var(--accent-orange-soft); color: var(--accent-orange); font-size: 0.82rem; font-weight: 700; padding: 4px 14px; border-radius: 9999px; margin-bottom: 20px;">
                    @<span>{{ $user->username ?? 'user' }}</span>
                </span>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; padding-top: 18px; border-top: 1px solid var(--border-subtle);">
                    <div style="background: var(--bg-elevated); padding: 14px; border-radius: 20px;">
                        <span style="font-size: 1.4rem; font-weight: 800; color: var(--accent-orange); display: block;">{{ $myReportsCount }}</span>
                        <span style="font-size: 0.78rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Mis Reportes</span>
                    </div>
                    <div style="background: var(--bg-elevated); padding: 14px; border-radius: 20px;">
                        <span style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user->colonia ?: 'Zona Centro' }}</span>
                        <span style="font-size: 0.78rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Localidad</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE INFORMACIÓN PERSONAL -->
        <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 28px; padding: 26px 24px; box-shadow: var(--shadow-card); margin-bottom: 22px;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-id-badge" style="color: var(--accent-orange);"></i> Datos de la Cuenta
            </h3>

            <form action="{{ route('citizen.profile.update') }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.83rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; padding-left: 6px;">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                           style="width: 100%; height: 50px; padding: 0 18px; border-radius: 9999px; border: 1.5px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.95rem; font-weight: 500;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.83rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; padding-left: 6px;">Teléfono / WhatsApp</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="765 100 0000"
                           style="width: 100%; height: 50px; padding: 0 18px; border-radius: 9999px; border: 1.5px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.95rem; font-weight: 500;">
                </div>

                <div style="margin-bottom: 22px;">
                    <label style="display: block; font-size: 0.83rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; padding-left: 6px;">Colonia o Localidad en Álamo</label>
                    <select name="colonia" style="width: 100%; height: 50px; padding: 0 18px; border-radius: 9999px; border: 1.5px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.95rem; font-weight: 500;">
                        @php $userColonia = $user->colonia ?? 'Zona Centro'; @endphp
                        <option value="Zona Centro" {{ $userColonia === 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                        <option value="Col. Heroica Veracruz" {{ $userColonia === 'Col. Heroica Veracruz' ? 'selected' : '' }}>Col. Heroica Veracruz</option>
                        <option value="Col. Pantepec" {{ $userColonia === 'Col. Pantepec' ? 'selected' : '' }}>Col. Pantepec</option>
                        <option value="Col. Unidad y Trabajo" {{ $userColonia === 'Col. Unidad y Trabajo' ? 'selected' : '' }}>Col. Unidad y Trabajo</option>
                        <option value="Acceso Puente López Portillo" {{ $userColonia === 'Acceso Puente López Portillo' ? 'selected' : '' }}>Acceso Puente López Portillo</option>
                        <option value="Col. Benito Juárez" {{ $userColonia === 'Col. Benito Juárez' ? 'selected' : '' }}>Col. Benito Juárez</option>
                        <option value="Col. Francisco I. Madero" {{ $userColonia === 'Col. Francisco I. Madero' ? 'selected' : '' }}>Col. Francisco I. Madero</option>
                        <option value="Col. Gabino González" {{ $userColonia === 'Col. Gabino González' ? 'selected' : '' }}>Col. Gabino González</option>
                        <option value="Otra colonia / Comunidad" {{ $userColonia === 'Otra colonia / Comunidad' ? 'selected' : '' }}>Otra colonia / Comunidad</option>
                    </select>
                </div>

                <button type="submit" style="width: 100%; height: 52px; background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%); border: none; border-radius: 9999px; color: #FFF; font-weight: 700; font-size: 0.98rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 10px 20px -4px rgba(234, 88, 12, 0.35);">
                    <i class="fa-solid fa-check"></i> Actualizar Perfil
                </button>
            </form>
        </div>

        <!-- BOTÓN CERRAR SESIÓN -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width: 100%; height: 52px; background: transparent; border: 2px solid #EF4444; border-radius: 9999px; color: #EF4444; font-weight: 700; font-size: 0.98rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: background-color 0.2s;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Cerrar Sesión</span>
            </button>
        </form>

    </div>
@endsection
