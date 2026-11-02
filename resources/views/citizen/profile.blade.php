@extends('citizen.layouts.app')

@section('title', 'Mi Perfil Ciudadano | Álamo Reporta')

@section('content')
    <div class="profile-container" style="max-width: 600px; margin: 0 auto; padding-bottom: 30px;">

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10B981; color: #10B981; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- TARJETA DE RESUMEN DE USUARIO -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 18px; padding: 24px; box-shadow: var(--shadow-card); margin-bottom: 20px; text-align: center;">
            <div style="width: 76px; height: 76px; border-radius: 50%; background: linear-gradient(135deg, #FACC15 0%, #EA580C 100%); color: #FFF; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin-bottom: 12px; box-shadow: 0 6px 16px -2px rgba(234, 88, 12, 0.4);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">{{ $user->name }}</h2>
            <div style="display: flex; justify-content: center; gap: 8px; align-items: center; margin-bottom: 16px;">
                <span style="background: var(--accent-orange-soft); color: var(--accent-orange); font-size: 0.78rem; font-weight: 700; padding: 3px 10px; border-radius: 6px;">@<span>{{ $user->username ?? 'user' }}</span></span>
                <span style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-size: 0.78rem; font-weight: 700; padding: 3px 10px; border-radius: 6px;"><i class="fa-solid fa-circle-check"></i> Cuenta Activa</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; border-top: 1px solid var(--border-subtle); padding-top: 16px;">
                <div style="background: var(--bg-elevated); padding: 12px; border-radius: 10px;">
                    <span style="font-size: 1.3rem; font-weight: 800; color: var(--accent-orange); display: block;">{{ $myReportsCount }}</span>
                    <span style="font-size: 0.78rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Mis Reportes</span>
                </div>
                <div style="background: var(--bg-elevated); padding: 12px; border-radius: 10px;">
                    <span style="font-size: 1.3rem; font-weight: 800; color: var(--text-primary); display: block;">{{ $user->colonia ?: 'Centro' }}</span>
                    <span style="font-size: 0.78rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Localidad</span>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE DATOS PERSONALES -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 18px; padding: 24px; box-shadow: var(--shadow-card); margin-bottom: 20px;">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-pen" style="color: var(--accent-orange);"></i> Datos de Contacto
            </h3>

            <form action="{{ route('citizen.profile.update') }}" method="POST">
                @csrf

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Correo Electrónico</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           style="width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-subtle); background: var(--bg-elevated); color: var(--text-muted); font-size: 0.9rem; cursor: not-allowed;">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Teléfono o WhatsApp</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="765 100 0000"
                           style="width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Colonia / Localidad en Álamo</label>
                    <select name="colonia" style="width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-subtle); background: var(--bg-input); color: var(--text-primary); font-size: 0.9rem;">
                        <option value="Zona Centro" {{ $user->colonia === 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                        <option value="Col. Heroica Veracruz" {{ $user->colonia === 'Col. Heroica Veracruz' ? 'selected' : '' }}>Col. Heroica Veracruz</option>
                        <option value="Col. Pantepec" {{ $user->colonia === 'Col. Pantepec' ? 'selected' : '' }}>Col. Pantepec</option>
                        <option value="Col. Unidad y Trabajo" {{ $user->colonia === 'Col. Unidad y Trabajo' ? 'selected' : '' }}>Col. Unidad y Trabajo</option>
                        <option value="Acceso Puente López Portillo" {{ $user->colonia === 'Acceso Puente López Portillo' ? 'selected' : '' }}>Acceso Puente López Portillo</option>
                        <option value="Col. Benito Juárez" {{ $user->colonia === 'Col. Benito Juárez' ? 'selected' : '' }}>Col. Benito Juárez</option>
                        <option value="Col. Francisco I. Madero" {{ $user->colonia === 'Col. Francisco I. Madero' ? 'selected' : '' }}>Col. Francisco I. Madero</option>
                        <option value="Col. Gabino González" {{ $user->colonia === 'Col. Gabino González' ? 'selected' : '' }}>Col. Gabino González</option>
                        <option value="Otra colonia / Comunidad" {{ $user->colonia === 'Otra colonia / Comunidad' ? 'selected' : '' }}>Otra colonia / Comunidad</option>
                    </select>
                </div>

                <button type="submit" style="width: 100%; height: 46px; background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%); border: none; border-radius: 10px; color: #FFF; font-weight: 700; font-size: 0.92rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </form>
        </div>

        <!-- PRÓXIMAS VALIDACIONES BIOMÉTRICAS -->
        <div style="background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-card); margin-bottom: 20px;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-fingerprint" style="color: var(--accent-orange);"></i> Verificación de Identidad Oficial (En Desarrollo)
            </h3>
            <p style="font-size: 0.82rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 12px;">
                Para la fase final se integrará validación oficial con fotografía de INE y escaneo biométrico (reconocimiento facial o de iris).
            </p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; background: var(--bg-elevated); padding: 5px 10px; border-radius: 6px; color: var(--text-secondary);"><i class="fa-solid fa-clock"></i> INE: Pendiente</span>
                <span style="font-size: 0.75rem; background: var(--bg-elevated); padding: 5px 10px; border-radius: 6px; color: var(--text-secondary);"><i class="fa-solid fa-clock"></i> Biometría Facial: Próximamente</span>
            </div>
        </div>

        <!-- BOTÓN CERRAR SESIÓN -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width: 100%; height: 48px; background: transparent; border: 1.5px solid #EF4444; border-radius: 12px; color: #EF4444; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Cerrar Sesión</span>
            </button>
        </form>

    </div>
@endsection

