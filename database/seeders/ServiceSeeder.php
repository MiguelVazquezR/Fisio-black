<?php

namespace Database\Seeders;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->services() as $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                $service,
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function services(): array
    {
        return [
            [
                'slug' => 'infiltracion-acido-hialuronico',
                'name' => 'Infiltración de Ácido Hialurónico',
                'category' => ServiceCategory::Inyectables,
                'price' => 4200,
                'duration_minutes' => 60,
                'description' => 'Infiltración intraarticular de ácido hialurónico para restaurar la viscosidad del líquido sinovial, reducir la fricción y aliviar el dolor en articulaciones desgastadas. Se realiza con material estéril y valoración previa.',
                'benefits' => [
                    'Reduce el dolor y la rigidez articular',
                    'Favorece la lubricación y el deslizamiento de la articulación',
                    'Mejora la movilidad en las actividades diarias',
                    'Aplicación ambulatoria con material estéril',
                    'Alternativa no quirúrgica para el desgaste articular',
                    'Incluye valoración previa y seguimiento',
                ],
            ],
            [
                'slug' => 'plantillas-ortopedicas-a-la-medida',
                'name' => 'Plantillas ortopédicas a la medida',
                'category' => ServiceCategory::Ortopedia,
                'price' => 600,
                'duration_minutes' => 60,
                'description' => 'Diseño y elaboración de plantillas personalizadas a partir del molde de tu pie para corregir la pisada, distribuir la carga y disminuir el dolor en pies, rodillas, cadera o espalda.',
                'benefits' => [
                    'Corrige alteraciones de la pisada y la postura',
                    'Distribuye mejor la carga en pies y rodillas',
                    'Disminuye dolor en pies, rodillas, cadera o espalda',
                    'Diseño personalizado a partir del molde de tu pie',
                ],
            ],
            [
                'slug' => 'infiltracion-synvisc-one',
                'name' => 'Infiltración con Synvisc One 2 ml',
                'category' => ServiceCategory::Inyectables,
                'price' => 3500,
                'duration_minutes' => 45,
                'description' => 'Infiltración de Synvisc One (2 ml) en una sola aplicación para tratar el dolor y la rigidez articular. Procedimiento ambulatorio realizado por personal capacitado.',
                'benefits' => [
                    'Alivio prolongado del dolor articular',
                    'Mejora la rigidez y la movilidad',
                    'Aplicación única en consultorio',
                    'Procedimiento ambulatorio con material estéril',
                ],
            ],
            [
                'slug' => 'disfuncion-erectil-ondas-de-choque',
                'name' => 'Disfunción eréctil vascular (ondas de choque)',
                'category' => ServiceCategory::Especializados,
                'price' => 800,
                'duration_minutes' => 45,
                'description' => 'Tratamiento con ondas de choque de baja intensidad para mejorar la circulación y la respuesta vascular en disfunción eréctil de origen vascular. Incluye valoración previa.',
                'benefits' => [
                    'Mejora el flujo sanguíneo en la zona',
                    'Alternativa no invasiva y sin medicamentos',
                    'Sesiones breves en consultorio',
                    'Incluye valoración previa',
                ],
            ],
            [
                'slug' => 'infiltracion-suplasyn-rodilla',
                'name' => 'Infiltración en rodilla con Suplasyn',
                'category' => ServiceCategory::Inyectables,
                'price' => 2000,
                'duration_minutes' => 45,
                'description' => 'Aplicación intraarticular de Suplasyn para lubricar y proteger la rodilla, indicada en desgaste articular y dolor persistente. Se realiza con material estéril y control posterior.',
                'benefits' => [
                    'Lubrica y protege la articulación',
                    'Reduce el dolor y la rigidez de rodilla',
                    'Apoya la recuperación del cartílago',
                    'Aplicación ambulatoria con control posterior',
                ],
            ],
            [
                'slug' => 'rehabilitacion-fisica',
                'name' => 'Rehabilitación física (1 hora)',
                'category' => ServiceCategory::Rehabilitacion,
                'price' => null,
                'duration_minutes' => 60,
                'description' => 'Sesión de una hora con valoración funcional y plan de ejercicio terapéutico para recuperar movilidad, fuerza y control del dolor después de una lesión, cirugía o sobrecarga.',
                'benefits' => [
                    'Valoración funcional individual',
                    'Ejercicio terapéutico progresivo',
                    'Recuperación de fuerza y movilidad',
                    'Trabajo específico de control del dolor',
                ],
            ],
            [
                'slug' => 'terapia-fisica-a-domicilio',
                'name' => 'Terapia física a domicilio',
                'category' => ServiceCategory::Rehabilitacion,
                'price' => null,
                'duration_minutes' => 60,
                'description' => 'Atención de fisioterapia en tu casa u hotel dentro de la zona de cobertura, de 08:00 a 14:00. Ideal para pacientes con movilidad limitada o en recuperación postoperatoria.',
                'benefits' => [
                    'Atención uno a uno en tu domicilio',
                    'Ideal para postoperatorios y movilidad limitada',
                    'Horario de 08:00 a 14:00',
                    'Cobertura en Zapopan y zonas cercanas',
                ],
            ],
            [
                'slug' => 'paralisis-facial',
                'name' => 'Tratamiento para parálisis facial',
                'category' => ServiceCategory::Especializados,
                'price' => null,
                'duration_minutes' => 60,
                'description' => 'Tratamiento especializado para parálisis facial: reeducación muscular, movilidad, drenaje y ejercicios frente al espejo, con seguimiento en cada sesión.',
                'benefits' => [
                    'Reeducación de la musculatura facial',
                    'Mejora de la simetría y la expresión',
                    'Ejercicios guiados frente al espejo',
                    'Seguimiento sesión a sesión',
                ],
            ],
            [
                'slug' => 'masaje-terapeutico',
                'name' => 'Masaje de descarga y descontracturante',
                'category' => ServiceCategory::Rehabilitacion,
                'price' => null,
                'duration_minutes' => 60,
                'description' => 'Masaje profundo de descarga y descontracturación para aliviar la tensión muscular, mejorar la movilidad y acelerar la recuperación después del entrenamiento o de una jornada laboral.',
                'benefits' => [
                    'Libera tensión y contracturas',
                    'Mejora la movilidad y la postura',
                    'Acelera la recuperación deportiva',
                    'Alivio inmediato después de la sesión',
                ],
            ],
            [
                'slug' => 'hemovacuna',
                'name' => 'Hemovacuna',
                'category' => ServiceCategory::Inyectables,
                'price' => 150,
                'duration_minutes' => 30,
                'description' => 'Aplicación de hemovacuna: ozonoterapia y componentes biológicos que buscan estimular la regeneración de tejidos y mejorar la respuesta inflamatoria.',
                'benefits' => [
                    'Estimula la regeneración de tejidos',
                    'Apoya la respuesta antiinflamatoria',
                    'Mejora la circulación local',
                    'Complemento en procesos de recuperación',
                ],
            ],
            [
                'slug' => 'terapia-neural',
                'name' => 'Terapia neural',
                'category' => ServiceCategory::Inyectables,
                'price' => 300,
                'duration_minutes' => 30,
                'description' => 'Terapia neural con procainización de puntos de conflicto para modular el dolor y la disfunción del sistema nervioso. Incluye valoración y seguimiento.',
                'benefits' => [
                    'Modula el dolor de origen neuropático',
                    'Busca restablecer la función del sistema nervioso',
                    'Incluye valoración y seguimiento',
                    'Técnica aplicada por personal capacitado',
                ],
            ],
            [
                'slug' => 'puncion-seca',
                'name' => 'Punción seca',
                'category' => ServiceCategory::Rehabilitacion,
                'price' => 250,
                'duration_minutes' => 30,
                'description' => 'Punción seca sobre puntos gatillo para liberar contracturas, mejorar el rango de movimiento y disminuir el dolor muscular de origen miofascial.',
                'benefits' => [
                    'Libera puntos gatillo y contracturas',
                    'Mejora el rango de movimiento',
                    'Disminuye el dolor miofascial',
                    'Se combina con terapia manual',
                ],
            ],
            [
                'slug' => 'kinesiology-taping',
                'name' => 'Kinesiology taping',
                'category' => ServiceCategory::Especializados,
                'price' => 100,
                'duration_minutes' => 30,
                'description' => 'Aplicación de vendaje neuromuscular (kinesiology taping) para dar soporte, mejorar la propiocepción y acompañar la recuperación sin limitar el movimiento.',
                'benefits' => [
                    'Soporte y estabilidad sin limitar el movimiento',
                    'Mejora la propiocepción',
                    'Reduce la carga sobre la zona lesionada',
                    'Acompaña la recuperación deportiva',
                ],
            ],
        ];
    }
}
