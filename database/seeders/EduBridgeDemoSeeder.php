<?php

namespace Database\Seeders;

use App\Models\AuxiliarMateria;
use App\Models\CuentaUsuario;
use App\Models\DisponibilidadAuxiliar;
use App\Models\InscripcionAyudantia;
use App\Models\Materia;
use App\Models\OfertaAyudantia;
use App\Models\PostulacionAuxiliar;
use App\Models\RolUsuario;
use App\Models\SesionAyudantia;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EduBridgeDemoSeeder extends Seeder
{
    private const PASSWORD_DEMO = 'Password123!';

    /**
     * Cantidades por defecto para una base demo grande.
     * Puedes modificarlas en .env sin tocar este archivo:
     *
     * EDUBRIDGE_SEED_STUDENTS=160
     * EDUBRIDGE_SEED_AUXILIARIES=24
     * EDUBRIDGE_SEED_OFFERS_PER_SUBJECT=4
     * EDUBRIDGE_SEED_SESSIONS_PER_OFFER=3
     */
    private int $studentCount;
    private int $auxiliaryCount;
    private int $offersPerSubject;
    private int $sessionsPerOffer;

    public function run(): void
    {
        $this->studentCount = max(40, (int) env('EDUBRIDGE_SEED_STUDENTS', 160));
        $this->auxiliaryCount = max(8, (int) env('EDUBRIDGE_SEED_AUXILIARIES', 24));
        $this->offersPerSubject = max(2, (int) env('EDUBRIDGE_SEED_OFFERS_PER_SUBJECT', 4));
        $this->sessionsPerOffer = max(2, (int) env('EDUBRIDGE_SEED_SESSIONS_PER_OFFER', 3));

        DB::transaction(function (): void {
            $roles = $this->seedRoles();
            $usuarios = $this->seedUsuarios($roles);
            $materias = $this->seedMaterias();
            $this->seedPostulaciones($usuarios, $materias);
            $this->seedAuxiliaresMateria($usuarios, $materias);
            $this->seedDisponibilidad($usuarios);
            $ofertas = $this->seedOfertas($materias);
            $sesiones = $this->seedSesiones($usuarios, $ofertas);
            $this->seedInscripciones($usuarios, $sesiones);
        });
    }

    /**
     * @return array<string, RolUsuario>
     */
    private function seedRoles(): array
    {
        $rolesBase = [
            'ESTUDIANTE' => 'Usuario que puede inscribirse a sesiones de ayudantía',
            'AUXILIAR' => 'Usuario que puede dictar sesiones de ayudantía',
            'COORDINADOR' => 'Usuario que administra ofertas, sesiones y postulaciones',
            'ADMINISTRADOR' => 'Usuario con permisos generales del sistema',
        ];

        $roles = [];

        foreach ($rolesBase as $nombre => $descripcion) {
            $roles[$nombre] = RolUsuario::query()->firstOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => $descripcion]
            );
        }

        return $roles;
    }

    /**
     * @param array<string, RolUsuario> $roles
     * @return array<string, array<string, Usuario>>
     */
    private function seedUsuarios(array $roles): array
    {
        $usuarios = [
            'admin' => [],
            'coordinadores' => [],
            'auxiliares' => [],
            'estudiantes' => [],
        ];

        $admin = $this->upsertUsuario(
            roles: $roles,
            key: 'admin',
            rol: 'ADMINISTRADOR',
            codigo: 'ADM-0001',
            correo: 'admin@edubridge.test',
            nombre: 'Admin EduBridge'
        );
        $usuarios['admin']['admin'] = $admin;

        $coordinadoresBase = [
            ['coordinador', 'COO-0001', 'coordinador@edubridge.test', 'Carla Coordinadora Académica'],
            ['coordinador_operaciones', 'COO-0002', 'operaciones@edubridge.test', 'Mario Coordinador de Operaciones'],
        ];

        foreach ($coordinadoresBase as [$key, $codigo, $correo, $nombre]) {
            $usuarios['coordinadores'][$key] = $this->upsertUsuario(
                roles: $roles,
                key: $key,
                rol: 'COORDINADOR',
                codigo: $codigo,
                correo: $correo,
                nombre: $nombre
            );
        }

        $auxiliarNames = [
            'Ana Quispe Rojas', 'Luis Mamani Flores', 'María Aguilar Suárez', 'Jorge Vargas Peña',
            'Valeria Roca Méndez', 'Pedro Cuéllar Ortiz', 'Andrea Rivero Paz', 'Rodrigo Salvatierra Gómez',
            'Luciana Arias Moreno', 'Mateo Ríos Céspedes', 'Gabriela Soto Montero', 'Renato Medina Justiniano',
            'Melissa Hurtado Paz', 'Fernando Castro Lima', 'Micaela Paredes Justiniano', 'Alejandro Rojas Ibáñez',
            'Paola Mendoza Vaca', 'Natalia Vaca Landívar', 'Sebastián Mercado Roca', 'Daniela Justiniano Ruiz',
            'Hugo Antelo Ribera', 'Fernanda Ibáñez Montero', 'Carlos Peña Vaca', 'Camila Núñez Salvatierra',
            'Diego Suárez Mercado', 'Sofía Rivero Sandoval', 'Andrés Ortiz Ribera', 'Valentina Paz Arce',
            'Emilio Vargas Dávila', 'Isabella Saucedo Roca', 'Nicolás Franco Terrazas', 'Julieta Durán Áñez',
        ];

        for ($i = 1; $i <= $this->auxiliaryCount; $i++) {
            $name = $auxiliarNames[($i - 1) % count($auxiliarNames)];
            $key = sprintf('auxiliar_%03d', $i);

            $usuarios['auxiliares'][$key] = $this->upsertUsuario(
                roles: $roles,
                key: $key,
                rol: 'AUXILIAR',
                codigo: sprintf('AUX-%04d', 1000 + $i),
                correo: sprintf('auxiliar%03d@edubridge.test', $i),
                nombre: $name
            );
        }

        // Alias fijos para no romper pruebas previas ni documentación.
        $usuarios['auxiliares']['auxiliar_ana'] = $usuarios['auxiliares']['auxiliar_001'];
        $usuarios['auxiliares']['auxiliar_luis'] = $usuarios['auxiliares']['auxiliar_002'];
        $usuarios['auxiliares']['auxiliar_maria'] = $usuarios['auxiliares']['auxiliar_003'];

        $demoStudent = $this->upsertUsuario(
            roles: $roles,
            key: 'estudiante_demo',
            rol: 'ESTUDIANTE',
            codigo: 'EST-2001',
            correo: 'estudiante@edubridge.test',
            nombre: 'Pablo Estudiante Demo'
        );
        $usuarios['estudiantes']['estudiante_demo'] = $demoStudent;

        $firstNames = [
            'Sofía', 'Diego', 'Camila', 'Andrés', 'Lucía', 'Mateo', 'Gabriela', 'Renato', 'Melissa', 'Fernando',
            'Micaela', 'Alejandro', 'Paola', 'Rodrigo', 'Natalia', 'Sebastián', 'Daniela', 'Hugo', 'Fernanda', 'Carlos',
            'Valentina', 'Emilio', 'Isabella', 'Nicolás', 'Julieta', 'Bruno', 'Mariana', 'Santiago', 'Elena', 'Gonzalo',
            'Antonella', 'Mauricio', 'Adriana', 'Pablo', 'Ximena', 'Rafael', 'Claudia', 'Tomás', 'Noelia', 'Iván',
        ];
        $lastNames = [
            'Rivero', 'Suárez', 'Núñez', 'Ortiz', 'Arias', 'Ríos', 'Soto', 'Medina', 'Hurtado', 'Castro',
            'Paredes', 'Rojas', 'Mendoza', 'Salazar', 'Vaca', 'Mercado', 'Justiniano', 'Antelo', 'Ibáñez', 'Peña',
            'Paz', 'Vargas', 'Saucedo', 'Franco', 'Durán', 'Dávila', 'Ribera', 'Montero', 'Landívar', 'Arce',
        ];
        $secondLastNames = [
            'Sandoval', 'Mercado', 'Salvatierra', 'Ribera', 'Moreno', 'Céspedes', 'Montero', 'Justiniano', 'Paz', 'Lima',
            'Justiniano', 'Ibáñez', 'Vaca', 'Pinto', 'Landívar', 'Roca', 'Ruiz', 'Ribera', 'Montero', 'Vaca',
        ];

        for ($i = 1; $i <= $this->studentCount; $i++) {
            $key = sprintf('estudiante_%03d', $i);
            $nombre = sprintf(
                '%s %s %s',
                $firstNames[($i - 1) % count($firstNames)],
                $lastNames[($i * 3 - 1) % count($lastNames)],
                $secondLastNames[($i * 5 - 1) % count($secondLastNames)]
            );

            $usuarios['estudiantes'][$key] = $this->upsertUsuario(
                roles: $roles,
                key: $key,
                rol: 'ESTUDIANTE',
                codigo: sprintf('EST-%04d', 2001 + $i),
                correo: sprintf('estudiante%03d@edubridge.test', $i),
                nombre: $nombre
            );
        }

        return $usuarios;
    }

    /**
     * @param array<string, RolUsuario> $roles
     */
    private function upsertUsuario(array $roles, string $key, string $rol, string $codigo, string $correo, string $nombre): Usuario
    {
        /** @var Usuario $usuario */
        $usuario = Usuario::query()->updateOrCreate(
            ['correo_institucional' => $correo],
            [
                'external_user_ref' => 'seed-'.$key,
                'codigo_universitario' => $codigo,
                'nombre_completo' => $nombre,
                'estado' => 'ACTIVO',
                'fecha_registro' => Carbon::now()->subDays(($this->hashNumber($key) % 90) + 1),
            ]
        );

        CuentaUsuario::query()->updateOrCreate(
            ['usuario_id' => $usuario->id],
            [
                'password_hash' => Hash::make(self::PASSWORD_DEMO),
                'estado' => 'ACTIVA',
            ]
        );

        $usuario->roles()->syncWithoutDetaching([
            $roles[$rol]->id => ['id' => $this->pivotId($usuario->id, $roles[$rol]->id)],
        ]);

        return $usuario->refresh();
    }

    /**
     * @return array<string, Materia>
     */
    private function seedMaterias(): array
    {
        $materiasBase = [
            'MAT-101' => ['Cálculo I', 'Límites, continuidad, derivadas y aplicaciones introductorias.'],
            'MAT-102' => ['Álgebra Lineal', 'Matrices, sistemas lineales, espacios vectoriales y transformaciones.'],
            'MAT-201' => ['Cálculo II', 'Integrales, series, funciones de varias variables y aplicaciones.'],
            'MAT-301' => ['Ecuaciones Diferenciales', 'Ecuaciones ordinarias, modelos dinámicos y sistemas lineales.'],
            'FIS-101' => ['Física I', 'Cinemática, dinámica, trabajo, energía y cantidad de movimiento.'],
            'FIS-202' => ['Física II', 'Electricidad, magnetismo, circuitos y fenómenos ondulatorios.'],
            'QUI-101' => ['Química General', 'Estequiometría, enlaces, soluciones, equilibrio y reacciones químicas.'],
            'INF-101' => ['Programación I', 'Lógica de programación, algoritmos, funciones y estructuras básicas.'],
            'INF-202' => ['Estructuras de Datos', 'Listas, pilas, colas, árboles, grafos y complejidad algorítmica.'],
            'INF-301' => ['Bases de Datos', 'Modelo relacional, SQL, normalización, transacciones y consultas.'],
            'INF-330' => ['Desarrollo Web', 'Frontend, backend, API REST, autenticación y despliegue.'],
            'ECO-101' => ['Microeconomía', 'Oferta, demanda, elasticidad, costos, competencia y equilibrio.'],
            'ECO-202' => ['Macroeconomía', 'PIB, inflación, desempleo, política fiscal y monetaria.'],
            'CON-101' => ['Contabilidad Básica', 'Asientos, libro diario, mayor, balance de comprobación y ajustes.'],
            'CON-202' => ['Contabilidad de Costos', 'Costeo por órdenes, procesos, costos estándar y variaciones.'],
            'ADM-101' => ['Administración General', 'Proceso administrativo, diseño organizacional, liderazgo y control.'],
            'ADM-220' => ['Gestión de Operaciones', 'Procesos, capacidad, inventarios, calidad y productividad.'],
            'FIN-201' => ['Finanzas I', 'Valor del dinero, análisis financiero, VAN, TIR y riesgo.'],
            'EST-101' => ['Estadística I', 'Estadística descriptiva, probabilidad y distribuciones discretas.'],
            'EST-202' => ['Estadística II', 'Inferencia, intervalos de confianza, pruebas de hipótesis y regresión.'],
            'INV-201' => ['Investigación de Operaciones', 'Programación lineal, método gráfico, simplex y sensibilidad.'],
            'MKT-201' => ['Marketing Estratégico', 'Segmentación, targeting, posicionamiento, propuesta de valor y marca.'],
            'MKT-305' => ['Investigación de Mercados', 'Diseño de investigación, encuestas, muestreo y análisis de datos.'],
            'LOG-210' => ['Logística y Supply Chain', 'Abastecimiento, inventarios, distribución, transporte y servicio.'],
            'DER-101' => ['Derecho Empresarial', 'Contratos, sociedades, obligaciones y marco legal empresarial.'],
            'PSI-101' => ['Psicología General', 'Procesos cognitivos, aprendizaje, personalidad y conducta.'],
            'COM-101' => ['Comunicación Oral y Escrita', 'Redacción académica, argumentación, exposición y comprensión lectora.'],
            'ING-101' => ['Inglés Técnico I', 'Lectura técnica, vocabulario académico y comunicación profesional básica.'],
            'EMP-301' => ['Emprendimiento', 'Modelo de negocio, validación, propuesta de valor y pitch.'],
            'MET-101' => ['Metodología de la Investigación', 'Planteamiento, marco teórico, diseño metodológico y reporte.'],
            'SIS-250' => ['Análisis y Diseño de Sistemas', 'Requerimientos, UML, casos de uso, procesos y arquitectura.'],
            'SEG-310' => ['Seguridad Informática', 'Principios de seguridad, amenazas, controles y buenas prácticas.'],
        ];

        $materias = [];

        foreach ($materiasBase as $codigo => [$nombre, $descripcion]) {
            /** @var Materia $materia */
            $materia = Materia::query()->updateOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'estado' => 'ACTIVA',
                ]
            );

            $materias[$codigo] = $materia;
        }

        return $materias;
    }

    /**
     * @param array<string, array<string, Usuario>> $usuarios
     * @param array<string, Materia> $materias
     */
    private function seedPostulaciones(array $usuarios, array $materias): void
    {
        $materiaItems = array_values($materias);
        $estados = ['APROBADA', 'PENDIENTE', 'RECHAZADA', 'CANCELADA'];
        $motivos = [
            'Deseo apoyar a mis compañeros en temas donde tengo buen rendimiento académico.',
            'Me interesa reforzar la comunidad académica mediante ayudantías prácticas.',
            'Tengo experiencia explicando ejercicios y preparando repasos antes de parciales.',
            'Quiero aportar con sesiones ordenadas, ejercicios resueltos y seguimiento a estudiantes.',
        ];
        $experiencias = [
            'Fui monitor informal del grupo de estudio y elaboré guías de ejercicios.',
            'Aprobé la materia con alto rendimiento y ayudé a compañeros durante el semestre.',
            'Tengo experiencia resolviendo ejercicios en pizarra y explicando paso a paso.',
            'Participé en tutorías internas, olimpiadas académicas o proyectos relacionados.',
        ];

        $auxIndex = 0;
        foreach ($usuarios['auxiliares'] as $key => $auxiliar) {
            if (! str_starts_with($key, 'auxiliar_0')) {
                continue;
            }

            for ($j = 0; $j < 5; $j++) {
                $materia = $materiaItems[($auxIndex * 3 + $j * 2) % count($materiaItems)];
                $estado = $estados[($auxIndex + $j) % count($estados)];

                PostulacionAuxiliar::query()->updateOrCreate(
                    [
                        'usuario_id' => $auxiliar->id,
                        'materia_id' => $materia->id,
                    ],
                    [
                        'motivo' => $motivos[($auxIndex + $j) % count($motivos)],
                        'experiencia' => $experiencias[($auxIndex + $j) % count($experiencias)],
                        'estado' => $estado,
                        'fecha_postulacion' => Carbon::now()->subDays(40 - (($auxIndex + $j) % 25)),
                    ]
                );
            }

            $auxIndex++;
        }

        // Algunas postulaciones de estudiantes que aún están en revisión para poblar pantallas administrativas.
        $studentIndex = 0;
        foreach ($usuarios['estudiantes'] as $key => $estudiante) {
            if ($key === 'estudiante_demo' || $studentIndex >= 30) {
                $studentIndex++;
                continue;
            }

            $materia = $materiaItems[($studentIndex * 5) % count($materiaItems)];

            PostulacionAuxiliar::query()->updateOrCreate(
                [
                    'usuario_id' => $estudiante->id,
                    'materia_id' => $materia->id,
                ],
                [
                    'motivo' => 'Postulación de prueba para validar flujo de revisión por coordinación.',
                    'experiencia' => 'Participación en grupos de estudio y apoyo a compañeros.',
                    'estado' => $estados[$studentIndex % count($estados)],
                    'fecha_postulacion' => Carbon::now()->subDays(15 - ($studentIndex % 10)),
                ]
            );

            $studentIndex++;
        }
    }

    /**
     * @param array<string, array<string, Usuario>> $usuarios
     * @param array<string, Materia> $materias
     */
    private function seedAuxiliaresMateria(array $usuarios, array $materias): void
    {
        $materiaItems = array_values($materias);
        $auxIndex = 0;

        foreach ($usuarios['auxiliares'] as $key => $auxiliar) {
            if (! str_starts_with($key, 'auxiliar_0')) {
                continue;
            }

            for ($j = 0; $j < 4; $j++) {
                $materia = $materiaItems[($auxIndex * 3 + $j * 2) % count($materiaItems)];

                AuxiliarMateria::query()->updateOrCreate(
                    [
                        'usuario_id' => $auxiliar->id,
                        'materia_id' => $materia->id,
                    ],
                    [
                        'estado' => ($j === 3 && $auxIndex % 5 === 0) ? 'INACTIVO' : 'ACTIVO',
                        'fecha_asignacion' => Carbon::now()->subDays(30 - (($auxIndex + $j) % 18)),
                    ]
                );
            }

            $auxIndex++;
        }
    }

    /**
     * @param array<string, array<string, Usuario>> $usuarios
     */
    private function seedDisponibilidad(array $usuarios): void
    {
        $dias = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
        $bloques = [
            ['08:00', '09:30'],
            ['10:00', '11:30'],
            ['12:00', '13:30'],
            ['14:00', '15:30'],
            ['16:00', '17:30'],
            ['18:00', '19:30'],
        ];

        $auxIndex = 0;
        foreach ($usuarios['auxiliares'] as $key => $auxiliar) {
            if (! str_starts_with($key, 'auxiliar_0')) {
                continue;
            }

            for ($j = 0; $j < 4; $j++) {
                $dia = $dias[($auxIndex + $j) % count($dias)];
                [$inicio, $fin] = $bloques[($auxIndex + $j * 2) % count($bloques)];

                DisponibilidadAuxiliar::query()->updateOrCreate(
                    [
                        'usuario_id' => $auxiliar->id,
                        'dia_semana' => $dia,
                        'hora_inicio' => $inicio,
                        'hora_fin' => $fin,
                    ],
                    [
                        'estado' => 'ACTIVA',
                    ]
                );
            }

            $auxIndex++;
        }
    }

    /**
     * @param array<string, Materia> $materias
     * @return array<string, OfertaAyudantia>
     */
    private function seedOfertas(array $materias): array
    {
        $ofertas = [];
        $templates = [
            ['Repaso intensivo de %s', 'Sesiones guiadas para reforzar conceptos centrales, resolver ejercicios y prepararse para evaluación.', 12],
            ['Taller práctico de %s', 'Taller con ejercicios progresivos, casos aplicados y resolución colaborativa.', 10],
            ['Clínica de dudas de %s', 'Espacio para resolver dudas frecuentes, errores comunes y ejercicios tipo parcial.', 8],
            ['Preparación parcial de %s', 'Plan de práctica rápida con problemas representativos y explicación paso a paso.', 15],
            ['Nivelación base de %s', 'Refuerzo desde fundamentos para estudiantes que necesitan ordenar conceptos antes de avanzar.', 9],
        ];

        $estadoPattern = ['PUBLICADA', 'PUBLICADA', 'PUBLICADA', 'PUBLICADA', 'BORRADOR', 'CERRADA', 'CANCELADA'];
        $materiaIndex = 0;

        foreach ($materias as $codigo => $materia) {
            for ($i = 1; $i <= $this->offersPerSubject; $i++) {
                [$tituloTpl, $descripcion, $cupoBase] = $templates[($i - 1) % count($templates)];
                $estado = $estadoPattern[($materiaIndex + $i - 1) % count($estadoPattern)];
                $titulo = sprintf($tituloTpl, $materia->nombre).' #'.$i;
                $key = sprintf('oferta_%s_%02d', Str::slug($codigo, '_'), $i);

                /** @var OfertaAyudantia $oferta */
                $oferta = OfertaAyudantia::query()->updateOrCreate(
                    ['titulo' => $titulo],
                    [
                        'materia_id' => $materia->id,
                        'descripcion' => $descripcion.' Materia: '.$materia->nombre.'. Código: '.$codigo.'.',
                        'cupo_maximo' => $cupoBase + (($materiaIndex + $i) % 6),
                        'estado' => $estado,
                        'fecha_creacion' => Carbon::now()->subDays(45 - (($materiaIndex + $i) % 30)),
                    ]
                );

                $ofertas[$key] = $oferta;
            }

            $materiaIndex++;
        }

        // Alias fijos para que el frontend tenga datos conocidos con títulos más humanos.
        $ofertas['oferta_calculo_derivadas'] = $this->upsertOfertaEspecial(
            materia: $materias['MAT-101'],
            titulo: 'Cálculo I: derivadas paso a paso',
            descripcion: 'Ayudantía enfocada en reglas de derivación, interpretación gráfica y problemas aplicados.',
            cupo: 12,
            estado: 'PUBLICADA'
        );

        $ofertas['oferta_prog_logica'] = $this->upsertOfertaEspecial(
            materia: $materias['INF-101'],
            titulo: 'Programación I: lógica y algoritmos',
            descripcion: 'Apoyo para pseudocódigo, estructuras condicionales, ciclos, funciones y depuración básica.',
            cupo: 14,
            estado: 'PUBLICADA'
        );

        $ofertas['oferta_contabilidad_diario'] = $this->upsertOfertaEspecial(
            materia: $materias['CON-101'],
            titulo: 'Contabilidad Básica: libro diario y mayor',
            descripcion: 'Práctica guiada de asientos, mayorización, balance de comprobación y ajustes.',
            cupo: 12,
            estado: 'PUBLICADA'
        );

        $ofertas['oferta_fisica_finalizada'] = $this->upsertOfertaEspecial(
            materia: $materias['FIS-101'],
            titulo: 'Física I: repaso histórico finalizado',
            descripcion: 'Oferta histórica para validar sesiones finalizadas y asistencia.',
            cupo: 10,
            estado: 'CERRADA'
        );

        return $ofertas;
    }

    private function upsertOfertaEspecial(Materia $materia, string $titulo, string $descripcion, int $cupo, string $estado): OfertaAyudantia
    {
        /** @var OfertaAyudantia $oferta */
        $oferta = OfertaAyudantia::query()->updateOrCreate(
            ['titulo' => $titulo],
            [
                'materia_id' => $materia->id,
                'descripcion' => $descripcion,
                'cupo_maximo' => $cupo,
                'estado' => $estado,
                'fecha_creacion' => Carbon::now()->subDays(12),
            ]
        );

        return $oferta;
    }

    /**
     * @param array<string, array<string, Usuario>> $usuarios
     * @param array<string, OfertaAyudantia> $ofertas
     * @return array<string, SesionAyudantia>
     */
    private function seedSesiones(array $usuarios, array $ofertas): array
    {
        $sesiones = [];
        $baseDate = Carbon::today()->addDays(1);
        $bloques = [
            ['08:00', '09:30'],
            ['10:00', '11:30'],
            ['12:00', '13:30'],
            ['14:00', '15:30'],
            ['16:00', '17:30'],
            ['18:00', '19:30'],
        ];
        $auxiliares = array_values(array_filter(
            $usuarios['auxiliares'],
            static fn ($usuario, $key): bool => str_starts_with((string) $key, 'auxiliar_0'),
            ARRAY_FILTER_USE_BOTH
        ));

        $offerIndex = 0;
        foreach ($ofertas as $ofertaKey => $oferta) {
            for ($i = 1; $i <= $this->sessionsPerOffer; $i++) {
                [$inicio, $fin] = $bloques[($offerIndex + $i) % count($bloques)];
                $fecha = $this->sessionDateForOffer($oferta->estado, $baseDate, $offerIndex, $i);
                $estado = $this->sessionStatusForOffer($oferta->estado, $offerIndex, $i);
                $aulaNumero = (($offerIndex * $this->sessionsPerOffer) + $i) % 80 + 1;
                $aulaRef = sprintf('SEED-AULA-%03d-%02d-%02d', $offerIndex + 1, $i, $aulaNumero);
                $key = sprintf('%s_sesion_%02d', $ofertaKey, $i);

                /** @var SesionAyudantia $sesion */
                $sesion = SesionAyudantia::query()->updateOrCreate(
                    [
                        'aula_ref_id' => $aulaRef,
                        'fecha' => $fecha->toDateString(),
                        'hora_inicio' => $inicio,
                        'hora_fin' => $fin,
                    ],
                    [
                        'oferta_ayudantia_id' => $oferta->id,
                        'auxiliar_id' => $auxiliares[($offerIndex + $i) % count($auxiliares)]->id,
                        'aula_nombre_cache' => sprintf('Aula %03d - Bloque %s', $aulaNumero, chr(65 + ($aulaNumero % 5))),
                        'estado' => $estado,
                    ]
                );

                $sesiones[$key] = $sesion;
            }

            $offerIndex++;
        }

        // Sesiones con keys fijas para pruebas rápidas desde el frontend.
        $sesiones['sesion_prog_1'] = $this->upsertSesionEspecial(
            oferta: $ofertas['oferta_prog_logica'],
            auxiliar: $usuarios['auxiliares']['auxiliar_maria'],
            fecha: Carbon::today()->addDays(2),
            inicio: '15:00',
            fin: '16:30',
            aulaRef: 'DEMO-LAB-INF-1',
            aulaNombre: 'Laboratorio de Informática 1',
            estado: 'PROGRAMADA'
        );

        $sesiones['sesion_conta_1'] = $this->upsertSesionEspecial(
            oferta: $ofertas['oferta_contabilidad_diario'],
            auxiliar: $usuarios['auxiliares']['auxiliar_004'],
            fecha: Carbon::today()->addDays(4),
            inicio: '18:00',
            fin: '19:30',
            aulaRef: 'DEMO-AULA-CON-1',
            aulaNombre: 'Aula Contabilidad 1',
            estado: 'PROGRAMADA'
        );

        $sesiones['sesion_historica_1'] = $this->upsertSesionEspecial(
            oferta: $ofertas['oferta_fisica_finalizada'],
            auxiliar: $usuarios['auxiliares']['auxiliar_luis'],
            fecha: Carbon::today()->subDays(8),
            inicio: '09:00',
            fin: '10:30',
            aulaRef: 'DEMO-AULA-HIS-1',
            aulaNombre: 'Aula Histórica 1',
            estado: 'FINALIZADA'
        );

        return $sesiones;
    }

    private function sessionDateForOffer(string $offerStatus, Carbon $baseDate, int $offerIndex, int $sessionNumber): Carbon
    {
        if ($offerStatus === 'CERRADA') {
            return Carbon::today()->subDays(5 + (($offerIndex + $sessionNumber) % 20));
        }

        if ($offerStatus === 'CANCELADA') {
            return Carbon::today()->addDays(3 + (($offerIndex + $sessionNumber) % 12));
        }

        return $baseDate->copy()->addDays(($offerIndex * 2 + $sessionNumber) % 35);
    }

    private function sessionStatusForOffer(string $offerStatus, int $offerIndex, int $sessionNumber): string
    {
        if ($offerStatus === 'CERRADA') {
            return 'FINALIZADA';
        }

        if ($offerStatus === 'CANCELADA') {
            return 'CANCELADA';
        }

        if ($offerStatus === 'BORRADOR') {
            return ($sessionNumber % 2 === 0) ? 'CANCELADA' : 'PROGRAMADA';
        }

        return (($offerIndex + $sessionNumber) % 14 === 0) ? 'CANCELADA' : 'PROGRAMADA';
    }

    private function upsertSesionEspecial(
        OfertaAyudantia $oferta,
        Usuario $auxiliar,
        Carbon $fecha,
        string $inicio,
        string $fin,
        string $aulaRef,
        string $aulaNombre,
        string $estado
    ): SesionAyudantia {
        /** @var SesionAyudantia $sesion */
        $sesion = SesionAyudantia::query()->updateOrCreate(
            [
                'aula_ref_id' => $aulaRef,
                'fecha' => $fecha->toDateString(),
                'hora_inicio' => $inicio,
                'hora_fin' => $fin,
            ],
            [
                'oferta_ayudantia_id' => $oferta->id,
                'auxiliar_id' => $auxiliar->id,
                'aula_nombre_cache' => $aulaNombre,
                'estado' => $estado,
            ]
        );

        return $sesion;
    }

    /**
     * @param array<string, array<string, Usuario>> $usuarios
     * @param array<string, SesionAyudantia> $sesiones
     */
    private function seedInscripciones(array $usuarios, array $sesiones): void
    {
        $estudiantes = array_values(array_filter(
            $usuarios['estudiantes'],
            static fn ($usuario, $key): bool => $key === 'estudiante_demo' || str_starts_with((string) $key, 'estudiante_0'),
            ARRAY_FILTER_USE_BOTH
        ));

        $sessionIndex = 0;
        foreach ($sesiones as $sesionKey => $sesion) {
            $oferta = $sesion->ofertaAyudantia()->first();
            $cupo = max(4, (int) optional($oferta)->cupo_maximo);

            if ($sesion->estado === 'PROGRAMADA') {
                $cantidad = min(count($estudiantes), max(2, min($cupo + 3, 10 + ($sessionIndex % 6))));
                for ($i = 0; $i < $cantidad; $i++) {
                    $studentOffset = ($sessionIndex * 7 + $i * 3) % count($estudiantes);
                    $estudiante = $estudiantes[$studentOffset];
                    $estado = ($i >= $cupo) ? 'EN_ESPERA' : (($i % 11 === 0) ? 'CANCELADO' : 'INSCRITO');

                    InscripcionAyudantia::query()->updateOrCreate(
                        [
                            'usuario_id' => $estudiante->id,
                            'sesion_ayudantia_id' => $sesion->id,
                        ],
                        [
                            'estado' => $estado,
                            'fecha_inscripcion' => Carbon::now()->subDays(($i + $sessionIndex) % 12),
                            'asistencia' => null,
                        ]
                    );
                }
            } elseif ($sesion->estado === 'FINALIZADA') {
                $cantidad = min(count($estudiantes), max(4, min($cupo, 12)));
                for ($i = 0; $i < $cantidad; $i++) {
                    $estudiante = $estudiantes[($sessionIndex * 5 + $i * 2) % count($estudiantes)];
                    $asistio = ($i % 5 !== 0);

                    InscripcionAyudantia::query()->updateOrCreate(
                        [
                            'usuario_id' => $estudiante->id,
                            'sesion_ayudantia_id' => $sesion->id,
                        ],
                        [
                            'estado' => $asistio ? 'ASISTIO' : 'NO_ASISTIO',
                            'fecha_inscripcion' => Carbon::parse($sesion->fecha)->subDays(4),
                            'asistencia' => $asistio,
                        ]
                    );
                }
            } elseif ($sesion->estado === 'CANCELADA' && $sessionIndex % 4 === 0) {
                $cantidad = min(count($estudiantes), 4);
                for ($i = 0; $i < $cantidad; $i++) {
                    $estudiante = $estudiantes[($sessionIndex + $i) % count($estudiantes)];

                    InscripcionAyudantia::query()->updateOrCreate(
                        [
                            'usuario_id' => $estudiante->id,
                            'sesion_ayudantia_id' => $sesion->id,
                        ],
                        [
                            'estado' => 'CANCELADO',
                            'fecha_inscripcion' => Carbon::now()->subDays(($i + 1) % 7),
                            'asistencia' => null,
                        ]
                    );
                }
            }

            $sessionIndex++;
        }

        // Datos controlados para probar rápido con estudiante@edubridge.test.
        InscripcionAyudantia::query()->updateOrCreate(
            [
                'usuario_id' => $usuarios['estudiantes']['estudiante_demo']->id,
                'sesion_ayudantia_id' => $sesiones['sesion_prog_1']->id,
            ],
            [
                'estado' => 'INSCRITO',
                'fecha_inscripcion' => Carbon::now()->subDays(2),
                'asistencia' => null,
            ]
        );

        InscripcionAyudantia::query()->updateOrCreate(
            [
                'usuario_id' => $usuarios['estudiantes']['estudiante_demo']->id,
                'sesion_ayudantia_id' => $sesiones['sesion_conta_1']->id,
            ],
            [
                'estado' => 'CANCELADO',
                'fecha_inscripcion' => Carbon::now()->subDays(3),
                'asistencia' => null,
            ]
        );
    }

    private function pivotId(string $usuarioId, string $rolId): string
    {
        $existing = DB::table('usuarios_roles')
            ->where('usuario_id', $usuarioId)
            ->where('rol_id', $rolId)
            ->value('id');

        return $existing ?: (string) Str::uuid();
    }

    private function hashNumber(string $value): int
    {
        return (int) hexdec(substr(md5($value), 0, 6));
    }
}
