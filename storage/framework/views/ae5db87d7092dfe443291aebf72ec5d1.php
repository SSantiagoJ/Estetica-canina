<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>


<link rel="stylesheet" href="<?php echo e(asset('css/perfil.css')); ?>">
<?php $__env->startSection('content'); ?>
    <div class="perfil-container">
        <!-- Sección de Perfil -->
        <div class="perfil-card">
            <h2>Mi Perfil</h2>
            <div class="perfil-content">
                <div class="perfil-avatar">
                    <img src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="Avatar">
                </div>
                <div class="perfil-info">
                    <h3>Bienvenido, <?php echo e($persona->nombres); ?></h3>
                    <p><strong>DNI:</strong> <?php echo e($persona->nro_documento); ?></p>
                    <p><strong>Correo:</strong> <?php echo e($usuario->correo); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo e($persona->telefono ?? 'No registrado'); ?></p>
                    <p><strong>Dirección:</strong> <?php echo e($persona->direccion ?? 'No registrada'); ?></p>
                    <button class="btn-edit" onclick="openEditPerfilModal()">✏️ Editar</button>
                </div>
            </div>
        </div>

        <!-- Sección de Mascotas -->
        <div class="mascotas-section">
            <div class="mascotas-header">
                <h2>Mis Mascotas</h2>
                <button class="btn-add-mascota" onclick="openModal()">
                    🐾 Añadir Mascota
                </button>
            </div>

            <div class="mascotas-grid" id="mascotasGrid">
                <?php $__empty_1 = true; $__currentLoopData = $mascotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mascota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mascota-card">
                        <div class="mascota-image">
                            <img src="<?php echo e(asset('images/default-pet.png')); ?>">
                        </div>
                        <div class="mascota-info">
                            <h3><strong><?php echo e($mascota->nombre); ?></strong></h3>
                            <p><?php echo e($mascota->sexo == 'Macho' ? '♂️' : '♀️'); ?> <?php echo e($mascota->sexo); ?></p>
                            <p>🐕 <?php echo e($mascota->raza ?? 'Sin raza'); ?></p>
                            <p>🎂 <?php echo e($mascota->edad); ?> años</p>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:8px;">
                            <button class="btn-edit-mascota"
                                onclick="openEditModal(<?php echo e($mascota->id_mascota); ?>, '<?php echo e($mascota->nombre); ?>', '<?php echo e($mascota->fecha_nacimiento); ?>', '<?php echo e($mascota->sexo); ?>', '<?php echo e($mascota->tamano); ?>', '<?php echo e($mascota->especie); ?>', '<?php echo e($mascota->raza); ?>', '<?php echo e($mascota->peso); ?>', '<?php echo e($mascota->descripcion); ?>')">
                                ✏️ Editar
                            </button>

                            <!-- Formulario para eliminar -->
                            <form action="<?php echo e(route('mascotas.destroy', $mascota->id_mascota)); ?>" method="POST"
                                  onsubmit="return confirm('¿Eliminar a <?php echo e($mascota->nombre); ?>? Esta acción no se puede deshacer.');"
                                  style="margin:0;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-delete-mascota">🗑️ Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="no-mascotas">No tienes mascotas registradas aún.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal para Registrar Mascota -->
        <div id="modalMascota" class="modal">
            <div class="modal-content">
                <h2>Registrar Mascota</h2>
                <form id="formMascota">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tamano">Tamaño</label>
                        <select id="tamano" name="tamano">
                            <option value="">Seleccionar</option>
                            <option value="Pequeño">Pequeño</option>
                            <option value="Mediano">Mediano</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="especie">Especie</label>
                        <select id="especie" name="especie" required>
                            <option value="">Seleccionar</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="raza">Raza</label>
                        <input type="text" id="raza" name="raza">
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="peso" name="peso">
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Editar Mascota -->
        <div id="modalEditarMascota" class="modal">
            <div class="modal-content">
                <h2>Editar Mascota</h2>
                <form id="formEditarMascota">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="edit_id_mascota" name="id_mascota">

                    <div class="form-group">
                        <label for="edit_nombre">Nombre</label>
                        <input type="text" id="edit_nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_sexo">Sexo</label>
                        <select id="edit_sexo" name="sexo" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_tamano">Tamaño</label>
                        <select id="edit_tamano" name="tamano">
                            <option value="">Seleccionar</option>
                            <option value="Pequeño">Pequeño</option>
                            <option value="Mediano">Mediano</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_especie">Especie</label>
                        <select id="edit_especie" name="especie" required>
                            <option value="">Seleccionar</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_raza">Raza</label>
                        <input type="text" id="edit_raza" name="raza">
                    </div>

                    <div class="form-group">
                        <label for="edit_peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="edit_peso" name="peso">
                    </div>

                    <div class="form-group">
                        <label for="edit_descripcion">Descripción</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Editar Perfil -->
        <div id="modalEditarPerfil" class="modal">
            <div class="modal-content">
                <h2>Editar Mi Perfil</h2>
                <form id="formEditarPerfil">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="form-group">
                        <label for="edit_nombres">Nombres</label>
                        <input type="text" id="edit_nombres" name="nombres" value="<?php echo e($persona->nombres); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_apellidos">Apellidos</label>
                        <input type="text" id="edit_apellidos" name="apellidos" value="<?php echo e($persona->apellidos); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_telefono">Telefono</label>
                        <input type="text" id="edit_telefono" name="telefono" value="<?php echo e($persona->telefono); ?>">
                    </div>

                    <div class="form-group">
                        <label for="edit_direccion">Direccion</label>
                        <input type="text" id="edit_direccion" name="direccion" value="<?php echo e($persona->direccion); ?>">
                    </div>

                    <div class="form-group">
                        <label for="edit_fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento"
                            value="<?php echo e($persona->fecha_nacimiento); ?>">
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeEditPerfilModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

<?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/perfil.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/perfil.blade.php ENDPATH**/ ?>