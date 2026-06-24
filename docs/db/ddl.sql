-- ============================================================
-- SISTEMA DE AYUDANTÍAS UNIVERSITARIAS
-- DDL PostgreSQL
-- Sin CREATE TYPE / Sin ENUMS
-- ============================================================

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- USUARIOS
-- ============================================================

CREATE TABLE usuarios (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    external_user_ref VARCHAR(100) NOT NULL, -- referencia técnica interna; si no se envía, se usa correo_institucional
    codigo_universitario VARCHAR(50),
    correo_institucional VARCHAR(150) NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,

    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVO',

    fecha_registro TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_usuarios_external_user_ref UNIQUE (external_user_ref),
    CONSTRAINT uq_usuarios_codigo_universitario UNIQUE (codigo_universitario),
    CONSTRAINT uq_usuarios_correo_institucional UNIQUE (correo_institucional)
);

CREATE TABLE cuentas_usuario (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,
    password_hash TEXT NOT NULL,

    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVA',
    ultimo_acceso TIMESTAMP WITH TIME ZONE,

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_cuentas_usuario_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_cuentas_usuario_usuario UNIQUE (usuario_id)
);

CREATE TABLE roles_usuario (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_roles_usuario_nombre UNIQUE (nombre)
);

CREATE TABLE usuarios_roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,
    rol_id UUID NOT NULL,

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_usuarios_roles_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_usuarios_roles_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles_usuario(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_usuarios_roles_usuario_rol UNIQUE (usuario_id, rol_id)
);

-- ============================================================
-- MATERIAS Y OFERTAS DE AYUDANTÍA
-- ============================================================

CREATE TABLE materias (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,

    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVA',

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_materias_codigo UNIQUE (codigo)
);

CREATE TABLE ofertas_ayudantia (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    materia_id UUID NOT NULL,

    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,

    cupo_maximo INTEGER NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'BORRADOR',

    fecha_creacion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_ofertas_ayudantia_materia
        FOREIGN KEY (materia_id)
        REFERENCES materias(id)
        ON DELETE RESTRICT,

    CONSTRAINT ck_ofertas_ayudantia_cupo_maximo
        CHECK (cupo_maximo >= 0)
);

-- ============================================================
-- AUXILIARES
-- ============================================================

CREATE TABLE postulaciones_auxiliar (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,
    materia_id UUID NOT NULL,

    motivo TEXT,
    experiencia TEXT,

    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE',
    fecha_postulacion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_postulaciones_auxiliar_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_postulaciones_auxiliar_materia
        FOREIGN KEY (materia_id)
        REFERENCES materias(id)
        ON DELETE RESTRICT
);

CREATE TABLE auxiliares_materia (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,
    materia_id UUID NOT NULL,

    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVO',
    fecha_asignacion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_auxiliares_materia_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_auxiliares_materia_materia
        FOREIGN KEY (materia_id)
        REFERENCES materias(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_auxiliares_materia_usuario_materia UNIQUE (usuario_id, materia_id)
);

CREATE TABLE disponibilidad_auxiliar (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,

    dia_semana VARCHAR(20) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,

    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVA',

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_disponibilidad_auxiliar_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT ck_disponibilidad_auxiliar_horas
        CHECK (hora_fin > hora_inicio),

    CONSTRAINT uq_disponibilidad_auxiliar
        UNIQUE (usuario_id, dia_semana, hora_inicio, hora_fin)
);

-- ============================================================
-- SESIONES DE AYUDANTÍA
-- ============================================================

CREATE TABLE sesiones_ayudantia (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    oferta_ayudantia_id UUID NOT NULL,

    auxiliar_id UUID,

    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,

    -- Referencia externa al sistema de aulas.
    -- No existe tabla aulas en este módulo.
    aula_ref_id VARCHAR(100) NOT NULL,
    aula_nombre_cache VARCHAR(150),

    estado VARCHAR(30) NOT NULL DEFAULT 'PROGRAMADA',

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_sesiones_ayudantia_oferta
        FOREIGN KEY (oferta_ayudantia_id)
        REFERENCES ofertas_ayudantia(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sesiones_ayudantia_auxiliar
        FOREIGN KEY (auxiliar_id)
        REFERENCES usuarios(id)
        ON DELETE SET NULL,

    CONSTRAINT ck_sesiones_ayudantia_horas
        CHECK (hora_fin > hora_inicio),

    CONSTRAINT uq_sesiones_ayudantia_aula_horario
        UNIQUE (aula_ref_id, fecha, hora_inicio, hora_fin)
);

-- ============================================================
-- INSCRIPCIONES
-- ============================================================

CREATE TABLE inscripciones_ayudantia (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    usuario_id UUID NOT NULL,
    sesion_ayudantia_id UUID NOT NULL,

    estado VARCHAR(30) NOT NULL DEFAULT 'INSCRITO',
    fecha_inscripcion TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    asistencia BOOLEAN,

    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_inscripciones_ayudantia_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_inscripciones_ayudantia_sesion
        FOREIGN KEY (sesion_ayudantia_id)
        REFERENCES sesiones_ayudantia(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_inscripciones_ayudantia_usuario_sesion
        UNIQUE (usuario_id, sesion_ayudantia_id)
);

-- ============================================================
-- ÍNDICES RECOMENDADOS
-- ============================================================

CREATE INDEX idx_usuarios_estado
    ON usuarios(estado);

CREATE INDEX idx_materias_estado
    ON materias(estado);

CREATE INDEX idx_ofertas_ayudantia_materia
    ON ofertas_ayudantia(materia_id);

CREATE INDEX idx_ofertas_ayudantia_estado
    ON ofertas_ayudantia(estado);

CREATE INDEX idx_sesiones_ayudantia_oferta
    ON sesiones_ayudantia(oferta_ayudantia_id);

CREATE INDEX idx_sesiones_ayudantia_fecha
    ON sesiones_ayudantia(fecha);

CREATE INDEX idx_sesiones_ayudantia_aula_ref
    ON sesiones_ayudantia(aula_ref_id);

CREATE INDEX idx_inscripciones_ayudantia_usuario
    ON inscripciones_ayudantia(usuario_id);

CREATE INDEX idx_inscripciones_ayudantia_sesion
    ON inscripciones_ayudantia(sesion_ayudantia_id);

CREATE INDEX idx_postulaciones_auxiliar_usuario
    ON postulaciones_auxiliar(usuario_id);

CREATE INDEX idx_postulaciones_auxiliar_materia
    ON postulaciones_auxiliar(materia_id);

CREATE INDEX idx_auxiliares_materia_usuario
    ON auxiliares_materia(usuario_id);

CREATE INDEX idx_auxiliares_materia_materia
    ON auxiliares_materia(materia_id);


INSERT INTO roles_usuario (nombre, descripcion)
VALUES
('ESTUDIANTE', 'Usuario que puede inscribirse a sesiones de ayudantía'),
('AUXILIAR', 'Usuario que puede dictar sesiones de ayudantía'),
('COORDINADOR', 'Usuario que administra ofertas, sesiones y postulaciones'),
('ADMINISTRADOR', 'Usuario con permisos generales del sistema');