<section id="contacto" class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Contact Info Column -->
            <div class="lg:col-span-5 space-y-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-green bg-brand-greenLight px-3.5 py-1.5 rounded-full">Atención Directa</span>
                    <h2 class="font-poppins font-bold text-3xl sm:text-4xl text-slate-900 tracking-tight mt-3">Ponte en contacto con nosotros</h2>
                    <p class="text-slate-500 mt-2 text-sm sm:text-base leading-relaxed font-medium">
                        Estamos disponibles a través de múltiples canales oficiales para atender tus solicitudes, brindar asesoría o canalizar emergencias médicas de inmediato.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Phone -->
                    <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/60 shadow-xs">
                        <div class="w-10 h-10 bg-emerald-50 text-brand-green rounded-xl flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined font-bold" data-icon="phone">phone</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Teléfono de Soporte</span>
                            <span class="text-sm font-bold text-slate-800">809-555-8888</span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/60 shadow-xs">
                        <div class="w-10 h-10 bg-blue-50 text-brand-blue rounded-xl flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined font-bold" data-icon="mail">mail</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Correo Electrónico</span>
                            <span class="text-sm font-bold text-slate-800 font-mono">info@saludintegral.com.do</span>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/60 shadow-xs">
                        <div class="w-10 h-10 bg-emerald-50 text-brand-green rounded-xl flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined font-bold" data-icon="pin_drop">pin_drop</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Oficina Corporativa</span>
                            <span class="text-sm font-semibold text-slate-700">Av. Winston Churchill No. 1024, Santo Domingo, R.D.</span>
                        </div>
                    </div>

                    <!-- Hours -->
                    <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/60 shadow-xs">
                        <div class="w-10 h-10 bg-blue-50 text-brand-blue rounded-xl flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined font-bold" data-icon="calendar_month">calendar_month</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Horario de Atención</span>
                            <span class="text-sm font-semibold text-slate-700">Lunes a Viernes de 8:00 AM a 6:00 PM · Sábados de 9:00 AM a 1:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Column -->
            <div class="lg:col-span-7 bg-slate-50 border border-slate-100/60 rounded-3xl p-6 sm:p-8 shadow-sm">
                <form class="space-y-4" onsubmit="event.preventDefault(); alert('Gracias por su mensaje. Nos comunicaremos a la brevedad.');">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nombre Completo</label>
                            <input type="text" required placeholder="Ej: Ramón Valdez" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-4 bg-white text-sm text-slate-800 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Teléfono</label>
                            <input type="text" required placeholder="809-555-5555" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-4 bg-white text-sm text-slate-800 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Correo Electrónico</label>
                        <input type="email" required placeholder="ejemplo@correo.com" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-4 bg-white text-sm text-slate-800 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipo de Solicitud</label>
                        <select required class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-4 bg-white text-sm text-slate-700 font-semibold">
                            <option value="">Selecciona una opción</option>
                            <option value="afiliacion">Nueva Afiliación</option>
                            <option value="reclamacion">Consulta de Reclamación / Reembolso</option>
                            <option value="red">Información de Red de Prestadores</option>
                            <option value="general">Información General</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Mensaje</label>
                        <textarea required rows="4" placeholder="Escribe tu consulta aquí..." class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-4 bg-white text-sm text-slate-800 font-medium"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-full text-white font-bold text-xs bg-brand-blue hover:bg-brand-blueHover transition shadow-md shadow-brand-blue/15 active:scale-[0.98]">
                        Enviar Mensaje
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</section>
